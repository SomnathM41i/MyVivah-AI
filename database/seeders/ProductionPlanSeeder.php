<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Service;
use Illuminate\Database\Seeder;
use RuntimeException;

/** Provision only the approved zero-price launch plan on production deploys. */
class ProductionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $service = Service::query()
            ->where('key', 'realtime_chat')
            ->where('is_active', true)
            ->first();

        if ($service === null) {
            throw new RuntimeException('Cannot publish the Free plan: the active realtime_chat service is missing.');
        }

        Plan::query()->firstOrCreate(
            ['service_id' => $service->id, 'plan_key' => 'free'],
            [
                'name' => 'Free',
                'description' => 'Free access to the real-time chat service.',
                'price' => 0,
                'currency' => 'INR',
                'billing_period' => 'custom',
                'billing_interval' => null,
                'is_active' => true,
                'features' => [],
                'sort_order' => 10,
            ],
        );
    }
}
