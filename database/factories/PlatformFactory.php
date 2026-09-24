<?php

namespace Database\Factories;

use App\Models\Platform;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Platform>
 */
class PlatformFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * 1:1 with docs/phase-2a-schema-plan.md — T2 `platforms` (isolation root).
     * `public_id` auto-generated via HasUlids; `slug` unique; owned by a creator `User`.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'slug' => fn (array $attributes) => Str::slug($attributes['name']).'-'.Str::lower(Str::random(6)),
            'website_url' => fake()->url(),
            'description' => fake()->sentence(),
            'status' => 'pending',
            'created_by' => User::factory(),
        ];
    }

    /**
     * Mark the platform as active for the seed/demo catalog.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Attach a specific creator (existing user) instead of factory-created.
     */
    public function createdBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => $user->id,
        ]);
    }
}
