<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Platform;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * 1:1 with docs/phase-2a-schema-plan.md — T7 `payments` (Payment & Billing, manual MVP/ADR-011).
     * Internal-only: no `public_id` ULID, no soft delete (financial records retained — database.md
     * soft-delete matrix). `platform_id` derived from the parent subscription's platform to stay
     * platform-scoped (isolation root); gateway left `null` (manual offline payment at MVP).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'platform_id' => fn (array $attributes) => Subscription::find($attributes['subscription_id'])->platform_id,
            'gateway' => null,
            'gateway_transaction_id' => null,
            'amount' => fake()->randomFloat(2, 999, 999_999),
            'currency' => 'INR',
            'status' => 'pending',
            'paid_at' => null,
            'metadata' => null,
        ];
    }

    /**
     * Mark the payment as completed (manually settled per ADR-011).
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'paid_at' => now(),
            'metadata' => ['collected_by' => 'manual'],
        ]);
    }
}
