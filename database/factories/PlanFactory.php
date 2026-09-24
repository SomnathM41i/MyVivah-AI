<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * 1:1 with docs/phase-2a-schema-plan.md — T5 `plans` (dynamic, ADR-012).
     * `public_id` auto-generated via HasUlids. UNIQUE(service_id, plan_key) at schema level.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $planKey = fake()->unique()->words(2, true);

        return [
            'service_id' => Service::factory(),
            'plan_key' => Str::snake($planKey),
            'name' => Str::title(Str::replace('_', ' ', Str::snake($planKey))),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 99, 4999),
            'currency' => 'INR',
            'billing_period' => 'monthly',
            'is_active' => false,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * A publishable, active plan tied to a given service.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}
