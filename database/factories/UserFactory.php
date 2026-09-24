<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * State the model's default password (single hash reused across factory-created users).
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * 1:1 with docs/phase-2a-schema-plan.md — T1 `users` (Phase 2A columns).
     * `public_id`, `status`, `timezone`, `locale` are auto-defaulted by the model/migration
     * where possible; the factory only fills the columns it owns (ADR-002 ULID via HasUlids).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->numerify('+91##########'),
            'phone_verified_at' => null,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'status' => 'active',
            'timezone' => 'UTC',
            'locale' => 'en',
            'last_login_at' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
