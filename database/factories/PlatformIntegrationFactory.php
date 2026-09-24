<?php

namespace Database\Factories;

use App\Models\Platform;
use App\Models\PlatformIntegration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlatformIntegration>
 */
class PlatformIntegrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * 1:1 with phase-3a-api-integration-plan.md §5.2 — T9 `platform_integrations`
     * (integration root). `public_id` auto-generated via HasUlids; status `pending`
     * by default (must be activated before tokens are issued).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform_id' => Platform::factory(),
            'status' => 'pending',
            'paseto_version' => PlatformIntegration::PASETO_V4_LOCAL,
            'token_ttl_seconds' => 3600,
            'rate_limit_per_minute' => 60,
            'base_domain' => fake()->domainName(),
            'allowed_origins' => [],
        ];
    }

    /**
     * Attach a specific platform instead of factory-created.
     */
    public function forPlatform(Platform $platform): static
    {
        return $this->state(fn (array $attributes) => [
            'platform_id' => $platform->id,
        ]);
    }

    /**
     * Activate the integration (tokens may now be issued/validated).
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Suspend the integration (authentication must fail).
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
        ]);
    }
}
