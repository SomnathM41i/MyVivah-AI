<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConversationsIndexRequest;
use App\Http\Requests\CreateConversationRequest;
use App\Http\Requests\MarkReadRequest;
use App\Http\Resources\ConversationParticipantResource;
use App\Http\Resources\ConversationResource;
use App\Models\Conversation;
use App\Services\ChatConversationService;
use App\Services\ExternalPlatformUserSearch;
use App\Services\ExternalUserContext;
use App\Services\ExternalUserService;
use App\Services\ValidatedPasetoToken;
use App\Services\ValidatedWidgetSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Conversation endpoints (phase-3d; docs/realtime-chat.md §Conversation Model).
 *
 * Platform context ALWAYS comes from the verified PASETO token; the ACTING user
 * always comes from `X-External-User-Id` (platform-scoped resolution — a value
 * that belongs to another platform can never resolve here). Every lookup is
 * platform-scoped FIRST, and non-membership is indistinguishable from absence
 * (404) so participation is never disclosed.
 */
class ChatConversationController extends Controller
{
    public function __construct(
        private readonly ChatConversationService $conversations,
        private readonly ExternalUserContext $userContext,
        private readonly ExternalUserService $users,
        private readonly ExternalPlatformUserSearch $search,
    ) {}

    /**
     * POST /api/v1/chat/conversations — resolve-or-create a two-party thread.
     *
     * Deterministic: the same pair of external user ids ALWAYS returns the same
     * thread (order-insensitive). No `X-External-User-Id` required — the initiator
     * is resolved implicitly as the platform's verified identity.
     *
     * WIDGET calls (Phase 4) are stricter: the token-bound actor (browser user)
     * MUST be one of the two participants — a browser can never open a thread
     * between two OTHER users.
     */
    public function store(CreateConversationRequest $request): JsonResponse
    {
        /** @var ValidatedPasetoToken|null $validated */
        $validated = $request->attributes->get('paseto');
        if (! $validated instanceof ValidatedPasetoToken) {
            throw new ApiException('INVALID_TOKEN', 'Missing platform context.', 401);
        }

        $widget = $request->attributes->get('widget_session');
        if ($widget instanceof ValidatedWidgetSession) {
            $candidate = $this->search->validateCandidate((string) $request->validated('candidate_token'), $validated->platform, $widget->externalUserId);
            $this->search->assertTargetEligible($validated->platform, $widget->externalUserId, $candidate['target_id']);
            $externalIds = [$widget->externalUserId, $candidate['target_id']];
        } else {
            /** @var list<string> $externalIds */
            $externalIds = $request->validated('participant_external_ids');
        }

        if ($widget instanceof ValidatedWidgetSession
            && ! in_array($widget->externalUserId, $externalIds, true)) {
            throw new ApiException(
                'VALIDATION_FAILED',
                'The acting user must be one of the conversation participants.',
                422
            );
        }

        $mapIds = [];
        foreach ($externalIds as $id) {
            $map = $this->users->resolveOrCreate($validated->platform, $id);
            $mapIds[] = $map->id;
        }

        $conversation = $this->conversations->resolveOrCreate($validated->platform, $mapIds);

        $conversation->load(['participants.externalUserMap']);
        $actingParticipant = $this->conversations->participant(
            $conversation,
            (int) $mapIds[0],
        );

        return response()->json([
            'success' => true,
            'data' => new ConversationResource($conversation, $actingParticipant),
            'meta' => [
                'request_id' => (string) Str::uuid(),
                'created' => $conversation->wasRecentlyCreated,
                'participants' => ConversationParticipantResource::collection(
                    $conversation->participants
                ),
            ],
        ], 200);
    }

    /**
     * GET /api/v1/chat/conversations — the acting user's conversations, ordered
     * by last-message time (empty threads last) with per-user read state.
     */
    public function index(ConversationsIndexRequest $request): JsonResponse
    {
        /** @var ValidatedPasetoToken|null $validated */
        $validated = $request->attributes->get('paseto');
        if (! $validated instanceof ValidatedPasetoToken) {
            throw new ApiException('INVALID_TOKEN', 'Missing platform context.', 401);
        }

        $actor = $this->userContext->resolve($request, $validated->platform);

        $perPage = max(1, min(
            (int) config('chat.list.max_per_page', 100),
            (int) $request->query('per_page', (int) config('chat.list.default_per_page', 20))
        ));

        $page = $validated->platform
            ->conversations()
            ->with(['lastMessage', 'lastMessageSender', 'participants.externalUserMap'])
            ->whereHas('participants', fn ($q) => $q->where('external_user_map_id', $actor->id))
            ->orderByRaw('CASE WHEN last_message_at IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('last_message_at')
            ->paginate($perPage)
            ->withQueryString();

        $actingMapId = $actor->id;

        $items = $page->getCollection()->map(function ($conversation) use ($actingMapId) {
            /** @var Conversation $conversation */
            $actingParticipant = $conversation->participants
                ->first(fn ($participant) => $participant->external_user_map_id === $actingMapId);

            return new ConversationResource($conversation, $actingParticipant);
        });

        return response()->json([
            'success' => true,
            'data' => $items->values(),
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

    /**
     * GET /api/v1/chat/conversations/{conversation} — one thread, actor-scoped.
     * 404 when the caller is not a participant or the thread belongs elsewhere.
     */
    public function show(Request $request, string $conversation): JsonResponse
    {
        /** @var ValidatedPasetoToken|null $validated */
        $validated = $request->attributes->get('paseto');
        if (! $validated instanceof ValidatedPasetoToken) {
            throw new ApiException('INVALID_TOKEN', 'Missing platform context.', 401);
        }

        $actor = $this->userContext->resolve($request, $validated->platform);

        $conversation = $validated->platform
            ->conversations()
            ->where('public_id', $conversation)
            ->with(['lastMessage', 'lastMessageSender', 'participants.externalUserMap'])
            ->first();

        $actingParticipant = $conversation instanceof Conversation
            ? $this->conversations->participant($conversation, $actor->id)
            : null;

        if ($conversation === null || $actingParticipant === null) {
            throw new ApiException('NOT_FOUND', 'Conversation not found.', 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ConversationResource($conversation, $actingParticipant),
            'meta' => ['request_id' => (string) Str::uuid()],
        ], 200);
    }

    /**
     * POST /api/v1/chat/conversations/{conversation}/read — advance the acting
     * user's read cursor (advance-only; never regresses) and reset unread.
     */
    public function markRead(MarkReadRequest $request, string $conversation): JsonResponse
    {
        /** @var ValidatedPasetoToken|null $validated */
        $validated = $request->attributes->get('paseto');
        if (! $validated instanceof ValidatedPasetoToken) {
            throw new ApiException('INVALID_TOKEN', 'Missing platform context.', 401);
        }

        $actor = $this->userContext->resolve($request, $validated->platform);

        $conversation = $validated->platform
            ->conversations()
            ->where('public_id', $conversation)
            ->first();

        if ($conversation === null) {
            throw new ApiException('NOT_FOUND', 'Conversation not found.', 404);
        }

        $requestedMessageId = $request->validated('last_read_message_id');
        $read = $this->conversations->markRead(
            $conversation,
            $actor->id,
            is_numeric($requestedMessageId) ? (int) $requestedMessageId : null,
        );

        return response()->json([
            'success' => true,
            'data' => [
                'conversation_id' => $conversation->public_id,
                'last_read_message_id' => $read['last_read_message_id'],
                'last_read_at' => $read['last_read_at']?->toISOString(),
                'unread_count' => $read['unread_count'],
            ],
            'meta' => ['request_id' => (string) Str::uuid()],
        ], 200);
    }
}
