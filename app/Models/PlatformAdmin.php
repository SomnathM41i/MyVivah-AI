<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformAdmin extends Model
{
    use HasFactory;

    /**
     * Platform↔user admin/owner/developer assignment (T3).
     *
     * Internal-only table — no `public_id` (plan §6 ULID coverage list excludes it).
     * No soft delete (per database.md soft-delete matrix). UNIQUE(platform_id, user_id)
     * guaranteed at the schema level.
     *
     * @var string
     */
    protected $table = 'platform_admins';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'platform_id',
        'user_id',
        'role',
        'invited_at',
        'accepted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => 'string',
            'invited_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    /**
     * The platform this admin belongs to (scoping root).
     *
     * @return BelongsTo<Platform, $this>
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * The user holding this admin role.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
