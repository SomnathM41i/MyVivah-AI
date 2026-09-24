<?php

namespace App\Models;

use Database\Factories\MessageFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A single chat entry (phase-3d; docs/database.md messages).
 *
 * STRICTLY platform-scoped; idempotent via UNIQUE
 * (platform_id, conversation_id, client_message_id). `client_message_id` is the
 * widget-generated dedup key: retrying a failed send returns the original row.
 * Soft delete reserved for a future recall scenario (nothing deletes at MVP).
 */
class Message extends Model
{
    /** @use HasFactory<MessageFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    /**
     * ULID columns — only `public_id` is exposed to clients.
     *
     * @return list<string>
     */
    public function uniqueIds(): array
    {
        return ['public_id'];
    }

    public const TYPE_TEXT = 'text';

    public const TYPE_IMAGE = 'image';

    public const TYPE_FILE = 'file';

    public const TYPE_SYSTEM = 'system';

    public const STATUS_SENT = 'sent';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_READ = 'read';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'platform_id',
        'conversation_id',
        'sender_external_user_map_id',
        'type',
        'status',
        'content',
        'client_message_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => 'string',
            'status' => 'string',
        ];
    }

    /**
     * @return BelongsTo<Platform, $this>
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * @return BelongsTo<Conversation, $this>
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * The external user who sent this message.
     *
     * @return BelongsTo<ExternalUserMap, $this>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(ExternalUserMap::class, 'sender_external_user_map_id');
    }
}
