<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Seed the global service catalog (T4 `services`, ADR-012 dynamic catalog).
     *
     * Idempotent: `updateOrCreate` on the natural unique `key` — re-running `db:seed` never
     * duplicates. Global catalog (platform-independent — platform-isolation.md); demo fake data
     * only (`realtime_chat`, example.test policy, database.md). No `public_id` (schema covers
     * services with natural `key` only; ULID §6 does not include services).
     */
    public function run(): void
    {
        $catalog = [
            [
                'key' => 'realtime_chat',
                'name' => 'Realtime Chat',
                'description' => 'Realtime 1:1 and group chat for platform users (realtime-chat.md).',
                'is_active' => true,
                'sort_order' => 10,
            ],
        ];

        foreach ($catalog as $service) {
            Service::updateOrCreate(
                ['key' => $service['key']],
                [
                    'name' => $service['name'],
                    'description' => $service['description'],
                    'is_active' => $service['is_active'],
                    'sort_order' => $service['sort_order'],
                ],
            );
        }
    }
}
