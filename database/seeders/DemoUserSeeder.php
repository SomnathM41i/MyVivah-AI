<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Seed demo users (T1 `users`) — development-only, fake data, `example.test` domain only.
     *
     * Reproducible: `updateOrCreate` on unique `email` — re-running never duplicates. No real
     * PII/phones (fake Indian-style digits via `numerify`, `example.test` emails per database.md
     * demo-data rules). Password is the shared demo hash `password` (same convention as
     * UserFactory). `public_id` ULID auto-generated.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Demo Platform Owner',
                'email' => 'owner@example.test',
                'phone' => '+919876543210',
                'status' => 'active',
                'timezone' => 'Asia/Kolkata',
                'locale' => 'en',
            ],
            [
                'name' => 'Demo User Alpha',
                'email' => 'alpha@example.test',
                'phone' => '+919876543211',
                'status' => 'active',
                'timezone' => 'Asia/Kolkata',
                'locale' => 'en',
            ],
            [
                'name' => 'Demo User Beta',
                'email' => 'beta@example.test',
                'phone' => '+919876543212',
                'status' => 'active',
                'timezone' => 'UTC',
                'locale' => 'en',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'phone' => $userData['phone'],
                    'status' => $userData['status'],
                    'timezone' => $userData['timezone'],
                    'locale' => $userData['locale'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ],
            );
        }
    }
}
