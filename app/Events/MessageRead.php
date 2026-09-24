<?php

namespace App\Events;

use App\Chat\ChatChannels;
use App\Models\Conversation;
use App\Models\ExternalUserMap;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Carbon;

/**
 * Fired after a participant's read cursor has advanced (post-commit) — Phase 3E.
 *
 * Carries the READER's external user id + the advance-only cursor so the other
 * participant can clear its UI unread badge; `unread_count` is the reader's
 * residual count (always 0 by the mark-read contract).
 */
class MessageRead extends RealtimeEvent
{
    public function __construct(
        public readonly Conversation $conversation,
        public readonly ExternalUserMap $reader,
        public readonly ?int $lastReadMessageId,
        public readonly int $unreadCount,
        public readonly Carbon $readAt,
    ) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(ChatChannels::conversationTransport($this->conversation)),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.read';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversation->public_id,
            'reader_id' => $this->reader->external_user_id,
            'last_read_message_id' => $this->lastReadMessageId,
            'unread_count' => $this->unreadCount,
            'read_at' => $this->readAt->toISOString(),
        ];
    }
}
