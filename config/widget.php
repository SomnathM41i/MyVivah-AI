<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Widget session tokens (Phase 4)
    |--------------------------------------------------------------------------
    |
    | A widget session token is a short-lived v4.local PASETO issued ONLY by the
    | server-to-server POST /api/v1/widget/session endpoint (itself protected by
    | a platform token + realtime_chat:write scope). It is bound to ONE external
    | user inside ONE platform: `aud = "widget:{slug}"`, `sub = external_user_id`,
    | `platform_id` + `external_user_id` claims, `kid` footer — and it carries
    | exactly the two chat scopes the widget needs. Tokens expire fast and are
    | jti-revocable via the shared revocation cache, so a browser-leaked token
    | has a tiny blast radius.
    |
    | `ttl_seconds` is the lifetime minted at issue; `max_ttl_seconds` is a hard
    | ceiling — exp − iat may never exceed it.
    |
    */

    'session' => [
        'ttl_seconds' => (int) env('WIDGET_SESSION_TTL_SECONDS', 900),
        'max_ttl_seconds' => (int) env('WIDGET_SESSION_MAX_TTL_SECONDS', 1800),
    ],

    /*
    |--------------------------------------------------------------------------
    | Widget audience prefix
    |--------------------------------------------------------------------------
    |
    | Deliberately DIFFERENT from the platform token prefix (`platform:`). A
    | widget token can NEVER satisfy the platform-token claim checks (and vice
    | versa), so one token kind can never be replayed against the other's routes.
    |
    */

    'audience_prefix' => 'widget:',

    /*
    |--------------------------------------------------------------------------
    | Widget user search (identity-map search)
    |--------------------------------------------------------------------------
    |
    | The widget searches the platform's OWN `platform_external_user_map`
    | namespace (the identity set the platform verified). `q` is a substring
    | filter on `external_user_id`; bounds below keep the lookup cheap.
    |
    */

    'search' => [
        'max_q_length' => (int) env('WIDGET_SEARCH_MAX_Q_LENGTH', 255),
    ],

];
