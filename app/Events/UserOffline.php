<?php

namespace App\Events;

/**
 * Fired on a user presence transition to OFFLINE (explicit offline heartbeat,
 * stale-heartbeat sweep or lazy read) — Phase 3E.
 */
class UserOffline extends UserPresenceChanged
{
    public function broadcastAs(): string
    {
        return 'user.offline';
    }
}
