<?php

namespace App\Http\Resources;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A conversation as seen from the ACTING participant's POV
 * (docs/integration-api.md §Conversations + docs/realtime-chat.md §Unread
 * Counts). Read state (`unread_count`, `last_read_at`) is per-participant, so
 * the resource is built with the acting seat.
 */
class ConversationResource extends JsonResource
{
    /**
     * @param  Conversation  $resource
     * @param  ConversationParticipant|null  $actingParticipant  the caller's seat
     */
    public function __construct(
        $resource,
        public ?ConversationParticipant $actingParticipant = null,
    ) {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        /** @var Conversation $conversation */
        $conversation = $this->resource;

        $lastMessage = $conversation->relationLoaded('lastMessage') ? $conversation->lastMessage : null;
        $lastSender = $conversation->relationLoaded('lastMessageSender') && $conversation->lastMessageSender !== null
            ? $conversation->lastMessageSender
            : null;

        return [
            'id' => $conversation->public_id,
            'status' => $conversation->status,
            'participants' => $conversation->relationLoaded('participants')
                ? ConversationParticipantResource::collection($conversation->participants)
                : [],
            'last_message' => $lastMessage !== null ? [
                'excerpt' => $conversation->last_message_excerpt,
                'sent_at' => $conversation->last_message_at?->toISOString(),
                'sender_id' => $lastSender?->external_user_id,
            ] : null,
            'unread_count' => $this->actingParticipant->unread_count ?? 0,
            'last_read_at' => $this->actingParticipant?->last_read_at?->toISOString(),
            'empty_msg' => $lastMessage === null ? 'No messages yet.' : null,
            'created_at' => $conversation->created_at?->toISOString(),
        ];
    }
}
