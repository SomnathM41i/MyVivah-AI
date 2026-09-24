<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ChatConversationController;
use App\Http\Controllers\Api\V1\ChatMessageController;
use App\Http\Controllers\Api\V1\IntegrationController;
use App\Http\Controllers\Api\V1\PlatformController;
use App\Http\Controllers\Api\V1\PresenceController;
use App\Http\Controllers\Api\V1\SocketAuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\WidgetSessionController;
use App\Http\Middleware\EnforcePlatformRateLimit;
use App\Http\Middleware\EnsurePlatformAccess;
use App\Http\Middleware\LogApiAudit;
use App\Http\Middleware\ValidatePlatformToken;
use App\Http\Middleware\ValidateWidgetToken;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — v1 (phase-3a-api-integration-plan.md §9 / Phase 3C)
|--------------------------------------------------------------------------
|
| Versioned `/api/v1` prefix, additive-only. All endpoints (except token
| issuance) require `Authorization: Bearer <paseto v4.local>`. Authenticated
| calls run through, in order:
|   1. ValidatePlatformToken     — cryptographically verify + hydrate context,
|   2. EnsurePlatformAccess      — scope + live-entitlement gate,
|   3. LogApiAudit               — append-only audit (wraps everything below so
|                                  even rejected 429/4xx requests stay audited),
|   4. EnforcePlatformRateLimit  — PER-PLATFORM rate limit (isolation-safe).
|
| Note: the per-platform policy is enforced by a plain middleware class, NOT the
| `throttle:` alias — Laravel priority-sorts ThrottleRequests ahead of custom
| middleware, which would run it before the token context exists.
|
*/

// Shared authenticated+scoped middleware stack (order matters — see above).
$scoped = function (string $scopeDotted): array {
    return [
        ValidatePlatformToken::class,
        EnsurePlatformAccess::class.':'.$scopeDotted,
        LogApiAudit::class,
        EnforcePlatformRateLimit::class,
    ];
};

// WIDGET (browser) middleware stack (Phase 4). Same shape as the platform stack
// but terminates on ValidateWidgetToken: widget sessions are SHORT-LIVED tokens
// bound to ONE external user, so downstream scope/entitlement/audit/rate-limit
// behave identically. Per-origin CORS restriction is applied globally AFTER
// HandleCors in bootstrap/app.php (see RestrictWidgetOrigins).
$widgetScoped = function (string $scopeDotted): array {
    return [
        ValidateWidgetToken::class,
        EnsurePlatformAccess::class.':'.$scopeDotted,
        LogApiAudit::class,
        EnforcePlatformRateLimit::class,
    ];
};

$authentication = str_replace(':', '.', (string) config('paseto.authentication_scope', 'authentication'));

Route::prefix('v1')
    ->middleware('api')
    ->group(function () use ($scoped, $widgetScoped, $authentication): void {
        // Credential exchange → short-lived PASETO (throttled, phase-3a §14).
        Route::post('auth/token', [AuthController::class, 'issue'])
            ->middleware('throttle:paseto.issue')
            ->name('api.v1.auth.token');

        // Authenticated token lifecycle (revoke blacklists the presented jti).
        Route::post('auth/revoke', [AuthController::class, 'revoke'])
            ->middleware($scoped($authentication))
            ->name('api.v1.auth.revoke');

        // Platform self-describe + entitlements (authentication scope only).
        Route::get('platform/me', [PlatformController::class, 'me'])
            ->middleware($scoped($authentication))
            ->name('api.v1.platform.me');

        // Integration self-service config (Phase 3C): read + update the
        // operator-tunable connection fields (base_domain / allowed_origins).
        Route::get('integration/config', [IntegrationController::class, 'config'])
            ->middleware($scoped($authentication))
            ->name('api.v1.integration.config.show');

        Route::patch('integration/config', [IntegrationController::class, 'updateConfig'])
            ->middleware($scoped($authentication))
            ->name('api.v1.integration.config.update');

        // API-key lifecycle management (Phase 3C): list metadata / rotate / revoke.
        Route::get('integration/keys', [IntegrationController::class, 'keys'])
            ->middleware($scoped($authentication))
            ->name('api.v1.integration.keys.index');

        Route::post('integration/keys/rotate', [IntegrationController::class, 'rotate'])
            ->middleware($scoped($authentication))
            ->name('api.v1.integration.keys.rotate');

        Route::post('integration/keys/revoke', [IntegrationController::class, 'revoke'])
            ->middleware($scoped($authentication))
            ->name('api.v1.integration.keys.revoke');

        // External-user identity mapping (realtime_chat:write), resolution (read)
        // and platform-scoped reconciliation list (Phase 3C).
        // `users/verify` MUST register before `users/{external_user_id}` so the
        // literal segment wins over the wildcard.
        Route::post('users/verify', [UserController::class, 'verify'])
            ->middleware($scoped('realtime_chat.write'))
            ->name('api.v1.users.verify');

        Route::get('integration/users', [UserController::class, 'index'])
            ->middleware($scoped('realtime_chat.read'))
            ->name('api.v1.integration.users.index');

        Route::get('users/{external_user_id}', [UserController::class, 'show'])
            ->where('external_user_id', '[^/]+')
            ->middleware($scoped('realtime_chat.read'))
            ->name('api.v1.users.show');

        // Chat (Phase 3D): conversation resolve-or-create + list + detail (read
        // state per acting user), cursor-paginated history, idempotent send, and
        // advance-only mark-read. Deterministic two-party threads; every lookup
        // is platform-then-actor scoped (docs/realtime-chat.md, §REST API).
        Route::prefix('chat')->group(function () use ($scoped): void {
            Route::post('conversations', [ChatConversationController::class, 'store'])
                ->middleware($scoped('realtime_chat.write'))
                ->name('api.v1.chat.conversations.store');

            Route::get('conversations', [ChatConversationController::class, 'index'])
                ->middleware($scoped('realtime_chat.read'))
                ->name('api.v1.chat.conversations.index');

            Route::get('conversations/{conversation}', [ChatConversationController::class, 'show'])
                ->where('conversation', '[a-zA-Z0-9]+')
                ->middleware($scoped('realtime_chat.read'))
                ->name('api.v1.chat.conversations.show');

            Route::post('conversations/{conversation}/read', [ChatConversationController::class, 'markRead'])
                ->where('conversation', '[a-zA-Z0-9]+')
                ->middleware($scoped('realtime_chat.write'))
                ->name('api.v1.chat.conversations.read');

            Route::get('conversations/{conversation}/messages', [ChatMessageController::class, 'index'])
                ->where('conversation', '[a-zA-Z0-9]+')
                ->middleware($scoped('realtime_chat.read'))
                ->name('api.v1.chat.messages.index');

            Route::post('conversations/{conversation}/messages', [ChatMessageController::class, 'store'])
                ->where('conversation', '[a-zA-Z0-9]+')
                ->middleware($scoped('realtime_chat.write'))
                ->name('api.v1.chat.messages.store');

            // Presence (Phase 3E): REST-first heartbeats + reads — work with NO
            // realtime infra (DB-backed); transitions broadcast user.online /
            // user.offline on the platform presence channel when enabled.
            // `presence/me` is registered BEFORE the `{external_user_id}` wildcard
            // so the literal segment wins.
            Route::post('presence', [PresenceController::class, 'heartbeat'])
                ->middleware($scoped('realtime_chat.write'))
                ->name('api.v1.chat.presence.heartbeat');

            Route::get('presence/me', [PresenceController::class, 'me'])
                ->middleware($scoped('realtime_chat.read'))
                ->name('api.v1.chat.presence.me');

            Route::get('presence/{external_user_id}', [PresenceController::class, 'show'])
                ->where('external_user_id', '[^/]+')
                ->middleware($scoped('realtime_chat.read'))
                ->name('api.v1.chat.presence.show');

            // Realtime channel subscription auth (Phase 3E): platform token +
            // X-External-User-Id → signed Pusher-protocol auth for private/
            // presence channels (server-side authorization, always enforced).
            Route::post('socket/auth', SocketAuthController::class)
                ->middleware($scoped('realtime_chat.read'))
                ->name('api.v1.chat.socket.auth');
        });

        // WIDGET (Phase 4) — server-to-server bootstrap. The platform's backend
        // exchanges a platform PASETO (realtime_chat.write) for a short-lived,
        // single-user WIDGET session token. This is the ONLY place a widget
        // token is minted and it STAYS server-to-server via the platform stack.
        Route::post('widget/session', WidgetSessionController::class)
            ->middleware($scoped('realtime_chat.write'))
            ->name('api.v1.widget.session');

        // WIDGET browser endpoints (Phase 4) — short-lived widget session tokens.
        // Same controllers as the platform chat API; identity is token-bound so a
        // browser header can never override it. Always registered after
        // `widget/session` so the bootstrap route above wins for that literal path.
        Route::prefix('widget')->group(function () use ($widgetScoped): void {
            Route::get('integration/users', [UserController::class, 'index'])
                ->middleware($widgetScoped('realtime_chat.read'))
                ->name('api.v1.widget.users.index');

            Route::get('users/{external_user_id}', [UserController::class, 'show'])
                ->where('external_user_id', '[^/]+')
                ->middleware($widgetScoped('realtime_chat.read'))
                ->name('api.v1.widget.users.show');

            Route::post('session/revoke', [WidgetSessionController::class, 'revoke'])
                ->middleware($widgetScoped('realtime_chat.write'))
                ->name('api.v1.widget.session.revoke');

            Route::prefix('chat')->group(function () use ($widgetScoped): void {
                Route::post('conversations', [ChatConversationController::class, 'store'])
                    ->middleware($widgetScoped('realtime_chat.write'))
                    ->name('api.v1.widget.chat.conversations.store');

                Route::get('conversations', [ChatConversationController::class, 'index'])
                    ->middleware($widgetScoped('realtime_chat.read'))
                    ->name('api.v1.widget.chat.conversations.index');

                Route::get('conversations/{conversation}', [ChatConversationController::class, 'show'])
                    ->where('conversation', '[a-zA-Z0-9]+')
                    ->middleware($widgetScoped('realtime_chat.read'))
                    ->name('api.v1.widget.chat.conversations.show');

                Route::post('conversations/{conversation}/read', [ChatConversationController::class, 'markRead'])
                    ->where('conversation', '[a-zA-Z0-9]+')
                    ->middleware($widgetScoped('realtime_chat.write'))
                    ->name('api.v1.widget.chat.conversations.read');

                Route::get('conversations/{conversation}/messages', [ChatMessageController::class, 'index'])
                    ->where('conversation', '[a-zA-Z0-9]+')
                    ->middleware($widgetScoped('realtime_chat.read'))
                    ->name('api.v1.widget.chat.messages.index');

                Route::post('conversations/{conversation}/messages', [ChatMessageController::class, 'store'])
                    ->where('conversation', '[a-zA-Z0-9]+')
                    ->middleware($widgetScoped('realtime_chat.write'))
                    ->name('api.v1.widget.chat.messages.store');

                Route::post('presence', [PresenceController::class, 'heartbeat'])
                    ->middleware($widgetScoped('realtime_chat.write'))
                    ->name('api.v1.widget.chat.presence.heartbeat');

                Route::get('presence/me', [PresenceController::class, 'me'])
                    ->middleware($widgetScoped('realtime_chat.read'))
                    ->name('api.v1.widget.chat.presence.me');

                Route::get('presence/{external_user_id}', [PresenceController::class, 'show'])
                    ->where('external_user_id', '[^/]+')
                    ->middleware($widgetScoped('realtime_chat.read'))
                    ->name('api.v1.widget.chat.presence.show');

                Route::get('presence/me', [PresenceController::class, 'me'])
                    ->middleware($widgetScoped('realtime_chat.read'))
                    ->name('api.v1.widget.chat.presence.me');

                Route::post('socket/auth', SocketAuthController::class)
                    ->middleware($widgetScoped('realtime_chat.read'))
                    ->name('api.v1.widget.chat.socket.auth');
            });
        });
    });
