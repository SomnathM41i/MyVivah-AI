<?php

namespace Tests\Unit;

use App\Exceptions\ApiException;
use App\Models\ApiAuditLog;
use App\Models\Platform;
use App\Models\PlatformApiKey;
use App\Models\PlatformIntegration;
use App\Services\ApiKeyService;
use App\Services\PasetoTokenService;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use ParagonIE\Paseto\Keys\Version4\SymmetricKey;
use ParagonIE\Paseto\Parser;
use Tests\TestCase;

/**
 * Key lifecycle (phase-3a-api-integration-plan.md §6.3):
 * rotation, grace window, hard revocation, and the never-expose-secrets contract.
 */
class ApiKeyServiceTest extends TestCase
{
    use RefreshDatabase;

    private ApiKeyService $keys;

    private PasetoTokenService $tokens;

    private Platform $platform;

    private PlatformIntegration $integration;

    private PlatformApiKey $oldKey;

    private SymmetricKey $oldSecret;

    protected function setUp(): void
    {
        parent::setUp();

        $this->keys = $this->app->make(ApiKeyService::class);
        $this->tokens = $this->app->make(PasetoTokenService::class);

        $this->platform = Platform::factory()->active()->create();
        $this->integration = PlatformIntegration::factory()
            ->forPlatform($this->platform)
            ->active()
            ->create();

        $this->oldSecret = new SymmetricKey(random_bytes(32));
        $this->oldKey = PlatformApiKey::factory()
            ->forIntegration($this->integration)
            ->withRawSecret($this->oldSecret->encode())
            ->create();
    }

    public function test_rotate_creates_new_primary_and_demotes_old_to_grace_backup(): void
    {
        [$newKey, $newSecret] = $this->keys->rotate($this->integration);

        // New key is the active primary.
        $this->assertTrue($newKey->is_primary);
        $this->assertSame(PlatformApiKey::STATUS_ACTIVE, $newKey->status);
        $this->assertSame(PlatformApiKey::PRIMARY_TOKEN, $newKey->primary_token);
        $this->assertSame($this->integration->id, (int) $newKey->platform_integration_id);
        $this->assertNotSame($this->oldSecret->encode(), $newSecret);

        // Old key was demoted to a rotated (grace) backup.
        $this->oldKey->refresh();
        $this->assertFalse($this->oldKey->is_primary);
        $this->assertSame(PlatformApiKey::STATUS_ROTATED, $this->oldKey->status);
        $this->assertNull($this->oldKey->primary_token);
        $this->assertNotNull($this->oldKey->rotated_at);

        // Only one primary exists; primaryApiKey() resolves the new key.
        $this->assertSame(1, $this->integration->apiKeys()->where('is_primary', true)->count());
        $this->assertSame($newKey->id, $this->integration->primaryApiKey()->id);

        // Grace backup still usable.
        $this->assertTrue($this->oldKey->isUsable());
    }

    public function test_rotate_returns_one_time_secret_and_never_persists_or_audits_it(): void
    {
        [$newKey, $newSecret] = $this->keys->rotate($this->integration);

        // The raw secret must never appear literally anywhere in the DB.
        foreach (PlatformApiKey::all() as $key) {
            $this->assertNotSame($newSecret, $key->key_encrypted);
            $this->assertNotSame($newSecret, $key->key_fingerprint);
        }

        // Ciphertext round-trips to the secret (proves correctness without plaintext at rest).
        $this->assertSame($newSecret, Crypt::decryptString($newKey->key_encrypted));

        // Audit metadata exposes fingerprints (kids) only — never the secret.
        $audit = ApiAuditLog::query()
            ->where('event', ApiAuditLog::EVENT_KEY_ROTATION)
            ->where('platform_id', $this->platform->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($audit);
        $metadata = $audit->metadata ?? [];
        $this->assertArrayNotHasKey('secret', $metadata);
        $this->assertSame($this->oldKey->key_fingerprint, $metadata['previous_kid'] ?? null);
        $this->assertSame($newKey->key_fingerprint, $metadata['current_kid'] ?? null);
    }

    public function test_old_key_issues_and_validates_during_grace_after_rotation(): void
    {
        $at = new DateTimeImmutable('-5 minutes');
        $beforeRotation = $this->tokens->issue($this->integration, $this->oldKey, ['authentication'], $at);

        $this->keys->rotate($this->integration);

        // The old key stays usable (and its tokens valid) within the grace window.
        $validated = $this->tokens->validate($beforeRotation->token);
        $this->assertSame($this->oldKey->id, $validated->apiKey->id);
        $this->assertTrue($this->oldKey->isUsable());
    }

    public function test_old_key_rejected_after_grace_and_hard_revoked(): void
    {
        // Simulate a rotation that happened 3 days ago (grace default = 24h).
        $this->oldKey->forceFill([
            'status' => PlatformApiKey::STATUS_ROTATED,
            'is_primary' => false,
            'primary_token' => null,
            'rotated_at' => now()->subDays(3),
        ])->save();

        $this->assertFalse($this->oldKey->isUsable());

        $issued = $this->tokens->issue($this->integration, $this->oldKey, ['authentication']);

        try {
            $this->tokens->validate($issued->token);
            $this->fail('Expected TOKEN_REVOKED for a grace-expired key.');
        } catch (ApiException $ex) {
            $this->assertSame('TOKEN_REVOKED', $ex->errorCode());
        }

        // The expiry was persisted: the key is now hard-revoked.
        $this->oldKey->refresh();
        $this->assertSame(PlatformApiKey::STATUS_REVOKED, $this->oldKey->status);
        $this->assertNotNull($this->oldKey->revoked_at);
    }

    public function test_revoked_key_is_rejected(): void
    {
        $issued = $this->tokens->issue($this->integration, $this->oldKey, ['authentication']);

        $this->oldKey->hardRevoke();
        $this->assertFalse($this->oldKey->isUsable());
        $this->assertTrue($this->oldKey->isRevoked());

        try {
            $this->tokens->validate($issued->token);
            $this->fail('Expected TOKEN_REVOKED.');
        } catch (ApiException $ex) {
            $this->assertSame('TOKEN_REVOKED', $ex->errorCode());
        }
    }

    public function test_revoke_all_renders_every_key_unusable_without_losing_history(): void
    {
        // A second key so revoke-all affects more than one row.
        PlatformApiKey::factory()->forIntegration($this->integration)->backup()->create();

        $this->keys->revokeAll($this->integration);
        $this->integration->refresh();

        foreach ($this->integration->apiKeys()->get() as $key) {
            $this->assertSame(PlatformApiKey::STATUS_REVOKED, $key->status);
            $this->assertNotNull($key->revoked_at);
            $this->assertFalse($key->isUsable());
        }

        $this->assertNull($this->integration->primaryApiKey());
    }

    public function test_issued_token_after_rotation_bears_new_kid(): void
    {
        [$newKey] = $this->keys->rotate($this->integration);

        $issued = $this->tokens->issue($this->integration, $this->integration->primaryApiKey(), ['authentication']);

        [$footer] = $this->unpackFooter($issued->token);
        $this->assertSame($newKey->key_fingerprint, $footer['kid']);
    }

    /**
     * @return array{0: array<string, mixed>}
     */
    private function unpackFooter(string $token): array
    {
        return [
            json_decode((string) Parser::extractFooter($token), true),
        ];
    }
}
