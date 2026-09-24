<?php

namespace Database\Factories;

use App\Models\ApiAuditLog;
use App\Models\Platform;
use App\Models\PlatformApiKey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApiAuditLog>
 */
class ApiAuditLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * Append-only audit row — no PII, no secrets; stable event + safe metadata only.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform_id' => Platform::factory(),
            'event' => ApiAuditLog::EVENT_TOKEN_ISSUED,
            'ip_hash' => hash('sha256', fake()->ipv4()),
            'endpoint' => '/api/v1/platform/me',
            'method' => 'GET',
            'status_code' => 200,
            'duration_ms' => fake()->numberBetween(1, 120),
        ];
    }

    /**
     * Attach the owning platform.
     */
    public function forPlatform(Platform $platform): static
    {
        return $this->state(fn (array $attributes) => [
            'platform_id' => $platform->id,
        ]);
    }

    /**
     * Attach the involved API key.
     */
    public function forApiKey(PlatformApiKey $key): static
    {
        return $this->state(fn (array $attributes) => [
            'platform_id' => $key->integration->platform_id,
            'api_key_id' => $key->id,
        ]);
    }
}
