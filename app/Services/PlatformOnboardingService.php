<?php

namespace App\Services;

use App\Models\Platform;
use App\Models\PlatformApiKey;
use App\Models\PlatformIntegration;
use App\Models\PlatformServiceAccess;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * Programmatic platform onboarding (Phase 3C — no dashboard UI at MVP).
 *
 * Provisions the full integration root in one idempotent call:
 *   platform (active) → integration (active, v4.local) → first primary API key
 *   → live `platform_service_access` entitlement for the requested service.
 *
 * Idempotency contract:
 *   - Re-running on a known slug reuses the existing platform + integration and
 *     only (re)confirms the entitlement.
 *   - A one-time secret is minted ONLY when no primary key exists yet. When a
 *     primary already exists the secret is null — the operator must rotate to
 *     obtain a fresh one (secrets are never re-issued or recoverable).
 *   - The raw secret is returned exactly once and then forgotten: the DB stores
 *     only the fingerprint + encrypted ciphertext (ApiKeyService contract).
 */
class PlatformOnboardingService
{
    /** System owner used for programmatically provisioned platforms. */
    public const SYSTEM_OWNER_EMAIL = 'system@myvivah.local';

    /**
     * @return array{0: Platform, 1: PlatformIntegration, 2: ?string}
     *                                                                [platform, integration, oneTimeClientSecret]
     */
    public function onboard(
        string $slug,
        string $name = '',
        string $serviceKey = 'realtime_chat',
    ): array {
        $platform = Platform::query()->firstOrCreate(
            ['slug' => Str::slug($slug)],
            [
                'name' => $name !== '' ? $name : Str::title(Str::replace('-', ' ', Str::slug($slug))),
                'status' => Platform::STATUS_ACTIVE,
                'created_by' => $this->systemOwner()->id,
            ],
        );

        $integration = $platform->integration ?? PlatformIntegration::query()->create([
            'platform_id' => $platform->id,
            'status' => PlatformIntegration::STATUS_ACTIVE,
            'paseto_version' => PlatformIntegration::PASETO_V4_LOCAL,
            'token_ttl_seconds' => (int) config('paseto.default_ttl_seconds', 3600),
            'rate_limit_per_minute' => (int) config('api.rate_limit_per_minute', 60),
        ]);

        // Avoid a stale cached-null relation on the returned platform (the first
        // lazy access above evaluates before the integration row exists).
        $platform->setRelation('integration', $integration);

        $this->ensureEntitlement($platform, $serviceKey);

        $secret = null;
        if ($integration->primaryApiKey() === null) {
            $secret = ApiKeyService::generateSecret();
            PlatformApiKey::query()->create([
                'platform_integration_id' => $integration->id,
                'key_fingerprint' => PlatformApiKey::fingerprint($secret),
                'key_encrypted' => Crypt::encryptString($secret),
                'is_primary' => true,
                'primary_token' => PlatformApiKey::PRIMARY_TOKEN,
                'status' => PlatformApiKey::STATUS_ACTIVE,
            ]);
        }

        return [$platform, $integration, $secret];
    }

    private function systemOwner(): User
    {
        return User::query()->where('email', self::SYSTEM_OWNER_EMAIL)->first()
            ?? User::factory()->create([
                'name' => 'MyVivahAI System',
                'email' => self::SYSTEM_OWNER_EMAIL,
            ]);
    }

    private function ensureEntitlement(Platform $platform, string $serviceKey): void
    {
        $service = Service::query()->firstOrCreate(
            ['key' => $serviceKey],
            [
                'name' => Str::title(Str::replace('_', ' ', $serviceKey)),
                'description' => 'Provisioned by integration onboarding.',
                'is_active' => true,
            ],
        );

        PlatformServiceAccess::query()->updateOrCreate(
            ['platform_id' => $platform->id, 'service_id' => $service->id],
            ['has_access' => true, 'effective_until' => null, 'synced_at' => now()],
        );
    }
}
