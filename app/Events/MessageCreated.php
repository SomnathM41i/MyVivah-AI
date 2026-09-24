<?php

namespace App\Events;

use App\Chat\ChatChannels;
use App\Models\Conversation;
use App\Models\ExternalUserMap;
use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;

/**
 * Fired once a message has been PERSISTED (post-commit) — Phase 3E.
 *
 * Channel: private-chat.{conversation public_id}. Payload is the minimum the
 * widget needs to render the bubble; `sender` is the platform's own external
 * user id (it always knows its own identity, we never leak profile data).
 */
class MessageCreated extends RealtimeEvent
{
    public function __construct(
        public readonly Conversation $conversation,
        public readonly Message $message,
        public readonly ExternalUserMap $sender,
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
        return 'message.created';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversation->public_id,
            'message_id' => $this->message->public_id,
            'sender_id' => $this->sender->external_user_id,
            'type' => $this->message->type,
            'status' => $this->message->status,
            'content' => $this->message->content,
            'client_message_id' => $this->message->client_message_id,
            'sent_at' => $this->message->created_at?->toISOString(),
        ];
    }
}
