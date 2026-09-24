<?php

namespace App\Services;

use App\Models\PlatformApiKey;
use App\Models\PlatformIntegration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use ParagonIE\Paseto\Keys\Version4\SymmetricKey;

/**
 * API key lifecycle operations: rotation (primary → backup + grace), revocation,
 * and secret generation (phase-3a-api-integration-plan.md §6.3).
 *
 * Security contract:
 *   - Raw secrets are generated here and returned to the caller EXACTLY ONCE.
 *   - The DB stores only the AES-256-CBC ciphertext (`key_encrypted`) and the
 *     non-reversible `key_fingerprint` (sha256) used in the PASETO `kid`.
 *   - Rotation never exposes the new secret in logs, audit metadata, or the DB.
 *   - Demoted (rotated) keys stay usable for `paseto.rotation_grace_seconds`
 *     (default 24h), then are hard-revoked on first presentation (see
 *     PasetoTokenService::assertKeyUsable / PlatformApiKey::isUsable).
 */
class ApiKeyService
{
    public function __construct(
        private readonly ApiAuditService $audit,
    ) {}

    /**
     * Generate a fresh base64url-encoded v4.local symmetric secret (32 bytes).
     */
    public static function generateSecret(): string
    {
        return (new SymmetricKey(random_bytes(32)))->encode();
    }

    /**
     * Rotate the integration's primary key.
     *
     * Demotes the current primary to `rotated` (grace backup) and creates a new
     * `active` primary. Returns the new key model plus its one-time raw secret.
     *
     * @return array{0: PlatformApiKey, 1: string} [newKey, oneTimeSecret]
     */
    public function rotate(
        PlatformIntegration $integration,
        ?\DateTimeInterface $now = null
    ): array {
        $current = $integration->primaryApiKey();
        $secret = self::generateSecret();

        $newKey = DB::transaction(function () use ($integration, $current, $secret, $now) {
            if ($current !== null) {
                $current->forceFill([
                    'status' => PlatformApiKey::STATUS_ROTATED,
                    'is_primary' => false,
                    'primary_token' => null,
                    'rotated_at' => $now ?? now(),
                ])->save();
            }

            return PlatformApiKey::query()->create([
                'platform_integration_id' => $integration->id,
                'key_fingerprint' => PlatformApiKey::fingerprint($secret),
                'key_encrypted' => Crypt::encryptString($secret),
                'is_primary' => true,
                'primary_token' => PlatformApiKey::PRIMARY_TOKEN,
                'status' => PlatformApiKey::STATUS_ACTIVE,
                'token_ttl_seconds' => $this->ttlFor($integration),
                'created_at' => $now ?? now(),
            ]);
        });

        // Audit the transition. Fingerprints (kids) only — NEVER the raw secret.
        $this->audit->keyRotated(
            $integration->platform,
            $current,
            $newKey,
            [
                'integration_public_id' => $integration->public_id,
                'previous_kid' => $current?->key_fingerprint,
                'current_kid' => $newKey->key_fingerprint,
            ]
        );

        return [$newKey, $secret];
    }

    /**
     * Revoke ONE key immediately (hard lifecycle; phase-3a §6.3).
     *
     * The caller is responsible for ensuring the key belongs to the given
     * integration (platform isolation is enforced at the controller layer).
     */
    public function revokeKey(
        PlatformIntegration $integration,
        PlatformApiKey $key,
        ?\DateTimeInterface $now = null
    ): void {
        $key->forceFill([
            'status' => PlatformApiKey::STATUS_REVOKED,
            'revoked_at' => $now ?? now(),
            'is_primary' => false,
            'primary_token' => null,
        ])->save();

        $this->audit->keyRotated(
            $integration->platform,
            $key,
            null,
            [
                'integration_public_id' => $integration->public_id,
                'action' => 'revoke',
                'revoked_kid' => $key->key_fingerprint,
            ]
        );
    }

    /**
     * Revoke ALL of an integration's keys immediately (hard lifecycle).
     */
    public function revokeAll(PlatformIntegration $integration, ?\DateTimeInterface $now = null): void
    {
        $affected = $integration->apiKeys()
            ->whereNull('revoked_at')
            ->update([
                'status' => PlatformApiKey::STATUS_REVOKED,
                'revoked_at' => $now ?? now(),
            ]);

        if ($affected > 0) {
            $this->audit->keyRotated(
                $integration->platform,
                null,
                null,
                [
                    'integration_public_id' => $integration->public_id,
                    'action' => 'revoke_all',
                    'keys_revoked' => $affected,
                ]
            );
        }
    }

    private function ttlFor(PlatformIntegration $integration): int
    {
        $default = (int) config('paseto.default_ttl_seconds', 3600);
        $max = (int) config('paseto.max_ttl_seconds', 3600);
        $ttl = (int) ($integration->token_ttl_seconds ?: $default);

        return min(max(1, $ttl), $max);
    }
}
