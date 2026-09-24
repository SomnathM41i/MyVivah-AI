<?php

namespace App\Events;

/**
 * Fired on a user presence transition to ONLINE (reconnect/heartbeat) — Phase 3E.
 */
class UserOnline extends UserPresenceChanged
{
    public function broadcastAs(): string
    {
        return 'user.online';
    }
}
