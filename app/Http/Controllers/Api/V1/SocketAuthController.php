<?php

namespace App\Http\Controllers\Api\V1;

use App\Chat\ChatChannelAuthorizer;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SocketAuthRequest;
use App\Services\ExternalUserContext;
use App\Services\ValidatedPasetoToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * POST /api/v1/chat/socket/auth (Phase 3E).
 *
 * Authenticates a realtime channel subscription: verifies the platform PASETO
 * token + the `X-External-User-Id` actor, then lets ChatChannelAuthorizer decide
 * membership (private conversation) or platform membership (presence). A signed
 * Pusher-protocol `auth` is returned for the widget to present to the realtime
 * server. Denials are 403 CHANNEL_DENIED — never existence-disclosing 404s.
 */
class SocketAuthController extends Controller
{
    public function __construct(
        private readonly ChatChannelAuthorizer $authorizer,
        private readonly ExternalUserContext $userContext,
    ) {}

    public function __invoke(SocketAuthRequest $request): JsonResponse
    {
        /** @var ValidatedPasetoToken|null $validated */
        $validated = $request->attributes->get('paseto');
        if (! $validated instanceof ValidatedPasetoToken) {
            throw new ApiException('INVALID_TOKEN', 'Missing platform context.', 401);
        }

        $actor = $this->userContext->resolve($request, $validated->platform);

        $allowed = $this->authorizer->authorize(
            $validated->platform,
            $actor,
            (string) $request->validated('socket_id'),
            (string) $request->validated('channel_name'),
        );

        return response()->json([
            'success' => true,
            'data' => [
                'authorized' => true,
                'auth' => $allowed['auth'],
                'channel_data' => $allowed['channel_data'],
            ],
            'meta' => ['request_id' => (string) Str::uuid()],
        ], 200);
    }
}
