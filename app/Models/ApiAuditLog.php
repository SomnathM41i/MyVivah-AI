<?php

namespace App\Models;

use Database\Factories\ApiAuditLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Append-only security/integration audit row (phase-3a §5.2/§13).
 *
 * Immutable by design: no `updated_at`, no soft deletes. Privacy contract:
 * never stores raw secrets, bodies, or PII — only fingerprints/checksums and
 * minimal metadata.
 */
class ApiAuditLog extends Model
{
    /** @use HasFactory<ApiAuditLogFactory> */
    use HasFactory;

    protected $table = 'api_audit_logs';

    public const UPDATED_AT = null;

    /** Stable event identifiers (phase-3a §13 + security.md). */
    public const EVENT_REQUEST = 'request';

    public const EVENT_TOKEN_ISSUED = 'token_issued';

    public const EVENT_TOKEN_REJECTED = 'token_rejected';

    public const EVENT_TOKEN_EXPIRED = 'token_expired';

    public const EVENT_INVALID_TOKEN = 'invalid_token';

    public const EVENT_WRONG_PLATFORM = 'wrong_platform';

    public const EVENT_INSUFFICIENT_SCOPE = 'insufficient_scope';

    public const EVENT_KEY_ROTATION = 'key_rotation';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'platform_id',
        'api_key_id',
        'event',
        'ip_hash',
        'endpoint',
        'method',
        'status_code',
        'duration_ms',
        'request_checksum',
        'response_checksum',
        'metadata',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'duration_ms' => 'integer',
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * The owning platform (null for pre-auth failures that could not be resolved).
     *
     * @return BelongsTo<Platform, $this>
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * The API key involved (null when unknown).
     *
     * @return BelongsTo<PlatformApiKey, $this>
     */
    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(PlatformApiKey::class);
    }
}
