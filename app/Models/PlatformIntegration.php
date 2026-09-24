<?php

namespace App\Models;

use Database\Factories\PlatformIntegrationFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlatformIntegration extends Model
{
    /** @use HasFactory<PlatformIntegrationFactory> */
    use HasFactory, HasUlids;

    /**
     * ULID columns — `public_id` is the CHAR(26) integration key (Phase 3A §5.2,
     * ADR-002 ULID matrix). Exposed in token `sub`/`platform_id` claims and client
     * integrations; internal `id` stays out of wire payloads.
     *
     * @return list<string>
     */
    public function uniqueIds(): array
    {
        return ['public_id'];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'public_id',
        'platform_id',
        'status',
        'paseto_version',
        'token_ttl_seconds',
        'rate_limit_per_minute',
        'base_domain',
        'allowed_origins',
        'last_active_at',
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
            'allowed_origins' => 'array',
            'last_active_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_SUSPENDED = 'suspended';

    public const STATUS_DEACTIVATED = 'deactivated';

    /** PASETO version shared lock (Phase 3A §20/1; ADR-013). */
    public const PASETO_V4_LOCAL = 'v4.local';

    /**
     * The owning platform — isolation root for every integration query.
     *
     * @return BelongsTo<Platform, $this>
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * All keys ever issued for this integration (primary, backups, revoked).
     *
     * @return HasMany<PlatformApiKey, $this>
     */
    public function apiKeys(): HasMany
    {
        return $this->hasMany(PlatformApiKey::class);
    }

    /**
     * The active primary key, or null when none exists.
     */
    public function primaryApiKey(): ?PlatformApiKey
    {
        return $this->apiKeys()
            ->where('is_primary', true)
            ->where('status', PlatformApiKey::STATUS_ACTIVE)
            ->whereNull('revoked_at')
            ->latest('id')
            ->first();
    }
}
