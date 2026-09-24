<?php

namespace Tests\Feature;

use App\Exceptions\ApiException;
use App\Models\Platform;
use App\Models\PlatformApiKey;
use App\Models\PlatformIntegration;
use App\Models\PlatformServiceAccess;
use App\Models\Service;
use App\Services\PasetoTokenService;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use ParagonIE\Paseto\Builder;
use ParagonIE\Paseto\Keys\Version4\SymmetricKey;
use ParagonIE\Paseto\Protocol\Version4;
use Tests\TestCase;

/**
 * End-to-end token lifecycle per docs/phase-3a-api-integration-plan.md §6/§9/§11.
 *
 * Platform isolation: every assertion derives context from the verified token —
 * never from client-supplied platform/user IDs.
 */
class AuthTokenLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private PasetoTokenService $tokens;

    private Platform $platform;

    private PlatformIntegration $integration;

    private string $secret;

    private SymmetricKey $secretKey;

    private string $bearer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tokens = $this->app->make(PasetoTokenService::class);
        [$this->platform, $this->integration, $this->secret, $this->secretKey] = $this->provisionedIntegration();
        $this->bearer = $this->issueBearer();
    }

    /**
     * Provision platform + integration + known primary key + live chat entitlement.
     *
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

    private function issueBearer(?string $secret = null, array $payload = []): string
    {
        $response = $this->postJson('/api/v1/auth/token', array_merge([
            'client_id' => $this->platform->public_id,
            'client_secret' => $secret ?? $this->secret,
        ], $payload));

        $response->assertOk();

        return (string) $response->json('data.access_token');
    }

    public function test_issue_returns_short_lived_v4_local_token(): void
    {
        $response = $this->postJson('/api/v1/auth/token', [
            'client_id' => $this->platform->public_id,
            'client_secret' => $this->secret,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.scope.0', 'authentication');

        $accessToken = $response->json('data.access_token');
        $this->assertStringStartsWith('v4.local.', $accessToken);
        $this->assertLessThanOrEqual(3600, (int) $response->json('data.expires_in'));
        $this->assertNotEmpty((string) $response->json('data.jti'));
    }

    public function test_issue_rejects_unknown_client(): void
    {
        $this->postJson('/api/v1/auth/token', [
            'client_id' => '01ABCDEFGHIJKLMNOPQRSTUVWX',
            'client_secret' => $this->secret,
        ])
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'INVALID_CLIENT')
            ->assertJsonPath('success', false);
    }

    public function test_issue_rejects_wrong_client_secret(): void
    {
        $this->postJson('/api/v1/auth/token', [
            'client_id' => $this->platform->public_id,
            'client_secret' => str_repeat('A', 43),
        ])
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'INVALID_CLIENT');
    }

    public function test_issue_missing_credentials_returns_validation_envelope(): void
    {
        $this->postJson('/api/v1/auth/token', [])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_FAILED')
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['error' => ['errors' => ['client_id', 'client_secret']]]);
    }

    public function test_issue_for_suspended_integration_is_rejected(): void
    {
        $platform = Platform::factory()->active()->create();
        $integration = PlatformIntegration::factory()->forPlatform($platform)->suspended()->create();
        $key = new SymmetricKey(random_bytes(32));
        PlatformApiKey::factory()
            ->forIntegration($integration)
            ->withRawSecret($key->encode())
            ->create();

        $this->postJson('/api/v1/auth/token', [
            'client_id' => $platform->public_id,
            'client_secret' => $key->encode(),
        ])
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'PLATFORM_SUSPENDED');
    }

    public function test_issue_grants_scopes_from_live_entitlements(): void
    {
        $this->postJson('/api/v1/auth/token', [
            'client_id' => $this->platform->public_id,
            'client_secret' => $this->secret,
        ])
            ->assertOk()
            ->assertJsonPath('data.scope', ['authentication', 'realtime_chat:read', 'realtime_chat:write']);
    }

    public function test_validate_accepts_authentic_token_and_returns_platform_me(): void
    {
        $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer '.$this->bearer])
            ->assertOk()
            ->assertJsonPath('data.platform.public_id', $this->platform->public_id)
            ->assertJsonPath('data.platform.slug', $this->platform->slug)
            ->assertJsonPath('data.integration.paseto_version', 'v4.local')
            ->assertJsonPath('data.entitlements.0.service_key', 'realtime_chat')
            ->assertJsonPath('data.scopes.0', 'authentication')
            ->assertJsonPath('data.scopes.1', 'realtime_chat:read')
            ->assertJsonPath('data.scopes.2', 'realtime_chat:write');
    }

    public function test_validate_rejects_missing_bearer(): void
    {
        $this->getJson('/api/v1/platform/me')
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'INVALID_TOKEN');
    }

    public function test_validate_rejects_expired_token(): void
    {
        $issued = $this->tokens->issue(
            $this->integration,
            $this->integration->primaryApiKey(),
            ['authentication', 'realtime_chat:read'],
            new DateTimeImmutable('-2 hours'),
        );

        $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer '.$issued->token])
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'TOKEN_EXPIRED');
    }

    public function test_validate_rejects_tampered_token(): void
    {
        $tampered = substr($this->bearer, 0, -1).(($last = substr($this->bearer, -1)) === 'A' ? 'B' : 'A');

        $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer '.$tampered])
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'INVALID_TOKEN');
    }

    public function test_validate_rejects_token_signed_with_different_key(): void
    {
        // A second real key for the same integration; the token is signed with it
        // but the footer `kid` points at the first (provisioned) key — so the
        // library must reject it cryptographically (key mismatch), never fall back.
        $otherKey = new SymmetricKey(random_bytes(32));
        $wrongKeyToken = Builder::getLocal($otherKey, new Version4)
            ->setAudience('platform:'.$this->platform->slug)
            ->setSubject($this->platform->public_id)
            ->setJti((string) Str::uuid())
            ->setIssuedAt(new DateTimeImmutable)
            ->setExpiration((new DateTimeImmutable)->modify('+1 hour'))
            ->set('platform_id', $this->platform->public_id)
            ->set('scope', ['authentication'])
            ->setFooterArray(['kid' => PlatformApiKey::fingerprint($this->secret)])
            ->toString();

        $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer '.$wrongKeyToken])
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'INVALID_TOKEN');
    }

    public function test_validate_rejects_token_with_mismatched_platform_claim(): void
    {
        // Re-mint a token for the same key but with a foreign platform's slug/ULID.
        $foreignPlatform = Platform::factory()->active()->create();

        $forged = Builder::getLocal($this->secretKey, new Version4)
            ->setAudience('platform:'.$foreignPlatform->slug)
            ->setSubject($foreignPlatform->public_id)
            ->setJti((string) Str::uuid())
            ->setIssuedAt(new DateTimeImmutable)
            ->setExpiration((new DateTimeImmutable)->modify('+1 hour'))
            ->set('platform_id', $foreignPlatform->public_id)
            ->setFooterArray(['kid' => PlatformApiKey::fingerprint($this->secret)])
            ->toString();

        try {
            $this->tokens->validate($forged);
            $this->fail('Expected PLATFORM_MISMATCH.');
        } catch (ApiException $ex) {
            $this->assertSame('PLATFORM_MISMATCH', $ex->errorCode());
        }
    }

    public function test_validate_rejects_revoked_token_in_claim(): void
    {
        $validated = $this->tokens->validate($this->bearer);
        Cache::put('paseto.jti.revoked.'.$validated->jti, true, 3600);

        $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer '.$this->bearer])
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'TOKEN_REVOKED');
    }

    public function test_revoke_endpoint_blacklists_jti(): void
    {
        $this->postJson('/api/v1/auth/revoke', [], ['Authorization' => 'Bearer '.$this->bearer])
            ->assertOk()
            ->assertJsonPath('data.revoked', true);

        $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer '.$this->bearer])
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'TOKEN_REVOKED');
    }

    public function test_validate_rejects_key_revoked_in_db(): void
    {
        $key = $this->integration->primaryApiKey();
        $key->update(['revoked_at' => now()]);

        $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer '.$this->bearer])
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'TOKEN_REVOKED');
    }

    public function test_scopes_are_bound_to_token_not_request_product(): void
    {
        $response = $this->postJson('/api/v1/auth/token', [
            'client_id' => $this->platform->public_id,
            'client_secret' => $this->secret,
            'scope' => ['realtime_chat:write'],
        ]);

        $response->assertOk();
        $this->assertSame(['realtime_chat:write'], $response->json('data.scope'));
    }

    public function test_requested_ungranted_scope_is_rejected(): void
    {
        $this->postJson('/api/v1/auth/token', [
            'client_id' => $this->platform->public_id,
            'client_secret' => $this->secret,
            'scope' => ['ai_agent:read'],
        ])
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'SERVICE_NO_ACCESS');
    }

    public function test_request_id_present_in_error_envelope(): void
    {
        $response = $this->postJson('/api/v1/auth/token', []);
        $response->assertStatus(422);

        $requestId = $response->json('error.request_id');
        $this->assertTrue((bool) preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/',
            (string) $requestId
        ));
    }
}
