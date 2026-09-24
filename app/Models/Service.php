<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * MyVivahAI product-catalog service (T4), e.g. `realtime_chat`.
     *
     * Global catalog — not platform-owned (no `platform_id`; see platform-isolation.md §catalog).
     * Internal-only: no `public_id` (plan §6 ULID coverage). Soft delete for deactivate-not-delete.
     *
     * @var string
     */
    protected $table = 'services';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'name',
        'description',
        'is_active',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Plans offered by this service.
     *
     * @return HasMany<Plan, $this>
     */
    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    /**
     * Subscriptions for this service across platforms.
     *
     * @return HasMany<Subscription, $this>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Entitlement rows granting platforms access to this service.
     *
     * @return HasMany<PlatformServiceAccess, $this>
     */
    public function platformServiceAccess(): HasMany
    {
        return $this->hasMany(PlatformServiceAccess::class);
    }
}
