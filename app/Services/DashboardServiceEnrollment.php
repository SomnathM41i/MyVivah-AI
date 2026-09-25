<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Platform;
use App\Models\PlatformIntegration;
use App\Models\PlatformServiceAccess;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DashboardServiceEnrollment
{
    public function __construct(private readonly ApiKeyService $apiKeys) {}

    /**
     * @return array{status: 'active'|'pending', api_secret: ?string, request_created?: bool}
     */
    public function enroll(Platform $platform, Plan $plan): array
    {
        return DB::transaction(function () use ($platform, $plan): array {
            $platform = Platform::query()->lockForUpdate()->findOrFail($platform->id);

            if ($platform->status !== Platform::STATUS_ACTIVE || ! $plan->is_active || ! $plan->service?->is_active) {
                throw ValidationException::withMessages(['plan' => 'This service plan is not available for this platform.']);
            }

            $existingPending = Subscription::query()
                ->where('platform_id', $platform->id)
                ->where('service_id', $plan->service_id)
                ->where('status', 'pending')
                ->latest('id')
                ->first();

            if ($existingPending !== null && (float) $plan->price > 0) {
                if ($existingPending->plan_id !== $plan->id) {
                    throw ValidationException::withMessages(['plan' => 'A plan request for this service is already awaiting review.']);
                }

                return ['status' => 'pending', 'api_secret' => null, 'request_created' => false];
            }

            $active = Subscription::query()
                ->where('platform_id', $platform->id)
                ->where('service_id', $plan->service_id)
                ->where('status', 'active')
                ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>', now()))
                ->latest('id')
                ->first();

            if ($active !== null) {
                if ($active->plan_id === $plan->id) {
                    if ((float) $plan->price > 0) {
                        return ['status' => 'active', 'api_secret' => null];
                    }

                    PlatformServiceAccess::query()->updateOrCreate(
                        ['platform_id' => $platform->id, 'service_id' => $plan->service_id],
                        ['has_access' => true, 'effective_until' => null, 'synced_at' => now()],
                    );
                    [, $apiSecret] = $this->ensureIntegrationKey($platform);

                    return ['status' => 'active', 'api_secret' => $apiSecret];
                }

                if ((float) $plan->price <= 0) {
                    throw ValidationException::withMessages(['plan' => 'A different plan is already active for this service. Contact support to change it.']);
                }
            }

            if ((float) $plan->price > 0) {
                Subscription::query()->create([
                    'platform_id' => $platform->id,
                    'service_id' => $plan->service_id,
                    'plan_id' => $plan->id,
                    'status' => 'pending',
                    'starts_at' => now(),
                    'auto_renew' => false,
                ]);

                return ['status' => 'pending', 'api_secret' => null, 'request_created' => true];
            }

            [, $apiSecret] = $this->ensureIntegrationKey($platform);

            Subscription::query()->create([
                'platform_id' => $platform->id,
                'service_id' => $plan->service_id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now(),
                'auto_renew' => false,
            ]);

            PlatformServiceAccess::query()->updateOrCreate(
                ['platform_id' => $platform->id, 'service_id' => $plan->service_id],
                ['has_access' => true, 'effective_until' => null, 'synced_at' => now()],
            );

            return ['status' => 'active', 'api_secret' => $apiSecret];
        }, 3);
    }

    /** @return array{PlatformIntegration, ?string} */
    private function ensureIntegrationKey(Platform $platform): array
    {
        $integration = $platform->integration()->firstOrCreate([], [
            'status' => PlatformIntegration::STATUS_ACTIVE,
            'paseto_version' => PlatformIntegration::PASETO_V4_LOCAL,
            'token_ttl_seconds' => (int) config('paseto.default_ttl_seconds', 3600),
            'rate_limit_per_minute' => (int) config('api.rate_limit_per_minute', 60),
        ]);

        if ($integration->status !== PlatformIntegration::STATUS_ACTIVE) {
            throw ValidationException::withMessages(['plan' => 'This platform API integration is not active. Contact support for help.']);
        }

        $apiSecret = null;
        if ($integration->primaryApiKey() === null) {
            [, $apiSecret] = $this->apiKeys->rotate($integration);
        }

        return [$integration, $apiSecret];
    }
}
