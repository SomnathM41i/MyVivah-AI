<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\WidgetSessionRequest;
use App\Services\ExternalUserService;
use App\Services\ValidatedPasetoToken;
use App\Services\ValidatedWidgetSession;
use App\Services\WidgetRealtimeConfig;
use App\Services\WidgetSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * POST /api/v1/widget/session — server-to-server widget bootstrap (Phase 4).
 *
 * Called with a PLATFORM token (must hold realtime_chat:write) + the external
 * user id of the specific logged-in user. The response is the ONLY thing a
 * platform's backend ever sends to the browser: a short-lived, single-user
 * widget session token plus public realtime coordinates. The platform's secret
 * never leaves its own server, and the browser can never mint identities itself.
 */
class WidgetSessionController extends Controller
{
    public function __construct(
        private readonly WidgetSessionService $sessions,
        private readonly ExternalUserService $users,
    ) {}

    public function __invoke(WidgetSessionRequest $request): JsonResponse
    {
        /** @var ValidatedPasetoToken|null $validated */
        $validated = $request->attributes->get('paseto');
        if (! $validated instanceof ValidatedPasetoToken) {
            throw new ApiException('INVALID_TOKEN', 'Missing platform context.', 401);
        }

        $externalUserId = (string) $request->validated('external_user_id');

        $map = $this->users->resolveOrCreate($validated->platform, $externalUserId);

        $issued = $this->sessions->issue(
            $validated->integration,
            $validated->apiKey,
            $externalUserId,
        );

        return response()->json([
            'success' => true,
            'data' => $this->sessions->present($issued, $map, WidgetRealtimeConfig::for()),
            'meta' => ['request_id' => (string) Str::uuid()],
        ], 200);
    }

    /**
     * POST /api/v1/widget/session/revoke — revoke the current widget token.
     *
     * Protected by the WIDGET token stack (not the platform one): lets a
     * platform's backend revoke a widget session at logout/suspension using the
     * very token that governs it.
     */
    public function revoke(): JsonResponse
    {
        /** @var ValidatedWidgetSession|null $widget */
        $widget = request()->attributes->get('widget_session');
        if (! $widget instanceof ValidatedWidgetSession) {
            throw new ApiException('INVALID_TOKEN', 'Missing widget session context.', 401);
        }

        $this->sessions->revoke($widget);

        return response()->json([
            'success' => true,
            'data' => ['revoked_jti' => $widget->jti],
            'meta' => ['request_id' => (string) Str::uuid()],
        ], 200);
    }
}
