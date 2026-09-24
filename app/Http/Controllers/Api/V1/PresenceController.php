<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\PresenceHeartbeatRequest;
use App\Services\ExternalUserContext;
use App\Services\ExternalUserService;
use App\Services\PresenceService;
use App\Services\RealtimeBroadcaster;
use App\Services\ValidatedPasetoToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Presence endpoints (Phase 3E).
 *
 * Presence is DB-resident and REST-first: heartbeats and reads work without any
 * realtime infrastructure. When realtime is enabled, transition events
 * (user.online / user.offline) are broadcast on the platform presence channel.
 */
class PresenceController extends Controller
{
    public function __construct(
        private readonly PresenceService $presence,
        private readonly ExternalUserContext $userContext,
        private readonly ExternalUserService $users,
        private readonly RealtimeBroadcaster $realtime,
    ) {}

    /**
     * POST /api/v1/chat/presence — heartbeat / offline the ACTING user.
     */
    public function heartbeat(PresenceHeartbeatRequest $request): JsonResponse
    {
        /** @var ValidatedPasetoToken|null $validated */
        $validated = $request->attributes->get('paseto');
        if (! $validated instanceof ValidatedPasetoToken) {
            throw new ApiException('INVALID_TOKEN', 'Missing platform context.', 401);
        }

        $actor = $this->userContext->resolve($request, $validated->platform);

        $state = $this->presence->heartbeat(
            $validated->platform,
            $actor,
            $request->validated('status'),
        );

        return response()->json([
            'success' => true,
            'data' => [
                'external_user_id' => $state['external_user_id'],
                'presence_status' => $state['presence_status'],
                'presence_seen_at' => $state['presence_seen_at']->toISOString(),
                'changed' => $state['changed'],
            ],
            'meta' => [
                'request_id' => (string) Str::uuid(),
                'offline_after_seconds' => $state['offline_after_seconds'],
                'realtime_enabled' => $this->realtime->enabled(),
            ],
        ], 200);
    }

    /**
     * GET /api/v1/chat/presence/{external_user_id} — effective presence of a
     * (platform-mapped) user. Works as the polling fallback when realtime is off.
     */
    public function show(Request $request, string $externalUserId): JsonResponse
    {
        /** @var ValidatedPasetoToken|null $validated */
        $validated = $request->attributes->get('paseto');
        if (! $validated instanceof ValidatedPasetoToken) {
            throw new ApiException('INVALID_TOKEN', 'Missing platform context.', 401);
        }

        $map = $this->users->resolve($validated->platform, $externalUserId);

        if ($map === null) {
            throw new ApiException('NOT_FOUND', 'No identity mapping found for this external user.', 404);
        }

        $state = $this->presence->state($validated->platform, $map);

        return response()->json([
            'success' => true,
            'data' => [
                'external_user_id' => $state['external_user_id'],
                'presence_status' => $state['presence_status'],
                'presence_seen_at' => $state['presence_seen_at']?->toISOString(),
            ],
            'meta' => ['request_id' => (string) Str::uuid()],
        ], 200);
    }

    /**
     * GET /api/v1/chat/presence/me — effective presence of the ACTING user
     * (widget convenience; identity always comes from the verified token).
     */
    public function me(Request $request): JsonResponse
    {
        /** @var ValidatedPasetoToken|null $validated */
        $validated = $request->attributes->get('paseto');
        if (! $validated instanceof ValidatedPasetoToken) {
            throw new ApiException('INVALID_TOKEN', 'Missing platform context.', 401);
        }

        $actor = $this->userContext->resolve($request, $validated->platform);
        $state = $this->presence->state($validated->platform, $actor);

        return response()->json([
            'success' => true,
            'data' => [
                'external_user_id' => $state['external_user_id'],
                'presence_status' => $state['presence_status'],
                'presence_seen_at' => $state['presence_seen_at']?->toISOString(),
            ],
            'meta' => ['request_id' => (string) Str::uuid()],
        ], 200);
    }
}
