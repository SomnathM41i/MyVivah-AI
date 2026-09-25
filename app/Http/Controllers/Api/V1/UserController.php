<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserVerifyRequest;
use App\Http\Requests\WidgetUserSearchRequest;
use App\Http\Resources\ExternalUserReferenceResource;
use App\Services\ExternalPlatformUserSearch;
use App\Services\ExternalUserService;
use App\Services\ValidatedPasetoToken;
use App\Services\ValidatedWidgetSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * External-user identity endpoints (phase-3a-api-integration-plan.md §9).
 *
 * Platform context ALWAYS comes from the verified PASETO token — never from the
 * body/path — so a caller can only resolve/upsert users inside its own namespace
 * (strict platform isolation). Responses carry only the integration-required
 * reference data; the external platform remains the profile source of truth.
 */
class UserController extends Controller
{
    public function __construct(
        private readonly ExternalUserService $users,
        private readonly ExternalPlatformUserSearch $search,
    ) {}

    /**
     * POST /api/v1/users/verify — create/update an external→local identity map.
     *
     * Body: { external_user_id (required), local_public_id (optional MyVivahAI
     * user ULID to link) }. Returns the local reference for future chat identity.
     */
    public function verify(UserVerifyRequest $request): JsonResponse
    {
        /** @var ValidatedPasetoToken|null $validated */
        $validated = $request->attributes->get('paseto');
        if (! $validated instanceof ValidatedPasetoToken) {
            throw new ApiException('INVALID_TOKEN', 'Missing platform context.', 401);
        }

        $externalUserId = (string) $request->validated('external_user_id');
        $localPublicId = $request->validated('local_public_id');

        $map = $this->users->resolveOrCreate(
            $validated->platform,
            $externalUserId,
            is_string($localPublicId) && $localPublicId !== '' ? $localPublicId : null,
            null,
        );

        return response()->json([
            'success' => true,
            'data' => new ExternalUserReferenceResource($map),
            'meta' => [
                'request_id' => (string) Str::uuid(),
                'created' => $map->wasRecentlyCreated,
            ],
        ], 200);
    }

    /**
     * GET /api/v1/users/{external_user_id} — resolve MyVivahAI-held identity data.
     *
     * Platform-scoped: a mapped user is only visible to the platform that owns
     * the mapping. Returns 404 NOT_FOUND when absent.
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
            throw new ApiException(
                'NOT_FOUND',
                'No identity mapping found for this external user.',
                404
            );
        }

        // Sync contract: a successful resolution marks the user as seen.
        $map->forceFill(['last_seen_at' => now()])->save();
        $map->refresh();

        return response()->json([
            'success' => true,
            'data' => new ExternalUserReferenceResource($map),
            'meta' => ['request_id' => (string) Str::uuid()],
        ], 200);
    }

    /**
     * GET /api/v1/integration/users — paginated platform-scoped identity
     * references for reconciliation (realtime_chat:read) and widget user
     * search (Phase 4). Only the calling platform's own maps are ever visible.
     *
     * `?q=` is an optional substring filter over `external_user_id` (escaped
     * so LIKE wildcards in the query are treated literally), bounded by
     * config('widget.search.max_q_length').
     */
    public function index(Request $request): JsonResponse
    {
        /** @var ValidatedPasetoToken|null $validated */
        $validated = $request->attributes->get('paseto');
        if (! $validated instanceof ValidatedPasetoToken) {
            throw new ApiException('INVALID_TOKEN', 'Missing platform context.', 401);
        }

        $perPage = max(1, min(100, (int) $request->query('per_page', 20)));

        $query = $validated->platform->externalUserMaps();

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $q = mb_substr($q, 0, (int) config('widget.search.max_q_length', 255));
            // Escaped literal `%`/`_`/`\` + an explicit ESCAPE clause so both
            // MySQL and SQLite treat them as literals (SQLite has no default
            // escape character for LIKE).
            $query->whereRaw(
                'external_user_id like ? escape ?',
                ['%'.addcslashes($q, '%_\\').'%', '\\'],
            );
        }

        $page = $query
            ->latest('synced_at')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'success' => true,
            'data' => ExternalUserReferenceResource::collection($page),
            'meta' => [
                'request_id' => (string) Str::uuid(),
                'pagination' => [
                    'total' => $page->total(),
                    'per_page' => $page->perPage(),
                    'current_page' => $page->currentPage(),
                    'last_page' => $page->lastPage(),
                    'has_more_pages' => $page->hasMorePages(),
                ],
            ],
        ], 200);
    }

    /** GET /api/v1/widget/users/search — delegates to the client's user directory. */
    public function search(WidgetUserSearchRequest $request): JsonResponse
    {
        $widget = $request->attributes->get('widget_session');
        if (! $widget instanceof ValidatedWidgetSession) {
            throw new ApiException('INVALID_TOKEN', 'Missing widget session.', 401);
        }
        $result = $this->search->search(
            $widget->platform,
            $widget->externalUserId,
            trim((string) $request->validated('q')),
            (int) $request->validated('limit', 20),
            $request->validated('cursor'),
        );

        return response()->json(['success' => true, 'data' => ['results' => $result['results'], 'pagination' => ['next_cursor' => $result['next_cursor'], 'has_more' => $result['has_more']]], 'meta' => ['request_id' => (string) Str::uuid()]]);
    }
}
