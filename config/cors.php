<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CORS (Phase 4 — widget embedding)
    |--------------------------------------------------------------------------
    |
    | Scoped to the WIDGET routes ONLY. Everything else on the API surface stays
    | CORS-free, so a browser on an unrelated origin cannot read it (defense in
    | depth on top of the bearer-token requirement).
    |
    | The widget is symmetric-token authenticated (`Authorization: Bearer <widget
    | session PASETO>`, no cookies, supports_credentials=false), so a wildcard
    | origin here is safe: an origin without a minted token still gets nothing.
    | Operators can further restrict which origins may actually receive responses
    | by setting `allowed_origins` on the platform integration — that rule is
    | enforced per-request by App\Http\Middleware\RestrictWidgetOrigins.
    |
    | `allowed_headers: *` is required because the widget sends the
    | `Authorization` bearer header on cross-origin requests (preflight).
    |
    */

    'paths' => ['api/v1/widget/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['X-RateLimit-Limit', 'X-RateLimit-Remaining'],

    'max_age' => 0,

    'supports_credentials' => false,

];
