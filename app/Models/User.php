<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUlids, Notifiable, SoftDeletes;

    /**
     * ULID columns — `public_id` is the CHAR(26) public account key (Phase 2A T1 / ADR-002).
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
        'email',
        'phone',
        'password',
        'status',
        'timezone',
        'locale',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Platforms registered by this user (its owner / creator).
     *
     * @return HasMany<Platform, $this>
     */
    public function platforms(): HasMany
    {
        return $this->hasMany(Platform::class, 'created_by');
    }

    /**
     * Platform-admin assignments held by this user.
     *
     * @return HasMany<PlatformAdmin, $this>
     */
    public function platformAdmins(): HasMany
    {
        return $this->hasMany(PlatformAdmin::class);
    }

    /**
     * Send the password reset notification (phase-5a; wire the `resets` broker
     * to our branded notification so expiry + throttle stay framework-managed).
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification(#[\SensitiveParameter] mixed $token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
