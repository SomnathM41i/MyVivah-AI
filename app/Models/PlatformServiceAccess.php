<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformServiceAccess extends Model
{
    use HasFactory;

    protected $table = 'platform_service_access';

    protected $fillable = [
        'platform_id',
        'service_id',
        'has_access',
        'effective_until',
        'synced_at',
    ];

    protected $casts = [
        'has_access' => 'boolean',
        'effective_until' => 'datetime',
        'synced_at' => 'datetime',
    ];

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * The service this entitlement row grants access to.
     *
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
