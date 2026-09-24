<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\Platform;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * 1:1 with docs/phase-2a-schema-plan.md — T6 `subscriptions` (status-driven lifecycle;
     * ADR-012 dynamic catalog). `public_id` auto-generated via HasUlids. No soft delete.
     * Factory keeps `service_id` consistent with the `plan_id` it creates.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform_id' => Platform::factory(),
            'plan_id' => Plan::factory(),
            'service_id' => fn (array $attributes) => Plan::find($attributes['plan_id'])->service_id,
            'status' => 'pending',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'next_billing_at' => now()->addMonth(),
            'auto_renew' => false,
        ];
    }

    /**
     * A live, auto-renewing subscription (active status).
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'auto_renew' => true,
        ]);
    }
}
