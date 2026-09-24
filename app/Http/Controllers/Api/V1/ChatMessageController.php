<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\MessageHistoryRequest;
use App\Http\Requests\SendMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Platform;
use App\Services\ChatConversationService;
use App\Services\ChatMessageService;
use App\Services\ExternalUserContext;
use App\Services\ValidatedPasetoToken;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * Message endpoints (phase-3d; docs/realtime-chat.md §Delivery Flow).
 *
 * Everything is STRICTLY platform-scoped (verified token) and actor-scoped
 * (X-External-User-Id); a non-participant — or any cross-platform caller — gets
 * a plain 404 so thread existence is never disclosed.
 */
class ChatMessageController extends Controller
{
    public function __construct(
        private readonly ChatMessageService $messages,
        private readonly ChatConversationService $conversations,
        private readonly ExternalUserContext $userContext,
    ) {}

    /**
     * POST /api/v1/chat/conversations/{conversation}/messages — send a message,
     * idempotent under `client_message_id`.
     */
    public function store(SendMessageRequest $request, string $conversation): JsonResponse
    {
        /** @var ValidatedPasetoToken|null $validated */
        $validated = $request->attributes->get('paseto');
        if (! $validated instanceof ValidatedPasetoToken) {
            throw new ApiException('INVALID_TOKEN', 'Missing platform context.', 401);
        }

        $actor = $this->userContext->resolve($request, $validated->platform);
        $conversation = $this->conversationOr404($validated->platform->conversations(), $conversation);

        if ($this->conversations->participant($conversation, $actor->id) === null) {
            throw new ApiException('NOT_FOUND', 'Conversation not found.', 404);
        }

        $result = $this->messages->send(
            $conversation,
            $validated->platform,
            $actor,
            (string) $request->validated('content'),
            (string) $request->validated('client_message_id'),
            (string) ($request->validated('type') ?? 'text'),
        );

        return response()->json([
            'success' => true,
            'data' => new MessageResource($result['message']),
            'meta' => [
                'request_id' => (string) Str::uuid(),
                'duplicate' => $result['duplicate'],
            ],
        ], 201);
    }

    /**
     * GET /api/v1/chat/conversations/{conversation}/messages — cursor-paginated
     * history (opaque `before` cursor; ASC rows; has_more + next_cursor).
     */
    public function index(MessageHistoryRequest $request, string $conversation): JsonResponse
    {
        /** @var ValidatedPasetoToken|null $validated */
        $validated = $request->attributes->get('paseto');
        if (! $validated instanceof ValidatedPasetoToken) {
            throw new ApiException('INVALID_TOKEN', 'Missing platform context.', 401);
        }

        $actor = $this->userContext->resolve($request, $validated->platform);
        $conversation = $this->conversationOr404($validated->platform->conversations(), $conversation);

        if ($this->conversations->participant($conversation, $actor->id) === null) {
            throw new ApiException('NOT_FOUND', 'Conversation not found.', 404);
        }

        $before = $request->validated('before');
        $limit = max(1, min(
            (int) config('chat.history.max_limit', 100),
            (int) ($request->validated('limit') ?? (int) config('chat.history.default_limit', 50))
        ));

        $history = $this->messages->history(
            $conversation,
            $before === null || $before === '' ? null : (int) $before,
            $limit,
        );

        return response()->json([
            'success' => true,
            'data' => MessageResource::collection($history['rows']),
            'meta' => [
                'request_id' => (string) Str::uuid(),
                'pagination' => [
                    'before' => $history['before'],
                    'limit' => $history['limit'],
                    'has_more' => $history['has_more'],
                    'next_cursor' => $history['next_cursor'],
                ],
            ],
        ], 200);
    }

    /**
     * @param  HasMany<Conversation, Platform>  $query
     */
    private function conversationOr404(HasMany $query, string $publicId): Conversation
    {
        $conversation = $query->where('public_id', $publicId)->first();

        if (! $conversation instanceof Conversation) {
            throw new ApiException('NOT_FOUND', 'Conversation not found.', 404);
        }

        return $conversation;
    }
}
