<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Broadcasting\ShouldRescue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Base for every MyVivahAI realtime chat event (Phase 3E).
 *
 * Behaviour guarantees:
 *   - ShouldBroadcastNow   — dispatched INLINE (no queue worker required), right
 *                            after the REST request that caused it commits.
 *   - Commit ordering      — every call site (ChatMessageService,
 *                            ChatConversationService, PresenceService) fires via
 *                            RealtimeBroadcaster AFTER its own DB transaction
 *                            has closed, so MySQL (source of truth) always holds
 *                            the row BEFORE any broadcast. No queue worker, no
 *                            transaction-manager indirection.
 *   - ShouldRescue         — a dead/unreachable realtime transport throws a
 *                            BroadcastException that is swallowed + logged; the
 *                            REST request never fails because of realtime.
 *   - broadcastWhen        — global kill-switch shared by every event
 *                            (config('chat.realtime.enabled')), so a fully
 *                            REST-only deployment dispatches nothing at all.
 *
 * Payload rule (AGENTS.md §15 / security.md): events carry ONLY public ids and
 * minimal display data — never API secrets, PASETO tokens, private keys,
 * internal primary keys, full profiles or audit material.
 */
abstract class RealtimeEvent implements ShouldBroadcastNow, ShouldRescue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function broadcastWhen(): bool
    {
        return (bool) config('chat.realtime.enabled', false);
    }

    /**
     * The connection the broadcast runs on. Default (null) = the configured
     * BROADCAST_CONNECTION; when realtime is disabled nothing is queued at all.
     */
    public function broadcastConnections(): array
    {
        return [null];
    }
}
