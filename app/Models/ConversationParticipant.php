<?php

namespace App\Models;

use Database\Factories\ConversationParticipantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Conversation membership + per-user read state (phase-3d).
 *
 * One seat per (conversation, external user map) — UNIQUE constraint backed.
 * Read state lives here so unread listing is O(1) and is updated transactionally
 * with message send (increment) and read (reset to 0) — it never drifts.
 */
class ConversationParticipant extends Model
{
    /** @use HasFactory<ConversationParticipantFactory> */
    use HasFactory;

    protected $table = 'conversation_participants';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'conversation_id',
        'platform_id',
        'external_user_map_id',
        'last_read_message_id',
        'last_read_at',
        'unread_count',
        'joined_at',
        'left_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_read_message_id' => 'integer',
            'last_read_at' => 'datetime',
            'unread_count' => 'integer',
            'joined_at' => 'datetime',
            'left_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Conversation, $this>
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * @return BelongsTo<Platform, $this>
     */
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * The external user occupying this seat (platform-scoped identity map).
     *
     * @return BelongsTo<ExternalUserMap, $this>
     */
    public function externalUserMap(): BelongsTo
    {
        return $this->belongsTo(ExternalUserMap::class, 'external_user_map_id');
    }
}
