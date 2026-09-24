<?php

namespace App\Events;

use App\Chat\ChatChannels;
use App\Models\Conversation;
use App\Models\ExternalUserMap;
use Illuminate\Broadcasting\PrivateChannel;

/**
 * Fired when a conversation is created or its denormalized summary changes
 * (post-commit) — Phase 3E. Clients use it to refresh conversation-list rows;
 * message sends ALSO produce this after the last-message metadata updates.
 *
 * `reason` is a client hint: `created` (new thread) or `updated` (metadata
 * changed). Participants are echoed as the platform's external user ids only.
 */
class ConversationUpdated extends RealtimeEvent
{
    public function __construct(
        public readonly Conversation $conversation,
        public readonly string $reason = 'updated',
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
        return 'conversation.updated';
    }

    public function broadcastWith(): array
    {
        /** @var array<int, string> $participants */
        $participants = $this->conversation->relationLoaded('participants')
            ? $this->conversation->participants
                ->map(fn ($participant) => $participant->externalUserMap instanceof ExternalUserMap
                    ? $participant->externalUserMap->external_user_id
                    : (string) $participant->external_user_map_id)
                ->values()
                ->all()
            : [];

        $lastSenderId = $this->conversation->relationLoaded('lastMessageSender')
            ? ($this->conversation->lastMessageSender instanceof ExternalUserMap
                ? $this->conversation->lastMessageSender->external_user_id
                : null)
            : null;

        return [
            'conversation_id' => $this->conversation->public_id,
            'reason' => $this->reason,
            'status' => $this->conversation->status,
            'participants' => $participants,
            'last_message' => $this->conversation->last_message_at !== null ? [
                'excerpt' => $this->conversation->last_message_excerpt,
                'sent_at' => $this->conversation->last_message_at->toISOString(),
                'sender_id' => $lastSenderId,
            ] : null,
            'updated_at' => $this->conversation->updated_at?->toISOString(),
        ];
    }
}
