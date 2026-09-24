<?php

namespace App\Events;

use App\Chat\ChatChannels;
use App\Models\ExternalUserMap;
use App\Models\Platform;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Support\Carbon;

/**
 * Base for user.online / user.offline (Phase 3E).
 *
 * Channel: presence-chat.{platform public_id}. Emitted ONLY on status
 * TRANSITIONS (repeated heartbeats with the same status never re-fire), so a
 * client that reconnects sees a fresh user.online. Payload is minimal — the
 * platform's external user id + status + last-seen.
 */
abstract class UserPresenceChanged extends RealtimeEvent
{
    public function __construct(
        public readonly Platform $platform,
        public readonly ExternalUserMap $user,
        public readonly string $status,
        public readonly Carbon $seenAt,
    ) {}

    /**
     * @return array<int, PresenceChannel>
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel(ChatChannels::presenceTransport($this->platform)),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'platform_id' => $this->platform->public_id,
            'external_user_id' => $this->user->external_user_id,
            'status' => $this->status,
            'seen_at' => $this->seenAt->toISOString(),
        ];
    }
}
