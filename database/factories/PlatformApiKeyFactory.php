<?php

namespace Database\Factories;

use App\Models\PlatformApiKey;
use App\Models\PlatformIntegration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;
use ParagonIE\Paseto\Keys\Version4\SymmetricKey;

/**
 * @extends Factory<PlatformApiKey>
 */
class PlatformApiKeyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * 1:1 with phase-3a-api-integration-plan.md §5.2 — T10 `platform_api_keys`.
     * The raw 32-byte v4 key is generated here, its fingerprint computed, then
     * stored ONLY as encrypted ciphertext (`key_encrypted`, AES-256-CBC via
     * APP_KEY). The plaintext is never persisted.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $key = new SymmetricKey(random_bytes(32));

        return [
            'platform_integration_id' => PlatformIntegration::factory(),
            'key_fingerprint' => PlatformApiKey::fingerprint($key->encode()),
            'key_encrypted' => Crypt::encryptString($key->encode()),
            'is_primary' => true,
            'primary_token' => PlatformApiKey::PRIMARY_TOKEN,
            'status' => PlatformApiKey::STATUS_ACTIVE,
            'token_ttl_seconds' => 3600,
        ];
    }

    /**
     * Attach a specific integration instead of factory-created.
     */
    public function forIntegration(PlatformIntegration $integration): static
    {
        return $this->state(fn (array $attributes) => [
            'platform_integration_id' => $integration->id,
        ]);
    }

    /**
     * Mark the key as the active primary for its integration.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
            'primary_token' => PlatformApiKey::PRIMARY_TOKEN,
        ]);
    }

    /**
     * Mark the key as a rotated/backup (non-primary) key inside its grace window.
     */
    public function backup(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => false,
            'primary_token' => null,
            'status' => PlatformApiKey::STATUS_ROTATED,
            'rotated_at' => now(),
        ]);
    }

    /**
     * Hard-revoke the key.
     */
    public function revoked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PlatformApiKey::STATUS_REVOKED,
            'revoked_at' => now(),
        ]);
    }

    /**
     * Use a known raw secret (base64url-encoded 32-byte v4 key). Enables tests
     * to reproduce the exact key that should be sent as `client_secret` or used
     * to mint tokens directly.
     */
    public function withRawSecret(string $base64url): static
    {
        return $this->state(fn (array $attributes) => [
            'key_fingerprint' => PlatformApiKey::fingerprint($base64url),
            'key_encrypted' => Crypt::encryptString($base64url),
        ]);
    }
}
