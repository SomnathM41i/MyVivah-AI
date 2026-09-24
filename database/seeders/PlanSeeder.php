<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Service;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Seed the dynamic demo plan catalog (T5 `plans`, ADR-012) for the seeded `realtime_chat`
     * service.
     *
     * Idempotent: `updateOrCreate` on the natural pair [service_id, plan_key] (UNIQUE at schema) —
     * re-runs never duplicate. `public_id` ULID auto-generated via HasUlids. Demo pricing in INR
     * (fake, ADR-012 dynamic catalog). `service_public_id` reserved for the client contract.
     */
    public function run(): void
    {
        $service = Service::query()->where('key', 'realtime_chat')->first();

        if ($service === null) {
            return; // ServiceSeeder must run first.
        }

        $demoPlans = [
            [
                'plan_key' => 'free',
                'name' => 'Free',
                'description' => '1:1 chat, 1 concurrent session, no history export.',
                'price' => 0.00,
                'currency' => 'INR',
                'billing_period' => 'monthly',
                'billing_interval' => null,
                'is_active' => true,
                'features' => ['chat_1v1' => true, 'history_export' => false, 'concurrent_sessions' => 1],
                'sort_order' => 10,
            ],
            [
                'plan_key' => 'growth',
                'name' => 'Growth',
                'description' => 'Group chat for up to 50, 30-day history, priority support.',
                'price' => 499.00,
                'currency' => 'INR',
                'billing_period' => 'monthly',
                'billing_interval' => null,
                'is_active' => true,
                'features' => ['chat_group' => true, 'group_member_limit' => 50, 'history_days' => 30],
                'sort_order' => 20,
            ],
            [
                'plan_key' => 'business',
                'name' => 'Business',
                'description' => 'Unlimited sessions, 1-year history, export + moderation API.',
                'price' => 1499.00,
                'currency' => 'INR',
                'billing_period' => 'monthly',
                'billing_interval' => null,
                'is_active' => true,
                'features' => ['history_days' => 365, 'history_export' => true, 'moderation_api' => true],
                'sort_order' => 30,
            ],
        ];

        foreach ($demoPlans as $plan) {
            Plan::updateOrCreate(
                ['service_id' => $service->id, 'plan_key' => $plan['plan_key']],
                $plan,
            );
        }
    }
}
