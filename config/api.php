<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Per-platform API rate limit (default)
    |--------------------------------------------------------------------------
    |
    | Default requests-per-minute cap applied to the authenticated v1 routes via
    | the per-platform policy (App\Http\Middleware\EnforcePlatformRateLimit).
    | Each platform's own ceiling can be tuned per row via
    | `platform_integrations.rate_limit_per_minute` (default 60 at migration
    | time); that value wins when present. The cap is keyed by the verified
    | platform id, so one platform can never consume another platform's quota
    | (phase-3a §14/§15).
    |
    */

    'rate_limit_per_minute' => (int) env('API_RATE_LIMIT_PER_MINUTE', 60),

];
