<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PASETO version
    |--------------------------------------------------------------------------
    |
    | MVP is locked to `v4.local` (symmetric AEAD, XChaCha20-Poly1305) per
    | ADR-013 / phase-3a-api-integration-plan.md §6.1. The parser allow-list is
    | built in PasetoTokenService; nothing else must ever be accepted.
    |
    */

    'version' => 'v4.local',

    /*
    |--------------------------------------------------------------------------
    | Token lifetime
    |--------------------------------------------------------------------------
    |
    | SHORT-LIVED machine-to-machine tokens. `default_ttl_seconds` is used when
    | an integration has no per-key override; `max_ttl_seconds` is an absolute
    | ceiling — exp − iat may never exceed it (plan §6.2: ≤ 1h at MVP). A longer
    | value here is allowed only when the deployment risk model is reviewed.
    |
    */

    'default_ttl_seconds' => (int) env('PASETO_DEFAULT_TTL_SECONDS', 3600),

    'max_ttl_seconds' => (int) env('PASETO_MAX_TTL_SECONDS', 3600),

    /*
    |--------------------------------------------------------------------------
    | Key rotation grace
    |--------------------------------------------------------------------------
    |
    | Rotated (demoted) keys remain valid for this many seconds after rotation
    | so in-flight tokens do not break (plan §6.3: grace, default 24h). The key
    | is then hard-revoked. Rotation/grace is enforced in the service layer.
    |
    */

    'rotation_grace_seconds' => (int) env('PASETO_ROTATION_GRACE_SECONDS', 86400),

    /*
    |--------------------------------------------------------------------------
    | Token retry / issuance throttling
    |--------------------------------------------------------------------------
    |
    | The token-issuance endpoint is credential-based and must be throttled to
    | slow brute force (plan §14: rate limiting + throttling on auth endpoints).
    |
    */

    'issue_throttle' => '5,1', // 5 attempts per 1 minute per client IP

    /*
    |--------------------------------------------------------------------------
    | Claim constants
    |--------------------------------------------------------------------------
    |
    | `audiences` — the `aud` claim format `platform:{slug}` binds a token to one
    | external matrimony platform; `subject`/`platform_id` carry the platform's
    | ULID. `jti` is a random UUID used for revocation.
    |
    */

    'audience_prefix' => 'platform:',

    /*
    |--------------------------------------------------------------------------
    | Bootstrap scopes
    |--------------------------------------------------------------------------
    |
    | Every issued token carries `authentication` so platform self-describe /
    | status endpoints (auth-only) can be protected. Service scopes are derived
    | from `platform_service_access` entitlements (T8) at issue time.
    |
    */

    'authentication_scope' => 'authentication',

    /*
    |--------------------------------------------------------------------------
    | Service → scope mapping
    |--------------------------------------------------------------------------
    |
    | When a platform holds a live entitlement (`platform_service_access`) for a
    | service, the service's scopes below are granted on issued tokens. Scopes
    | are coarse per service (`realtime_chat:read/write`) per phase-3a §20/4.
    |
    */

    'service_scopes' => [
        'realtime_chat' => [
            'realtime_chat:read',
            'realtime_chat:write',
        ],
    ],

];
