<?php

namespace App\Models;

use Database\Factories\PlatformFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Platform extends Model
{
    /** @use HasFactory<PlatformFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    /**
     * ULID columns — `public_id` is the CHAR(26) platform key used across widget config,
     * client URLs, and API contracts (Phase 2A T2 / ADR-002 / platform-isolation.md).
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
        'name',
        'slug',
        'website_url',
        'description',
        'status',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    /**
     * Progression statuses used in MVP (pending → active; suspended/deactivated).
     */
    public const STATUS_PENDING = 'pending';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_SUSPENDED = 'suspended';

    public const STATUS_DEACTIVATED = 'deactivated';

    /**
     * The user who created/owns the platform.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Users assigned as admins (owner/admin/developer) via `platform_admins`.
     *
     * @return BelongsToMany<User, $this>
     */
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'platform_admins', 'platform_id', 'user_id')
            ->withPivot(['role', 'invited_at', 'accepted_at']);
    }

    /**
     * Platform-admin assignment rows (role-scoped, non-soft-deleted).
     *
     * @return HasMany<PlatformAdmin, $this>
     */
    public function platformAdmins(): HasMany
    {
        return $this->hasMany(PlatformAdmin::class);
    }

    /**
     * Active/pending subscriptions held by this platform.
     *
     * @return HasMany<Subscription, $this>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Payment records for this platform.
     *
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Precomputed entitlement rows for this platform.
     *
     * @return HasMany<PlatformServiceAccess, $this>
     */
    public function serviceAccess(): HasMany
    {
        return $this->hasMany(PlatformServiceAccess::class);
    }

    /**
     * This platform's API integration root (1:1, Phase 3A T9).
     *
     * @return HasOne<PlatformIntegration, $this>
     */
    public function integration(): HasOne
    {
        return $this->hasOne(PlatformIntegration::class);
    }

    /**
     * External→local user identity references for this platform (phase-3a §8).
     *
     * @return HasMany<ExternalUserMap, $this>
     */
    public function externalUserMaps(): HasMany
    {
        return $this->hasMany(ExternalUserMap::class);
    }

    /**
     * Chat conversations owned by this platform (phase-3d).
     *
     * @return HasMany<Conversation, $this>
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Security/integration audit rows for this platform (phase-3a §13).
     *
     * @return HasMany<ApiAuditLog, $this>
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(ApiAuditLog::class);
    }
}
