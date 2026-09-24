<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * 1:1 with docs/phase-2a-schema-plan.md — T4 `services` (global product catalog,
     * platform-independent, ADR-012). Internal-only (no `public_id`).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $key = fake()->unique()->words(2, true);

        return [
            'key' => Str::snake($key),
            'name' => Str::title(Str::replace('_', ' ', Str::snake($key))),
            'description' => fake()->sentence(),
            'is_active' => false,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Mark the service as live in the catalog.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}
