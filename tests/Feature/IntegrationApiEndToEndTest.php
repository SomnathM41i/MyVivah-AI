<?php

namespace Tests\Feature;

use App\Models\ApiAuditLog;
use App\Models\Platform;
use App\Models\PlatformApiKey;
use App\Models\PlatformIntegration;
use App\Services\PlatformOnboardingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 3C end-to-end: the platform-facing integration API surface.
 *
 * Exercises the full journey a real integrator performs against /api/v1:
 * onboarding → credential exchange → integration config → key lifecycle →
 * external-user identity → scope/entitlement gates → per-platform rate limits
 * → standardized envelope across every status (200/401/403/404/405/422/429).
 *
 * Platform isolation is asserted throughout: one platform can never read,
 * mutate, or revoke another's data, and can never consume another's quota.
 */
class IntegrationApiEndToEndTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_issued_from_onboarded_secret_and_issue_throttle_is_per_client(): void
    {
        [$platformA, , $secretA] = $this->provision('plat-a');
        [$platformB, , $secretB] = $this->provision('plat-b');

        // exhaust platform A's credential-exchange budget (issue_throttle = 5/min)
        foreach (range(1, 4) as $ignored) {
            $this->postJson('/api/v1/auth/token', [
                'client_id' => $platformA->public_id,
                'client_secret' => $secretA,
            ])->assertOk();
        }

        $this->postJson('/api/v1/auth/token', [
            'client_id' => $platformA->public_id,
            'client_secret' => $secretA,
        ])->assertStatus(429)
            ->assertJsonPath('error.code', 'RATE_LIMITED')
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['error' => ['request_id', 'retry_after_seconds']]);

        // B on the same IP is NOT throttled — per-client keying.
        $this->postJson('/api/v1/auth/token', [
            'client_id' => $platformB->public_id,
            'client_secret' => $secretB,
        ])->assertOk();
    }

    public function test_config_read_uses_standard_envelope(): void
    {
        [$platform, $integration, , $bearer] = $this->provision();

        $this->getJson('/api/v1/integration/config', ['Authorization' => 'Bearer '.$bearer])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.platform.public_id', $platform->public_id)
            ->assertJsonPath('data.platform.slug', $platform->slug)
            ->assertJsonPath('data.integration.public_id', $integration->public_id)
            ->assertJsonPath('data.integration.paseto_version', 'v4.local')
            ->assertJsonPath('data.integration.status', 'active')
            ->assertJsonPath('data.integration.rate_limit_per_minute', 60)
            ->assertJsonPath('data.integration.token_ttl_seconds', 3600)
            ->assertJsonStructure(['meta' => ['request_id']]);
    }

    public function test_config_update_persists_and_round_trips(): void
    {
        [, , , $bearer] = $this->provision();

        $this->patchJson('/api/v1/integration/config', [
            'base_domain' => 'https://portal.matrimony.example',
            'allowed_origins' => ['https://app.matrimony.example', 'https://admin.matrimony.example'],
        ], ['Authorization' => 'Bearer '.$bearer])
            ->assertOk()
            ->assertJsonPath('data.integration.base_domain', 'https://portal.matrimony.example')
            ->assertJsonPath('data.integration.allowed_origins.0', 'https://app.matrimony.example')
            ->assertJsonCount(2, 'data.integration.allowed_origins')
            ->assertJsonPath('meta.updated', true);

        $this->getJson('/api/v1/integration/config', ['Authorization' => 'Bearer '.$bearer])
            ->assertOk()
            ->assertJsonPath('data.integration.base_domain', 'https://portal.matrimony.example')
            ->assertJsonCount(2, 'data.integration.allowed_origins');
    }

    public function test_config_update_rejects_invalid_values_with_envelope(): void
    {
        [$platform, , , $bearer] = $this->provision();

        $this->patchJson('/api/v1/integration/config', [
            'base_domain' => 'not-a-url',
            'allowed_origins' => ['also-not-a-url'],
        ], ['Authorization' => 'Bearer '.$bearer])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_FAILED')
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['base_domain', 'allowed_origins.0'], 'error.errors');

        // nothing changed server-side
        $this->getJson('/api/v1/integration/config', ['Authorization' => 'Bearer '.$bearer])
            ->assertOk()
            ->assertJsonPath('data.integration.base_domain', null);
    }

    public function test_keys_list_exposes_metadata_only_never_the_secret(): void
    {
        [$platform, , $secret, $bearer] = $this->provision();

        $response = $this->getJson('/api/v1/integration/keys', ['Authorization' => 'Bearer '.$bearer]);
        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data.keys')
            ->assertJsonPath('data.keys.0.is_primary', true)
            ->assertJsonPath('data.keys.0.status', 'active')
            ->assertJsonPath('data.keys.0.kid', PlatformApiKey::fingerprint($secret))
            ->assertJsonStructure(['data' => ['keys' => [['kid', 'status', 'is_primary', 'created_at']]]]);

        $payload = (string) json_encode($response->json());
        $this->assertStringNotContainsString($secret, $payload);
        $this->assertStringNotContainsString('key_encrypted', $payload);
    }

    public function test_rotate_mints_new_primary_and_returns_one_time_secret(): void
    {
        [$platform, , $oldSecret, $bearer] = $this->provision();
        $oldKid = PlatformApiKey::fingerprint($oldSecret);

        $rotated = $this->postJson('/api/v1/integration/keys/rotate', [], ['Authorization' => 'Bearer '.$bearer]);

        $rotated->assertOk()
            ->assertJsonPath('data.rotated', true)
            ->assertJsonPath('data.client_secret_visible_once', true);

        $newSecret = (string) $rotated->json('data.client_secret');
        $this->assertNotSame($oldSecret, $newSecret);
        $this->assertSame(PlatformApiKey::fingerprint($newSecret), $rotated->json('data.current_kid'));

        // new secret issues tokens; old secret still works inside the grace window
        $newBearer = $this->issueBearer($platform, $newSecret);
        $this->assertNotNull($newBearer);
        $this->assertNotNull($this->issueBearer($platform, $oldSecret));

        $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer '.$newBearer])->assertOk();

        $keys = $this->getJson('/api/v1/integration/keys', ['Authorization' => 'Bearer '.$newBearer])
            ->assertOk()
            ->json('data.keys');
        $this->assertSame($oldKid, collect($keys)->firstWhere('status', 'rotated')['kid']);
        $this->assertSame(PlatformApiKey::fingerprint($newSecret), collect($keys)->firstWhere('is_primary', true)['kid']);

        // the rotation is audited (kids only)
        $this->assertNotNull(ApiAuditLog::query()->where('event', ApiAuditLog::EVENT_KEY_ROTATION)->latest('id')->first());
    }

    public function test_rotated_secret_is_hard_rejected_after_grace_window(): void
    {
        config(['paseto.rotation_grace_seconds' => 0]);
        [$platform, , $oldSecret, $bearer] = $this->provision();

        $this->postJson('/api/v1/integration/keys/rotate', [], ['Authorization' => 'Bearer '.$bearer])->assertOk();

        $this->postJson('/api/v1/auth/token', [
            'client_id' => $platform->public_id,
            'client_secret' => $oldSecret,
        ])->assertStatus(401)
            ->assertJsonPath('error.code', 'TOKEN_REVOKED');
    }

    public function test_revoke_single_key_is_immediate_and_audited(): void
    {
        [$platform, $integration, $secret, $bearer] = $this->provision();
        $kid = PlatformApiKey::fingerprint($secret);

        $this->postJson('/api/v1/integration/keys/revoke', ['key_fingerprint' => $kid], ['Authorization' => 'Bearer '.$bearer])
            ->assertOk()
            ->assertJsonPath('data.revoked', true)
            ->assertJsonPath('data.key_fingerprint', $kid);

        $this->assertSame('revoked', $integration->apiKeys()->firstOrFail()->status);
        $this->assertNotNull(ApiAuditLog::query()->where('event', ApiAuditLog::EVENT_KEY_ROTATION)->latest('id')->first());

        // tokens signed by the revoked key are rejected
        $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer '.$bearer])
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'TOKEN_REVOKED');
    }

    public function test_revoke_unknown_or_foreign_key_is_404(): void
    {
        [$platformA, $integrationA, $secretA, $bearerA] = $this->provision('plat-a');
        [, , , $bearerB] = $this->provision('plat-b');

        $this->postJson('/api/v1/integration/keys/revoke', ['key_fingerprint' => str_repeat('0', 40)], ['Authorization' => 'Bearer '.$bearerA])
            ->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND')
            ->assertJsonPath('success', false);

        // B cannot revoke A's key — strict isolation
        $this->postJson('/api/v1/integration/keys/revoke', ['key_fingerprint' => PlatformApiKey::fingerprint($secretA)], ['Authorization' => 'Bearer '.$bearerB])
            ->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');

        $this->assertSame('active', $integrationA->apiKeys()->firstOrFail()->status);
    }

    public function test_verify_and_show_work_and_show_touches_last_seen(): void
    {
        [$platform, , , $bearer] = $this->provision();

        $this->postJson('/api/v1/users/verify', ['external_user_id' => 'ext-1001'], ['Authorization' => 'Bearer '.$bearer])
            ->assertOk()
            ->assertJsonPath('data.external_user_id', 'ext-1001')
            ->assertJsonPath('data.platform.public_id', $platform->public_id)
            ->assertJsonPath('meta.created', true);

        $this->getJson('/api/v1/users/ext-1001', ['Authorization' => 'Bearer '.$bearer])
            ->assertOk()
            ->assertJsonPath('data.external_user_id', 'ext-1001')
            ->assertJsonStructure(['data' => ['last_seen_at']]);

        $this->assertNotNull($platform->externalUserMaps()->firstOrFail()->last_seen_at);

        $this->getJson('/api/v1/users/ext-9999', ['Authorization' => 'Bearer '.$bearer])
            ->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND')
            ->assertJsonStructure(['error' => ['request_id']]);
    }

    public function test_reconciliation_list_is_platform_scoped_and_paginated(): void
    {
        [$platformA, , , $bearerA] = $this->provision('plat-a');
        [, , , $bearerB] = $this->provision('plat-b');

        foreach (['u1', 'u2', 'u3'] as $externalId) {
            $this->postJson('/api/v1/users/verify', ['external_user_id' => $externalId], ['Authorization' => 'Bearer '.$bearerA])->assertOk();
        }
        $this->postJson('/api/v1/users/verify', ['external_user_id' => 'b1'], ['Authorization' => 'Bearer '.$bearerB])->assertOk();

        $pageOne = $this->getJson('/api/v1/integration/users?per_page=2', ['Authorization' => 'Bearer '.$bearerA]);
        $pageOne->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.pagination.total', 3)
            ->assertJsonPath('meta.pagination.per_page', 2)
            ->assertJsonPath('meta.pagination.last_page', 2)
            ->assertJsonPath('meta.pagination.has_more_pages', true);

        $ids = collect($pageOne->json('data'))->pluck('external_user_id')->all();
        $this->assertNotEmpty($ids);
        $this->assertNotContains('b1', $ids);

        // B sees only its OWN map, never A's
        $bIds = collect($this->getJson('/api/v1/integration/users', ['Authorization' => 'Bearer '.$bearerB])->json('data'))->pluck('external_user_id')->all();
        $this->assertContains('b1', $bIds);
        foreach (['u1', 'u2', 'u3'] as $externalId) {
            $this->assertNotContains($externalId, $bIds);
        }
    }

    public function test_authentication_scope_token_cannot_access_service_scopes(): void
    {
        [$platform, , $secret] = $this->provision();
        $authOnly = $this->issueBearer($platform, $secret, ['scope' => ['authentication']]);

        $this->getJson('/api/v1/integration/users', ['Authorization' => 'Bearer '.$authOnly])
            ->assertStatus(403)
            ->assertJsonPath('error.code', 'SERVICE_NO_ACCESS')
            ->assertJsonPath('success', false);

        // integration config is authentication-scoped, so it stays reachable
        $this->getJson('/api/v1/integration/config', ['Authorization' => 'Bearer '.$authOnly])
            ->assertOk();
    }

    public function test_cross_platform_user_isolation(): void
    {
        [, , , $bearerA] = $this->provision('plat-a');
        [$platformB, , , $bearerB] = $this->provision('plat-b');

        $this->postJson('/api/v1/users/verify', ['external_user_id' => 'shared-profile'], ['Authorization' => 'Bearer '.$bearerA])->assertOk();

        // B cannot resolve A's mapping even though the path uses the same id
        $this->getJson('/api/v1/users/shared-profile', ['Authorization' => 'Bearer '.$bearerB])
            ->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');

        $this->getJson('/api/v1/integration/users', ['Authorization' => 'Bearer '.$bearerB])
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_per_platform_rate_limit_returns_429_envelope_and_isolates_peers(): void
    {
        [$platformA, $integrationA, , $bearerA] = $this->provision('plat-a');
        [, , , $bearerB] = $this->provision('plat-b');

        $integrationA->update(['rate_limit_per_minute' => 2]);

        $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer '.$bearerA])->assertOk();
        $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer '.$bearerA])->assertOk();

        $third = $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer '.$bearerA]);
        $third->assertStatus(429)
            ->assertJsonPath('error.code', 'RATE_LIMITED')
            ->assertJsonPath('success', false)
            ->assertHeader('Retry-After')
            ->assertJsonStructure(['error' => ['request_id', 'retry_after_seconds']]);

        // peer platform on the same IP is untouched
        $this->getJson('/api/v1/platform/me', ['Authorization' => 'Bearer '.$bearerB])->assertOk();

        // throttled requests are still audited
        $this->assertNotNull(ApiAuditLog::query()->where('status_code', 429)->first());
    }

    public function test_method_not_allowed_and_unknown_route_keep_envelope(): void
    {
        $this->getJson('/api/v1/auth/token')
            ->assertStatus(405)
            ->assertJsonPath('error.code', 'METHOD_NOT_ALLOWED')
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['error' => ['request_id']]);

        $this->getJson('/api/v1/does-not-exist')
            ->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND')
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['error' => ['request_id']]);
    }

    public function test_integration_endpoint_requests_are_audited(): void
    {
        [$platform, , , $bearer] = $this->provision();

        $this->getJson('/api/v1/integration/config', ['Authorization' => 'Bearer '.$bearer])->assertOk();
        $this->postJson('/api/v1/integration/keys/rotate', [], ['Authorization' => 'Bearer '.$bearer])->assertOk();

        $rows = ApiAuditLog::query()
            ->where('event', ApiAuditLog::EVENT_REQUEST)
            ->whereIn('endpoint', ['api/v1/integration/config', 'api/v1/integration/keys/rotate'])
            ->get();

        $this->assertCount(2, $rows);
        $this->assertSame($platform->id, $rows->first()->platform_id);
        $this->assertSame(200, $rows->first()->status_code);
    }

    /**
     * @return array{0: Platform, 1: PlatformIntegration, 2: string, 3: string}
     *                                                                          [platform, integration, oneTimeSecret, bearer]
     */
    private function provision(string $slug = 'matrimonyband'): array
    {
        [$platform, $integration, $secret] = $this->app
            ->make(PlatformOnboardingService::class)
            ->onboard($slug);

        $this->assertNotNull($secret);
        /** @var string $secret */

        return [
            $platform,
            $integration,
            $secret,
            $this->issueBearer($platform, $secret),
        ];
    }

    private function issueBearer(Platform $platform, string $secret, array $payload = []): string
    {
        $response = $this->postJson('/api/v1/auth/token', array_merge([
            'client_id' => $platform->public_id,
            'client_secret' => $secret,
        ], $payload));

        $response->assertOk();

        return (string) $response->json('data.access_token');
    }
}
