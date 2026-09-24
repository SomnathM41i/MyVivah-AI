<?php

namespace App\Models;

use Database\Factories\ExternalUserMapFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Platform-scoped external→local user identity reference (phase-3a §5.2/§8).
 *
 * Data-minimization: mapping + sync timestamps + integration metadata ONLY.
 * The external platform remains the source of truth for the user's profile;
 * MyVivahAI never mirrors it. Identity key: UNIQUE(platform_id, external_user_id).
 */
class ExternalUserMap extends Model
{
    /** @use HasFactory<ExternalUserMapFactory> */
    use HasFactory;

    protected $table = 'platform_external_user_map';

    public const PRESENCE_ONLINE = 'online';

    public const PRESENCE_OFFLINE = 'offline';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'platform_id',
        'external_user_id',
        'local_public_id',
        'metadata',
        'synced_at',
        'last_seen_at',
        'presence_status',
        'presence_seen_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'synced_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'presence_seen_at' => 'datetime',
        ];
    }

    /**
     * The owning platform — the isolation root for every lookup.
     *
     * @return BelongsTo<Platform, $this>
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * The optional linked MyVivahAI user (by public ULID).
     *
     * @return BelongsTo<User, $this>
     */
    public function localUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'local_public_id', 'public_id');
    }
}
