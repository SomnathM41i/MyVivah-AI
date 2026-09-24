<?php

namespace App\Models;

use Database\Factories\ConversationFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Platform-scoped message thread (phase-3d; docs/database.md).
 *
 * Identity: ULID `public_id` (sent to clients); dedup: UNIQUE
 * (platform_id, participant_key) where participant_key is the normalized sorted
 * pair of map ids — so resolving the same pair always returns the same
 * conversation, regardless of argument order. Lifecycle is status-driven
 * (active/archived/closed); NO soft delete (keeps the dedup key authoritative).
 */
class Conversation extends Model
{
    /** @use HasFactory<ConversationFactory> */
    use HasFactory, HasUlids;

    /**
     * ULID columns — only `public_id` is exposed to clients.
     *
     * @return list<string>
     */
    public function uniqueIds(): array
    {
        return ['public_id'];
    }

    public const STATUS_ACTIVE = 'active';

    public const STATUS_ARCHIVED = 'archived';

    public const STATUS_CLOSED = 'closed';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'platform_id',
        'participant_key',
        'status',
        'last_message_id',
        'last_message_at',
        'last_message_excerpt',
        'last_message_sender_external_user_map_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'string',
            'last_message_at' => 'datetime',
        ];
    }

    /**
     * The owning platform — the isolation root for every lookup.
     *
     * @return BelongsTo<Platform, $this>
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * The participants (two rows at MVP; membership gate for authorization).
     *
     * @return HasMany<ConversationParticipant, $this>
     */
    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    /**
     * All messages in the conversation (history/cursor pagination).
     *
     * @return HasMany<Message, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * The most recent message (denormalized reference kept in sync on send).
     *
     * @return BelongsTo<Message, $this>
     */
    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    /**
     * The external user who sent the last message (denormalized reference).
     *
     * @return BelongsTo<ExternalUserMap, $this>
     */
    public function lastMessageSender(): BelongsTo
    {
        return $this->belongsTo(ExternalUserMap::class, 'last_message_sender_external_user_map_id');
    }
}
