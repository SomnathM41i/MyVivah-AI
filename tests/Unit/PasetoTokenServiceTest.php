<?php

namespace Tests\Unit;

use App\Exceptions\ApiException;
use App\Models\Platform;
use App\Models\PlatformApiKey;
use App\Models\PlatformIntegration;
use App\Models\PlatformServiceAccess;
use App\Models\Service;
use App\Services\PasetoTokenService;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use ParagonIE\Paseto\Builder;
use ParagonIE\Paseto\Keys\Version4\SymmetricKey;
use ParagonIE\Paseto\Parser;
use ParagonIE\Paseto\Protocol\Version4;
use ParagonIE\Paseto\ProtocolCollection;
use Tests\TestCase;

/**
 * Service-level PASETO guarantees (clock-mocked) per docs/phase-3a §6.2/§6.3.
 */
class PasetoTokenServiceTest extends TestCase
{
    use RefreshDatabase;

    private PasetoTokenService $tokens;

    private Platform $platform;

    private PlatformIntegration $integration;

    private PlatformApiKey $key;

    private SymmetricKey $secret;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tokens = $this->app->make(PasetoTokenService::class);
        $this->platform = Platform::factory()->active()->create();

        $this->integration = PlatformIntegration::factory()
            ->forPlatform($this->platform)
            ->active()
            ->create();

        $this->secret = new SymmetricKey(random_bytes(32));
        $this->key = PlatformApiKey::factory()
            ->forIntegration($this->integration)
            ->withRawSecret($this->secret->encode())
            ->create();
    }

    public function test_issue_produces_v4_local_token_with_expected_claims(): void
    {
        $at = new DateTimeImmutable('2026-01-01T00:00:00+00:00');
        $issued = $this->tokens->issue($this->integration, $this->key, ['authentication'], $at);

        [$footer, $payload] = $this->unpack($issued->token);

        $this->assertTrue(str_starts_with($issued->token, 'v4.local.'));
        $this->assertSame($this->key->key_fingerprint, $footer['kid']);
        $this->assertSame('platform:'.$this->platform->slug, $payload['aud']);
        $this->assertSame($this->platform->public_id, $payload['sub']);
        $this->assertSame($this->platform->public_id, $payload['platform_id']);
        $this->assertSame(['authentication'], $payload['scope']);
        $this->assertSame(3600, $issued->ttlSeconds);
        $this->assertSame(3600, $issued->expiresInSeconds());
    }

    public function test_validate_round_trips_a_just_issued_token(): void
    {
        $issued = $this->tokens->issue($this->integration, $this->key, ['authentication', 'realtime_chat:read']);

        $validated = $this->tokens->validate($issued->token);

        $this->assertSame($this->platform->id, $validated->platform->id);
        $this->assertSame($this->key->id, $validated->apiKey->id);
        $this->assertSame($this->platform->public_id, $validated->subject);
        $this->assertSame($issued->jti, $validated->jti);
        $this->assertSame(['authentication', 'realtime_chat:read'], $validated->scopes);
        $this->assertTrue($validated->hasScope('realtime_chat:read'));
    }

    public function test_validate_rejects_expired_token(): void
    {
        $past = new DateTimeImmutable('-2 hours');
        $issued = $this->tokens->issue($this->integration, $this->key, ['authentication'], $past);

        try {
            $this->tokens->validate($issued->token);
            $this->fail('Expected TOKEN_EXPIRED.');
        } catch (ApiException $ex) {
            $this->assertSame('TOKEN_EXPIRED', $ex->errorCode());
            $this->assertSame(401, $ex->status());
        }
    }

    public function test_validate_rejects_malformed_token(): void
    {
        try {
            $this->tokens->validate('not-a-paseto-token');
            $this->fail('Expected INVALID_TOKEN.');
        } catch (ApiException $ex) {
            $this->assertSame('INVALID_TOKEN', $ex->errorCode());
        }
    }

    public function test_validate_rejects_tampered_token(): void
    {
        $issued = $this->tokens->issue($this->integration, $this->key, ['authentication']);
        $parts = explode('.', $issued->token, 3);
        $tampered = $parts[0].'.'.$parts[1].'.'.strrev($parts[2]);

        try {
            $this->tokens->validate($tampered);
            $this->fail('Expected INVALID_TOKEN.');
        } catch (ApiException $ex) {
            $this->assertSame('INVALID_TOKEN', $ex->errorCode());
        }
    }

    public function test_validate_rejects_token_signed_by_unprovisioned_key(): void
    {
        // Token signed with a key that has no DB row yet (kid unknown).
        $foreign = new SymmetricKey(random_bytes(32));
        $foreignKey = PlatformApiKey::factory()
            ->forIntegration($this->integration)
            ->withRawSecret($foreign->encode())
            ->backup()
            ->create();
        $foreignKey->delete();

        $builder = Builder::getLocal($foreign, new Version4);
        $at = new DateTimeImmutable;
        $token = $builder
            ->setAudience('platform:'.$this->platform->slug)
            ->setSubject($this->platform->public_id)
            ->setJti((string) Str::uuid())
            ->setIssuedAt($at)
            ->setExpiration($at->modify('+1 hour'))
            ->set('platform_id', $this->platform->public_id)
            ->setFooterArray(['kid' => PlatformApiKey::fingerprint($foreign->encode())])
            ->toString();

        try {
            $this->tokens->validate($token);
            $this->fail('Expected INVALID_TOKEN.');
        } catch (ApiException $ex) {
            $this->assertSame('INVALID_TOKEN', $ex->errorCode());
        }
    }

    public function test_issue_limits_ttl_to_global_maximum(): void
    {
        $this->integration->update(['token_ttl_seconds' => 99999]);

        $issued = $this->tokens->issue($this->integration, $this->key, ['authentication']);

        $this->assertSame(3600, $issued->ttlSeconds);
    }

    public function test_validation_rejects_when_integration_revoked(): void
    {
        $issued = $this->tokens->issue($this->integration, $this->key, ['authentication']);
        $this->integration->update(['revoked_at' => now()]);

        try {
            $this->tokens->validate($issued->token);
            $this->fail('Expected TOKEN_REVOKED.');
        } catch (ApiException $ex) {
            $this->assertSame('TOKEN_REVOKED', $ex->errorCode());
        }
    }

    public function test_validation_rejects_when_platform_suspended(): void
    {
        $issued = $this->tokens->issue($this->integration, $this->key, ['authentication']);
        $this->platform->update(['status' => Platform::STATUS_SUSPENDED]);

        try {
            $this->tokens->validate($issued->token);
            $this->fail('Expected PLATFORM_SUSPENDED.');
        } catch (ApiException $ex) {
            $this->assertSame('PLATFORM_SUSPENDED', $ex->errorCode());
        }
    }

    public function test_scopes_derive_from_live_entitlements(): void
    {
        $service = Service::factory()->active()->create(['key' => 'realtime_chat']);
        PlatformServiceAccess::factory()->granted()->create([
            'platform_id' => $this->platform->id,
            'service_id' => $service->id,
        ]);

        $scopes = $this->tokens->scopesFor($this->platform);

        $this->assertContains('authentication', $scopes);
        $this->assertContains('realtime_chat:read', $scopes);
        $this->assertContains('realtime_chat:write', $scopes);
    }

    public function test_revoke_blacklists_jti_then_validate_rejects(): void
    {
        $issued = $this->tokens->issue($this->integration, $this->key, ['authentication']);

        $this->tokens->revoke($issued->token);

        try {
            $this->tokens->validate($issued->token);
            $this->fail('Expected TOKEN_REVOKED.');
        } catch (ApiException $ex) {
            $this->assertSame('TOKEN_REVOKED', $ex->errorCode());
        }
    }

    /**
     * Parse footer + claims from a v4.local token for claim assertions.
     *
     * @return array{0: array<string, mixed>, 1: array<string, mixed>}
     */
    private function unpack(string $token): array
    {
        $this->assertTrue(str_starts_with($token, 'v4.local.'));

        $parser = Parser::getLocal(
            $this->secret,
            new ProtocolCollection(new Version4),
        );
        $decoded = $parser->parse($token, true);

        return [
            json_decode(Parser::extractFooter($token), true),
            $decoded->getClaims(),
        ];
    }
}
