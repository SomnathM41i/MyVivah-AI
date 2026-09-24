<?php

namespace Database\Seeders;

use App\Models\ExternalUserMap;
use App\Models\Platform;
use App\Models\PlatformAdmin;
use App\Models\PlatformApiKey;
use App\Models\PlatformIntegration;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use ParagonIE\Paseto\Keys\Version4\SymmetricKey;

class DemoPlatformSeeder extends Seeder
{
    /**
     * Seed a demo platform (T2 `platforms` — isolation root) owned by the demo owner.
     *
     * Idempotent: `updateOrCreate` on the unique `slug`; `public_id` ULID auto-generated via
     * HasUlids. Platform-scoped demo fixtures only (example.test — no real data, database.md).
     * `created_by` resolves the demo owner (`owner@example.test` from DemoUserSeeder) so the
     * demo platform tenant root maps to a real owner. Also creates the platform↔owner admin row.
     *
     * Phase 4: additionally provisions a LIVE integration + primary symmetric key
     * (so `/demo/chat` can mint widget sessions locally) and a small, platform-scoped
     * set of demo external users (so the widget's identity-map search has something
     * to find). The symmetric key plaintext is never persisted — only its fingerprint
     * and the APP_KEY-encrypted ciphertext (identical to PlatformApiKeyFactory).
     */
    public function run(): void
    {
        $owner = User::query()->where('email', 'owner@example.test')->first();

        if ($owner === null) {
            return; // DemoUserSeeder must run first.
        }

        $slug = 'demo-matrimony-site';

        $platform = Platform::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => 'Demo Matrimony Site',
                'website_url' => 'https://demo-matrimony.example.test',
                'description' => 'Demo matrimony platform for the MyVivah-AI seed data (ADRs/example.test only).',
                'status' => 'active',
                'created_by' => $owner->id,
            ],
        );

        PlatformAdmin::updateOrCreate(
            ['platform_id' => $platform->id, 'user_id' => $owner->id],
            ['role' => 'owner'],
        );

        $integration = PlatformIntegration::updateOrCreate(
            ['platform_id' => $platform->id],
            [
                'status' => PlatformIntegration::STATUS_ACTIVE,
                'paseto_version' => PlatformIntegration::PASETO_V4_LOCAL,
                'token_ttl_seconds' => 3600,
                'rate_limit_per_minute' => 60,
                // Demo page + the widget's own demo origin are allowed by default.
                'allowed_origins' => ['http://127.0.0.1', 'http://localhost'],
                'last_active_at' => now(),
            ],
        );

        if (! $integration->primaryApiKey() instanceof PlatformApiKey) {
            $key = new SymmetricKey(random_bytes(32));

            PlatformApiKey::query()->create([
                'platform_integration_id' => $integration->id,
                'key_fingerprint' => PlatformApiKey::fingerprint($key->encode()),
                'key_encrypted' => Crypt::encryptString($key->encode()),
                'is_primary' => true,
                'primary_token' => PlatformApiKey::PRIMARY_TOKEN,
                'status' => PlatformApiKey::STATUS_ACTIVE,
                'token_ttl_seconds' => 3600,
            ]);
        }

        $demoUsers = [
            ['alice@demo.example.test', 'user_001_alice'],
            ['bob@demo.example.test', 'user_002_bob'],
            ['cara@demo.example.test', 'user_003_cara'],
            ['dan@demo.example.test', 'user_004_dan'],
            ['elena@demo.example.test', 'user_005_elena'],
        ];

        foreach ($demoUsers as [$externalId, $localPublicId]) {
            ExternalUserMap::updateOrCreate(
                ['platform_id' => $platform->id, 'external_user_id' => $externalId],
                ['local_public_id' => $localPublicId, 'synced_at' => now(), 'last_seen_at' => now()],
            );
        }
    }
}
