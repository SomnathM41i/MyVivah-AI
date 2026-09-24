<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the Phase 2D demo database seed (root orchestrator).
     *
     * Ordered by FK dependency (updated models 2C / docs/phase-2a-schema-plan.md):
     *
     *   1. DemoUserSeeder     — T1 `users` (ULID `public_id`, status/timezone/locale, verified)
     *   2. ServiceSeeder      — T4 `services` global catalog (`realtime_chat`, ADR-012)
     *   3. PlanSeeder         — T5 `plans` demo catalog (free/growth/business, INR, realtime_chat)
     *   4. DemoPlatformSeeder — T2 `platforms` (owned by demo owner) + T3 `platform_admins`
     *
     * All child seeders are idempotent (`updateOrCreate`/`updateOrCreate` on natural unique keys),
     * so `php artisan db:seed` and `migrate:fresh --seed` may be re-run safely — no duplicates, no
     * real data (example.test only, database.md demo rules).
     */
    public function run(): void
    {
        $this->call([
            DemoUserSeeder::class,
            ServiceSeeder::class,
            PlanSeeder::class,
            DemoPlatformSeeder::class,
        ]);
    }
}
