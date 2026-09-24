<?php

namespace Database\Factories;

use App\Models\Platform;
use App\Models\PlatformServiceAccess;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlatformServiceAccess>
 */
class PlatformServiceAccessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * 1:1 with docs/phase-2a-schema-plan.md — T8 `platform_service_access` (entitlement cache,
     * cache-over-compute). Internal-only: no `public_id`, no soft delete, no timestamps in the row
     * (migration defines only platform_id/service_id/has_access/effective_until/synced_at).
     * UNIQUE(platform_id, service_id) guaranteed at schema level — never duplicate pairs.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform_id' => Platform::factory(),
            'service_id' => Service::factory(),
            'has_access' => false,
            'effective_until' => null,
            'synced_at' => now(),
        ];
    }

    /**
     * Grant platform-level access to the service (entitlement active).
     */
    public function granted(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_access' => true,
            'effective_until' => now()->addMonth(),
        ]);
    }
}
