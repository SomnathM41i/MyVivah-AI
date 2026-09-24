<?php

namespace App\Services;

use App\Events\ConversationUpdated;
use App\Events\MessageCreated;
use App\Events\MessageRead;
use App\Events\UserOffline;
use App\Events\UserOnline;
use App\Models\Conversation;
use App\Models\ExternalUserMap;
use App\Models\Message;
use App\Models\Platform;
use Illuminate\Support\Carbon;

/**
 * Centralized realtime dispatch (Phase 3E).
 *
 * Every method first honors config('chat.realtime.enabled'): when realtime is
 * disabled (the shared-hosting default) nothing is constructed or dispatched —
 * the REST write already happened and stays fully functional. When enabled,
 * events fire AFTER the service's own DB commit (strict call-site ordering) and
 * synchronously (ShouldBroadcastNow); failures are rescued, so a dead realtime
 * transport can never break a REST request.
 */
class RealtimeBroadcaster
{
    public function messageCreated(Conversation $conversation, Message $message, ExternalUserMap $sender): void
    {
        if (! $this->enabled()) {
            return;
        }

        event(new MessageCreated($conversation, $message, $sender));
    }

    /**
     * Broadcast the conversation summary. Loads the (small) participant set when
     * realtime is on so the payload can echo platform external user ids.
     */
    public function conversationUpdated(Conversation $conversation, string $reason = 'updated'): void
    {
        if (! $this->enabled()) {
            return;
        }

        if (! $conversation->relationLoaded('participants')) {
            $conversation->load('participants.externalUserMap');
        }
        if (! $conversation->relationLoaded('lastMessageSender')) {
            $conversation->load('lastMessageSender');
        }

        event(new ConversationUpdated($conversation, $reason));
    }

    public function messageRead(
        Conversation $conversation,
        ExternalUserMap $reader,
        ?int $lastReadMessageId,
        int $unreadCount,
        Carbon $readAt,
    ): void {
        if (! $this->enabled()) {
            return;
        }

        event(new MessageRead($conversation, $reader, $lastReadMessageId, $unreadCount, $readAt));
    }

    /**
     * Presence transition (online/offline). Callers invoke this ONLY on actual
     * status CHANGES; repeated same-status heartbeats must not re-fire.
     */
    public function presenceChanged(Platform $platform, ExternalUserMap $user, string $status, Carbon $seenAt): void
    {
        if (! $this->enabled()) {
            return;
        }

        $event = $status === ExternalUserMap::PRESENCE_ONLINE
            ? new UserOnline($platform, $user, $status, $seenAt)
            : new UserOffline($platform, $user, $status, $seenAt);

        event($event);
    }

    public function enabled(): bool
    {
        return (bool) config('chat.realtime.enabled', false);
    }
}
