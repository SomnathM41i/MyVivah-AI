<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    /**
     * Payment & Billing (T7) — manual MVP (ADR-011), financial records retained.
     *
     * No soft delete (financial records must never be lost; database.md soft-delete matrix).
     * Internal-only: no `public_id` (plan §6 ULID coverage excludes `payments`).
     *
     * @var string
     */
    protected $table = 'payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'subscription_id',
        'platform_id',
        'gateway',
        'gateway_transaction_id',
        'amount',
        'currency',
        'status',
        'paid_at',
        'metadata',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gateway' => 'string',
            'amount' => 'decimal:2',
            'currency' => 'string',
            'status' => 'string',
            'paid_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /**
     * The subscription this payment settles.
     *
     * @return BelongsTo<Subscription, $this>
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * The platform that owns this payment (scoping root).
     *
     * @return BelongsTo<Platform, $this>
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }
}
