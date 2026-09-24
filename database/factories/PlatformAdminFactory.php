<?php

namespace Database\Factories;

use App\Models\Platform;
use App\Models\PlatformAdmin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlatformAdmin>
 */
class PlatformAdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * 1:1 with docs/phase-2a-schema-plan.md — T3 `platform_admins`.
     * Internal-only (no `public_id`); UNIQUE(platform_id, user_id) enforced at schema level —
     * seeders/factories must never create duplicate (platform, user) pairs.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform_id' => Platform::factory(),
            'user_id' => User::factory(),
            'role' => 'admin',
            'invited_at' => now(),
            'accepted_at' => now(),
        ];
    }

    /**
     * Grant the strongest admin role (single owner per platform — service-layer rule).
     */
    public function owner(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'owner',
        ]);
    }
}
