<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Public-site contact form submission (Phase 5A).
 *
 * Append-mostly inbox for messages sent through the public `/contact` form.
 * Internal-only table (no `public_id`, no soft-delete). Stores a hashed IP
 * (never the raw IP) for abuse triage; see migration comments.
 *
 * @property int $id
 * @property string $name
 * @property string|null $company
 * @property string $email
 * @property string|null $phone
 * @property string $message
 * @property string|null $ip_hash
 * @property string|null $source_url
 * @property Carbon|null $created_at
 */
class ContactMessage extends Model
{
    use HasFactory;

    /**
     * Contact submissions are append-only (no updated_at needed).
     */
    public const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'company',
        'email',
        'phone',
        'message',
        'ip_hash',
        'source_url',
    ];
}
