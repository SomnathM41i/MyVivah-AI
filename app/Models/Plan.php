<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    /**
     * Service-specific plan (T5) — dynamic catalog (ADR-012), demo data seeded later.
     *
     * Public `public_id` CHAR(26) ULID used where plans are referenced by clients (plan §6).
     * Soft delete for deactivate-not-delete.
     *
     * @var string
     */
    protected $table = 'plans';

    /**
     * ULID column — `public_id` (CHAR(26) ULID) is the public plan reference.
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
        'service_id',
        'plan_key',
        'name',
        'description',
        'price',
        'currency',
        'billing_period',
        'billing_interval',
        'is_active',
        'features',
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
            'public_id' => 'string',
            'price' => 'decimal:2',
            'currency' => 'string',
            'billing_period' => 'string',
            'billing_interval' => 'integer',
            'is_active' => 'boolean',
            'features' => 'array',
            'sort_order' => 'integer',
        ];
    }

    /**
     * The service this plan belongs to.
     *
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Subscriptions purchased against this plan.
     *
     * @return HasMany<Subscription, $this>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
