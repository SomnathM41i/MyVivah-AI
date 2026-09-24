<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory, HasUlids;

    /**
     * Service & Subscription lifecycle (T6) — platform_scoped, status-driven.
     *
     * No soft delete (status transitions only, ADR-00x / database.md soft-delete matrix).
     * At-most-one `active` per (platform, service) enforced in service layer (open decision,
     * platform-isolation.md), not via a partial unique index.
     *
     * @var string
     */
    protected $table = 'subscriptions';

    /**
     * ULID column — `public_id` (CHAR(26) ULID) is the public subscription reference.
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
        'service_id',
        'plan_id',
        'status',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'next_billing_at',
        'auto_renew',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'public_id' => 'string',
            'status' => 'string',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'next_billing_at' => 'datetime',
            'auto_renew' => 'boolean',
        ];
    }

    /**
     * The platform that owns this subscription (scoping root).
     *
     * @return BelongsTo<Platform, $this>
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * The service being subscribed to.
     *
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * The purchased plan.
     *
     * @return BelongsTo<Plan, $this>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Payments recorded against this subscription.
     *
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
