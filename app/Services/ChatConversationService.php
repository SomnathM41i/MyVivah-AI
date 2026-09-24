<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\ExternalUserMap;
use App\Models\Message;
use App\Models\Platform;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Conversation lifecycle (phase-3d; docs/realtime-chat.md §Conversation Model).
 *
 * Resolve-or-create semantics: a conversation is identified by the deterministic
 * `participant_key` (sorted pair of external-user-map ids), scoped by platform.
 * The same pair always resolves to the same thread; ordering of the request ids
 * never matters; a concurrent first-create tripping the unique key converges on
 * the winner's row.
 *
 * Realtime (Phase 3E): a genuinely NEW thread fires `conversation.updated`
 * (`reason=created`); mark-read fires `message.read`. Both post-commit and
 * transport-failure-resilient.
 */
class ChatConversationService
{
    public function __construct(
        private readonly RealtimeBroadcaster $realtime,
    ) {}

    private function pairKey(int $lower, int $higher): string
    {
        return $lower.':'.$higher;
    }

    /**
     * Resolve an existing conversation for the pair, or create it with the two
     * participant seats atomically. Returns the (possibly just-created) thread.
     *
     * @param  array<int, int>  $externalUserMapIds  exactly two distinct map ids
     */
    public function resolveOrCreate(Platform $platform, array $externalUserMapIds): Conversation
    {
        $pair = $this->sortPair($externalUserMapIds);
        $key = $this->pairKey($pair[0], $pair[1]);

        $existing = Conversation::query()
            ->where('platform_id', $platform->id)
            ->where('participant_key', $key)
            ->first();

        if ($existing instanceof Conversation) {
            return $existing;
        }

        try {
            $conversation = DB::transaction(function () use ($platform, $pair, $key): Conversation {
                $conversation = Conversation::query()->create([
                    'platform_id' => $platform->id,
                    'participant_key' => $key,
                    'status' => Conversation::STATUS_ACTIVE,
                ]);

                foreach ($pair as $mapId) {
                    $conversation->participants()->create([
                        'platform_id' => $platform->id,
                        'external_user_map_id' => $mapId,
                        'unread_count' => 0,
                        'joined_at' => now(),
                    ]);
                }

                return $conversation;
            });
        } catch (QueryException $ex) {
            if (! $this->isUniqueViolation($ex)) {
                throw $ex;
            }

            return Conversation::query()
                ->where('platform_id', $platform->id)
                ->where('participant_key', $key)
                ->firstOrFail();
        }

        $this->realtime->conversationUpdated($conversation, 'created');

        return $conversation;
    }

    /**
     * The seat for an external user in a conversation, strictly platform-scoped.
     * Null means the user is not a participant (authorization gate: 404).
     */
    public function participant(Conversation $conversation, int $externalUserMapId): ?ConversationParticipant
    {
        return ConversationParticipant::query()
            ->where('conversation_id', $conversation->id)
            ->where('platform_id', $conversation->platform_id)
            ->where('external_user_map_id', $externalUserMapId)
            ->first();
    }

    /**
     * Mark a participant's read cursor at a message (never backwards), reset the
     * unread fast-path, and return the fresh read state.
     *
     * @return array{participant: ConversationParticipant, last_read_message_id: ?int, last_read_at: ?Carbon, unread_count: int}
     */
    public function markRead(Conversation $conversation, int $externalUserMapId, ?int $requestedMessageId): array
    {
        /** @var ConversationParticipant|null $participant */
        $participant = ConversationParticipant::query()
            ->where('conversation_id', $conversation->id)
            ->where('platform_id', $conversation->platform_id)
            ->where('external_user_map_id', $externalUserMapId)
            ->lockForUpdate()
            ->first();

        if (! $participant instanceof ConversationParticipant) {
            throw new ApiException('NOT_FOUND', 'Conversation not found.', 404);
        }

        $read = DB::transaction(function () use ($conversation, $participant, $requestedMessageId): array {
            $latestMessageId = (int) (Message::query()
                ->where('conversation_id', $conversation->id)
                ->where('platform_id', $conversation->platform_id)
                ->max('id') ?? 0);

            $candidate = $requestedMessageId !== null
                ? min($requestedMessageId, $latestMessageId)
                : $latestMessageId;

            if ($candidate > (int) ($participant->last_read_message_id ?? 0)) {
                $participant->forceFill([
                    'last_read_message_id' => $candidate,
                    'last_read_at' => now(),
                ])->save();
            }

            $participant->forceFill(['unread_count' => 0])->save();
            $participant->refresh();

            $lastReadAt = $participant->last_read_at;

            return [
                'participant' => $participant,
                'last_read_message_id' => $participant->last_read_message_id,
                'last_read_at' => $lastReadAt === null ? null : Carbon::make($lastReadAt),
                'unread_count' => 0,
            ];
        });

        $reader = $participant->externalUserMap;

        if ($reader instanceof ExternalUserMap) {
            $this->realtime->messageRead(
                $conversation,
                $reader,
                $read['last_read_message_id'],
                $read['unread_count'],
                $read['last_read_at'] ?? Carbon::now(),
            );
        }

        return $read;
    }

    /**
     * @param  array<int, int>  $mapIds
     * @return array{0: int, 1: int}
     */
    private function sortPair(array $mapIds): array
    {
        $sorted = array_values(array_unique(array_map('intval', $mapIds)));

        if (count($sorted) !== 2) {
            throw new ApiException(
                'VALIDATION_FAILED',
                'A conversation requires exactly two distinct participants.',
                422
            );
        }

        sort($sorted);

        return [$sorted[0], $sorted[1]];
    }

    private function isUniqueViolation(QueryException $ex): bool
    {
        $message = $ex->getMessage();

        return str_contains($message, 'SQLSTATE[23000]')
            || str_contains($message, 'SQLSTATE[23505]')
            || str_contains($message, 'UNIQUE constraint failed')
            || str_contains($message, 'conversations_platform_pair');
    }
}
