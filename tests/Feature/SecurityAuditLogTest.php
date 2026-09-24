<?php

namespace Tests\Feature;

use App\Models\ApiAuditLog;
use App\Models\Platform;
use App\Models\PlatformApiKey;
use App\Models\PlatformIntegration;
use App\Models\PlatformServiceAccess;
use App\Models\Service;
use App\Services\ApiAuditService;
use App\Services\ApiKeyService;
use App\Services\PasetoTokenService;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use ParagonIE\Paseto\Builder;
use ParagonIE\Paseto\Keys\Version4\SymmetricKey;
use ParagonIE\Paseto\Protocol\Version4;
use Tests\TestCase;

/**
 * Security/integration audit trail (phase-3a-api-integration-plan.md §13;
 * security.md). Every event is written append-only, platform-scoped where the
 * platform could be resolved, and never carries secrets/PII.
 */
class SecurityAuditLogTest extends TestCase
{
    use RefreshDatabase;

    private Platform $platform;

    private PlatformIntegration $integration;

    private string $secret;

    private SymmetricKey $secretKey;

    private string $bearer;

    protected function setUp(): void
    {
        parent::setUp();

        [$this->platform, $this->integration, $this->secret, $this->secretKey] = $this->provisionedIntegration();
        $this->bearer = $this->issueBearer();
    }

    public function test_token_issued_is_audited(): void
    {
        $this->postJson('/api/v1/auth/token', [
            'client_id' => $this->platform->public_id,
            'client_secret' => $this->secret,
        ])->assertOk();

        $audit = $this->latestEvent(ApiAuditLog::EVENT_TOKEN_ISSUED);
        $this->assertNotNull($audit);
        $this->assertSame($this->platform->id, $audit->platform_id);
        $this->assertSame($this->integration->primaryApiKey()->id, $audit->api_key_id);
        $this->assertArrayHasKey('jti', $audit->metadata ?? []);
    }

    public function test_token_rejected_is_audited_with_platform_context(): void
    {
        $this->postJson('/api/v1/auth/token', [
            'client_id' => $this->platform->public_id,
            'client_secret' => str_repeat('A', 43),
        ])->assertStatus(401);

        $audit = $this->latestEvent(ApiAuditLog::EVENT_TOKEN_REJECTED);
        $this->assertNotNull($audit);
        $this->assertSame($this->platform->id, $audit->platform_id);
        $this->assertSame('INVALID_CLIENT', $audit->metadata['reason'] ?? null);
    }

    public function test_token_rejected_for_unknown_client_is_audited_without_platform(): void
    {
        $this->postJson('/api/v1/auth/token', [
            'client_id' => '01ABCDEFGHIJKLMNOPQRSTUVWX',
            'client_secret' => $this->secret,
        ])->assertStatus(401);

        $audit = $this->latestEvent(ApiAuditLog::EVENT_TOKEN_REJECTED);
        $this->assertNotNull($audit);
        $this->assertNull($audit->platform_id);
        $this->assertSame('INVALID_CLIENT', $audit->metadata['reason'] ?? null);
    }

    public function test_expired_token_is_audited_platform_scoped(): void
    {
        $expired = $this->tokens()->issue(
            $this->integration,
            $this->integration->primaryApiKey(),
            ['authentication', 'realtime_chat:read'],
            new DateTimeImmutable('-2 hours'),
        );

        $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer '.$expired->token])
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'TOKEN_EXPIRED');

        $audit = $this->latestEvent(ApiAuditLog::EVENT_TOKEN_EXPIRED);
        $this->assertNotNull($audit);
        $this->assertSame($this->platform->id, $audit->platform_id);
        $this->assertSame(401, $audit->status_code);
    }

    public function test_invalid_token_is_audited_without_platform(): void
    {
        $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer not-a-paseto-token'])
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'INVALID_TOKEN');

        $audit = $this->latestEvent(ApiAuditLog::EVENT_INVALID_TOKEN);
        $this->assertNotNull($audit);
        $this->assertNull($audit->platform_id);
    }

    public function test_wrong_platform_is_audited_against_resolved_key_platform(): void
    {
        $foreign = Platform::factory()->active()->create();

        $forged = Builder::getLocal($this->secretKey, new Version4)
            ->setAudience('platform:'.$foreign->slug)
            ->setSubject($foreign->public_id)
            ->setJti((string) Str::uuid())
            ->setIssuedAt(new DateTimeImmutable)
            ->setExpiration((new DateTimeImmutable)->modify('+1 hour'))
            ->set('platform_id', $foreign->public_id)
            ->setFooterArray(['kid' => PlatformApiKey::fingerprint($this->secret)])
            ->toString();

        $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer '.$forged])
            ->assertJsonPath('error.code', 'PLATFORM_MISMATCH');

        $audit = $this->latestEvent(ApiAuditLog::EVENT_WRONG_PLATFORM);
        $this->assertNotNull($audit);
        // Scoped to the KEY's platform (verified source), never the forged claims.
        $this->assertSame($this->platform->id, $audit->platform_id);
    }

    public function test_insufficient_scope_is_audited(): void
    {
        $limited = $this->issueBearer([
            'scope' => ['authentication'],
        ]);

        // realtime_chat:read is required but missing → 403 + audit.
        $this->getJson('/api/v1/users/4582', ['Authorization' => 'Bearer '.$limited])
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'SERVICE_NO_ACCESS');

        $audit = $this->latestEvent(ApiAuditLog::EVENT_INSUFFICIENT_SCOPE);
        $this->assertNotNull($audit);
        $this->assertSame($this->platform->id, $audit->platform_id);
        $this->assertSame('realtime_chat:read', $audit->metadata['scope'] ?? null);
        $this->assertSame(403, $audit->status_code);
    }

    public function test_key_rotation_is_audited(): void
    {
        $service = $this->app->make(ApiKeyService::class);
        [$newKey] = $service->rotate($this->integration);

        $audit = $this->latestEvent(ApiAuditLog::EVENT_KEY_ROTATION);
        $this->assertNotNull($audit);
        $this->assertSame($this->platform->id, $audit->platform_id);
        $this->assertSame($newKey->id, $audit->api_key_id);
        $this->assertSame($newKey->key_fingerprint, $audit->metadata['current_kid'] ?? null);
        $this->assertArrayNotHasKey('secret', $audit->metadata ?? []);
    }

    public function test_every_authenticated_call_writes_request_row_with_checksums_only(): void
    {
        $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer '.$this->bearer])->assertOk();

        $audit = $this->latestEvent(ApiAuditLog::EVENT_REQUEST);
        $this->assertNotNull($audit);
        $this->assertSame($this->platform->id, $audit->platform_id);
        $this->assertSame('api/v1/platform/me', $audit->endpoint);
        $this->assertSame('GET', $audit->method);
        $this->assertSame(200, $audit->status_code);
        $this->assertNotNull($audit->ip_hash);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', (string) $audit->request_checksum);
        $this->assertGreaterThanOrEqual(0, $audit->duration_ms);
    }

    public function test_audit_rows_are_platform_scoped(): void
    {
        $platformB = Platform::factory()->active()->create();

        $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer '.$this->bearer])->assertOk();

        $this->assertDatabaseHas('api_audit_logs', ['platform_id' => $this->platform->id, 'event' => ApiAuditLog::EVENT_REQUEST]);
        $this->assertDatabaseMissing('api_audit_logs', [
            'platform_id' => $platformB->id,
            'event' => ApiAuditLog::EVENT_REQUEST,
        ]);
    }

    public function test_audit_rows_are_immutable_append_only(): void
    {
        $before = ApiAuditLog::query()->count();

        ApiAuditLog::factory()->forPlatform($this->platform)->create();

        $this->assertSame($before + 1, ApiAuditLog::query()->count());

        // No updated_at column exists; rows are hard-lifecycle (no soft delete).
        $row = ApiAuditLog::query()->latest('id')->first();
        $this->assertNull($row->getAttributes()['updated_at'] ?? null);
    }

    public function test_audit_service_fails_open_when_write_errors(): void
    {
        $service = $this->app->make(ApiAuditService::class);

        try {
            // Negative duration violates the unsigned column on MySQL (strict) and
            // is coerced on SQLite — in both cases record() must never throw. A
            // legit write error (not DDL) so the table survives on MySQL.
            $service->tokenIssued($this->platform, $this->integration->primaryApiKey(), [
                'jti' => 'x',
                'duration_ms' => -1,
            ]);

            $this->assertTrue(true);
        } catch (\Throwable $ex) {
            $this->fail('Audit failure must not propagate: '.$ex->getMessage());
        }
    }

    /**
     * @return array{0: Platform, 1: PlatformIntegration, 2: string, 3: SymmetricKey}
     */
    private function provisionedIntegration(): array
    {
        $platform = Platform::factory()->active()->create();

        $integration = PlatformIntegration::factory()
            ->forPlatform($platform)
            ->active()
            ->create();

        $key = new SymmetricKey(random_bytes(32));

        PlatformApiKey::factory()
            ->forIntegration($integration)
            ->withRawSecret($key->encode())
            ->create();

        $service = Service::factory()->active()->create(['key' => 'realtime_chat', 'name' => 'Real-Time Chat']);

        PlatformServiceAccess::factory()->granted()->create([
            'platform_id' => $platform->id,
            'service_id' => $service->id,
        ]);

        return [$platform, $integration, $key->encode(), $key];
    }

    private function issueBearer(array $payload = []): string
    {
        $response = $this->postJson('/api/v1/auth/token', array_merge([
            'client_id' => $this->platform->public_id,
            'client_secret' => $this->secret,
        ], $payload));

        $response->assertOk();

        return (string) $response->json('data.access_token');
    }

    private function latestEvent(string $event): ?ApiAuditLog
    {
        return ApiAuditLog::query()
            ->where('event', $event)
            ->latest('id')
            ->first();
    }

    private function tokens(): PasetoTokenService
    {
        return $this->app->make(PasetoTokenService::class);
    }
}
