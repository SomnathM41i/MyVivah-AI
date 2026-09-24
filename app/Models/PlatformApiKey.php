<?php

namespace App\Models;

use Database\Factories\PlatformApiKeyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use ParagonIE\Paseto\Exception\PasetoException;
use ParagonIE\Paseto\Keys\SymmetricKey;
use ParagonIE\Paseto\Protocol\Version4;

class PlatformApiKey extends Model
{
    /** @use HasFactory<PlatformApiKeyFactory> */
    use HasFactory;

    /**
     * Keys are immutable audit-lifecycle rows: no `updated_at`. Soft deletes are
     * NOT used (crypto keys are hard-lifecycle records per AGENTS.md matrix).
     *
     * @var string
     */
    protected $table = 'platform_api_keys';

    public const UPDATED_AT = null;

    /** Sentinel stored in `primary_token` on the primary row. */
    public const PRIMARY_TOKEN = 'PRIMARY';

    /** Explicit lifecycle states (phase-3a-api-integration-plan.md §6.3). */
    public const STATUS_ACTIVE = 'active';

    public const STATUS_ROTATED = 'rotated';

    public const STATUS_REVOKED = 'revoked';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'platform_integration_id',
        'key_fingerprint',
        'key_encrypted',
        'is_primary',
        'primary_token',
        'status',
        'backup_of_id',
        'token_ttl_seconds',
        'rotated_at',
        'revoked_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'rotated_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    /**
     * Deterministic, non-reversible key identifier used in the PASETO `kid`.
     *
     * The fingerprint is the SHA-256 of the base64url-encoded raw key. It is NOT
     * key material — safe to expose in tokens, DB lookups and logs.
     */
    public static function fingerprint(string $base64urlEncodedRawKey): string
    {
        return substr(hash('sha256', $base64urlEncodedRawKey), 0, 40);
    }

    /**
     * The provisioning integration that owns this key.
     *
     * @return BelongsTo<PlatformIntegration, $this>
     */
    public function integration(): BelongsTo
    {
        return $this->belongsTo(PlatformIntegration::class, 'platform_integration_id');
    }

    /**
     * Whether the key is still usable (never revoked / revoked status).
     */
    public function isRevoked(): bool
    {
        return $this->revoked_at !== null || $this->status === self::STATUS_REVOKED;
    }

    /**
     * Whether the key can still be used to issue/validate tokens at `$now`.
     *
     * Lifecycle rules (phase-3a §6.3):
     *   - `active` → usable.
     *   - `rotated` → usable ONLY while inside its rotation grace window
     *     (`rotated_at` + `paseto.rotation_grace_seconds`, default 24h).
     *   - `revoked` / `revoked_at` set → never usable.
     */
    public function isUsable(?\DateTimeInterface $now = null): bool
    {
        if ($this->isRevoked()) {
            return false;
        }

        if ($this->status === self::STATUS_ACTIVE) {
            return true;
        }

        if ($this->status === self::STATUS_ROTATED) {
            $grace = $this->graceExpiresAt();

            return $grace !== null && ($now ?? now()) < $grace;
        }

        return false;
    }

    /**
     * Moment the rotation grace window ends for a demoted key, or null when the
     * key is not in the rotated state.
     */
    public function graceExpiresAt(?\DateTimeInterface $now = null): ?Carbon
    {
        if ($this->status !== self::STATUS_ROTATED || $this->rotated_at === null) {
            return null;
        }

        $seconds = max(0, (int) config('paseto.rotation_grace_seconds', 86400));

        return Carbon::instance($this->rotated_at)->copy()->addSeconds($seconds);
    }

    /**
     * Hard-revoke the key (used when a rotated key is presented after its grace
     * window expired — the revocation is persisted on sight).
     */
    public function hardRevoke(?\DateTimeInterface $now = null): void
    {
        $this->forceFill([
            'status' => self::STATUS_REVOKED,
            'revoked_at' => $now ?? now(),
        ])->save();
    }

    /**
     * Rebuild the PASETO symmetric key from the encrypted-at-rest material.
     *
     * The ciphertext is AES-256-CBC via APP_KEY (Laravel Crypt) of the base64url
     * v4 key string. The raw secret is only ever present in memory during
     * issue/validate — never persisted, logged, or returned.
     *
     * @throws PasetoException|\RuntimeException when the stored ciphertext cannot be decrypted.
     */
    public function toSymmetricKey(): SymmetricKey
    {
        try {
            $encoded = Crypt::decryptString($this->key_encrypted);
        } catch (\Throwable $ex) {
            throw new \RuntimeException('Unable to decrypt API key material.', 0, $ex);
        }

        return SymmetricKey::fromEncodedString($encoded, new Version4);
    }
}
