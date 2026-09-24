<?php

namespace Tests\Feature;

use App\Models\ApiAuditLog;
use App\Models\PlatformApiKey;
use App\Models\PlatformServiceAccess;
use App\Models\Service;
use App\Services\PlatformOnboardingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Programmatic onboarding (Phase 3C) — service + console command.
 *
 * Contract tested here:
 *   - idempotent provisioning (platform/integration/entitlement/primary key),
 *   - one-time secret minted ONLY on first provisioning,
 *   - no second platform/integration ever created for the same slug,
 *   - the secret is immediately usable against the token endpoint.
 */
class IntegrationOnboardingTest extends TestCase
{
    use RefreshDatabase;

    private function onboard(): PlatformOnboardingService
    {
        return $this->app->make(PlatformOnboardingService::class);
    }

    public function test_onboard_provisions_full_integration_root_and_usable_secret(): void
    {
        [$platform, $integration, $secret] = $this->onboard()->onboard('matrimonyband');

        $this->assertSame('active', $platform->status);
        $this->assertSame($platform->fresh()->status, 'active');
        $this->assertSame($integration->fresh()->paseto_version, 'v4.local');
        $this->assertSame('active', $integration->fresh()->status);
        $this->assertNotNull($secret);

        $primary = $integration->primaryApiKey();
        $this->assertNotNull($primary);
        $this->assertSame(PlatformApiKey::fingerprint((string) $secret), $primary->key_fingerprint);

        // entitlement provisioned and live
        $service = Service::query()->where('key', 'realtime_chat')->firstOrFail();
        $access = PlatformServiceAccess::query()
            ->where('platform_id', $platform->id)
            ->where('service_id', $service->id)
            ->firstOrFail();
        $this->assertTrue($access->has_access);
        $this->assertTrue($service->is_active);

        // the one-time secret is immediately usable
        $this->postJson('/api/v1/auth/token', [
            'client_id' => $platform->public_id,
            'client_secret' => $secret,
        ])->assertOk()->assertJsonPath('success', true);
    }

    public function test_onboard_is_idempotent_and_never_reissues_secret(): void
    {
        [$platformA, $integrationA, $secret] = $this->onboard()->onboard('matrimonyband');
        [$platformB, $integrationB, $secretAgain] = $this->onboard()->onboard('matrimonyband');

        $this->assertSame($platformA->id, $platformB->id);
        $this->assertSame($integrationA->id, $integrationB->id);
        $this->assertNull($secretAgain);

        // exactly one primary key has ever been issued
        $this->assertSame(1, $integrationA->apiKeys()->count());
        $this->assertNotNull($integrationA->primaryApiKey());
    }

    public function test_onboard_creates_separate_namespaces_per_slug(): void
    {
        [$platformA] = $this->onboard()->onboard('matrimonyband');
        [$platformB] = $this->onboard()->onboard('shaadi-hub');

        $this->assertNotSame($platformA->id, $platformB->id);
        $this->assertSame(1, $platformA->integration->apiKeys()->count());
        $this->assertSame(1, $platformB->integration->apiKeys()->count());
    }

    public function test_console_command_prints_one_time_secret_once(): void
    {
        $this->artisan('integration:onboard', ['slug' => 'matrimonyband'])
            ->expectsOutputToContain('One-time client_secret')
            ->run();

        $primary = PlatformApiKey::query()->firstOrFail();
        $this->assertSame('active', $primary->status);

        // second run: no new secret, still a single key
        $this->artisan('integration:onboard', ['slug' => 'matrimonyband'])
            ->expectsOutputToContain('no new secret was issued')
            ->run();

        $this->assertSame(1, PlatformApiKey::query()->count());
    }

    public function test_onboard_audits_nothing_and_keys_are_encrypted_at_rest(): void
    {
        [$platform, $integration, $secret] = $this->onboard()->onboard('matrimonyband');

        $row = $integration->apiKeys()->firstOrFail();
        $this->assertNotSame($secret, $row->key_encrypted);
        $this->assertStringNotContainsString((string) $secret, (string) json_encode($row->toArray()));

        // onboarding itself (a provisioning operation) writes NO request audit row
        $this->assertSame(0, ApiAuditLog::query()->count());
    }
}
