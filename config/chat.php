<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Chat domain tunables (Phase 3D — REST foundation)
    |--------------------------------------------------------------------------
    |
    | All limits below are validated at the API layer (`ApiFormRequest` rules)
    | and/or enforced by App\Http\Controllers\Api\V1 chat controllers. They are
    | deliberately conservative defaults; raise them only with a tested reason.
    |
    */

    'conversation' => [
        // MVP is two-party conversations only (docs/realtime-chat.md §Scope:
        // group conversations are out of scope). Two-party keeps the
        // `participant_key` dedup key simple and deterministic.
        'participants' => (int) env('CHAT_CONVERSATION_PARTICIPANTS', 2),
        // Maximum length of a client-supplied external user id (matches the
        // `platform_external_user_map.external_user_id` VARCHAR(255)).
        'external_user_id_max' => 255,
    ],

    'message' => [
        'max_length' => (int) env('CHAT_MESSAGE_MAX_LENGTH', 4000),
        // Idempotency key: client-generated, must be stable per logical message.
        'client_id_min' => 8,
        'client_id_max' => 64,
        // Truncated preview persisted on the conversation row for fast lists.
        'excerpt_length' => (int) env('CHAT_MESSAGE_EXCERPT_LENGTH', 120),
    ],

    'history' => [
        // Cursor-based message history window (docs/realtime-chat.md §History).
        'default_limit' => 50,
        'max_limit' => 100,
    ],

    'list' => [
        'default_per_page' => 20,
        'max_per_page' => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Realtime (Phase 3E — OPTIONAL)
    |--------------------------------------------------------------------------
    |
    | Realtime never guards correctness: MySQL is the source of truth and every
    | broadcast fires ONLY AFTER a successful commit. When realtime is disabled
    | or unavailable, the REST endpoints above remain fully functional and the
    | widget falls back to polling history/presence endpoints.
    |
    | `enabled` gates event dispatch (config('chat.realtime.enabled')). Events
    | implement ShouldBroadcastNow (inline, no queue worker needed) + ShouldRescue
    | (a failed/unreachable transport is logged and swallowed — never a 500).
    |
    | Channels (pusher naming conventions; see App\Chat\ChatChannels):
    |   private-chat.{conversation public_id}   — message.created / message.read
    |                                            / conversation.updated
    |   presence-chat.{platform public_id}      — user.online / user.offline
    |
    */

    'realtime' => [
        'enabled' => (bool) env('CHAT_REALTIME_ENABLED', false),
        // Which realtime transport the widget browser client should talk to.
        // One of: reverb | pusher | soketi. When unset, derived from
        // config/broadcasting.php::default (which stays a server-side default).
        'connection' => (string) env('CHAT_REALTIME_CONNECTION', 'reverb'),
        // Pusher-protocol credentials used to SIGN the socket-auth response.
        // Clients then present this signature to the realtime server, which
        // re-verifies it with the SAME app secret — so the signature is derived
        // from secret server config, never exposed in any payload.
        'app_key' => (string) env('REVERB_APP_KEY', ''),
        'app_secret' => (string) env('REVERB_APP_SECRET', ''),
        'channel_names' => [
            'private' => 'private-chat',
            'presence' => 'presence-chat',
        ],
        // Browser-facing connection coordinates (all public-safe):
        'scheme' => (string) env('CHAT_REALTIME_SCHEME', 'wss'),
        'host' => (string) env('CHAT_REALTIME_HOST', '127.0.0.1'),
        'port' => (int) env('CHAT_REALTIME_PORT', 443),
        // For pusher connection: the ws-{cluster}.pusher.com hostname the widget joins.
        'pusher_cluster' => (string) env('CHAT_REALTIME_PUSHER_CLUSTER', 'mt1'),
        // A user without a heartbeat inside this window is treated offline.
        // No Redis needed: staleness is computed from the DB `presence_seen_at`.
        'presence_offline_after_seconds' => (int) env('CHAT_PRESENCE_OFFLINE_AFTER', 90),
        // chat:presence-sweep marks stale online rows offline + broadcasts
        // user.offline. Expected to run from cron (shared-hosting friendly).
        'presence_sweep_idle_after_seconds' => (int) env('CHAT_PRESENCE_SWEEP_IDLE_AFTER', 120),
        'presence_sweep_limit' => (int) env('CHAT_PRESENCE_SWEEP_LIMIT', 1000),
    ],

];
