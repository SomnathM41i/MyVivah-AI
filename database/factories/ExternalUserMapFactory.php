<?php

namespace Database\Factories;

use App\Models\ExternalUserMap;
use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExternalUserMap>
 */
class ExternalUserMapFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * Mapping + timestamps only — NO profile data (source of truth stays on the
     * external platform; phase-3a §8).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform_id' => Platform::factory(),
            'external_user_id' => (string) fake()->unique()->numberBetween(100000, 999999),
            'local_public_id' => null,
            'metadata' => null,
            'synced_at' => now(),
            'last_seen_at' => now(),
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
}
