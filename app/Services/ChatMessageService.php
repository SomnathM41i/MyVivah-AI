<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\ExternalUserMap;
use App\Models\Message;
use App\Models\Platform;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Message persistence (phase-3d; docs/realtime-chat.md §Delivery Flow).
 *
 * Idempotency: a send is keyed on UNIQUE(platform_id, conversation_id,
 * client_message_id). A retry of a failed/retried send converges on the ORIGINAL
 * row (never a duplicate) — including under concurrent attempts, which race onto
 * the unique index and are reconciled by one re-read.
 *
 * Side effects happen atomically with the insert in a single transaction:
 *   1. the conversation's denormalized last-message metadata, and
 *   2. unread increments for every OTHER participant (the sender's own unread
 *      never moves — read state excludes self-sent messages).
 *
 * Realtime (Phase 3E): messages are broadcast ONLY after they exist in MySQL —
 * the FIRST successful insert fires `message.created` + `conversation.updated`;
 * idempotent retries never re-broadcast. A dead transport is rescued, so realtime
 * can never fail a send.
 */
class ChatMessageService
{
    public function __construct(
        private readonly RealtimeBroadcaster $realtime,
    ) {}

    /**
     * Idempotently persist a message and return the row + whether it is a retry.
     *
     * @return array{message: Message, duplicate: bool}
     */
    public function send(
        Conversation $conversation,
        Platform $platform,
        ExternalUserMap $sender,
        string $content,
        string $clientMessageId,
        string $type = Message::TYPE_TEXT,
    ): array {
        $existing = $this->findByIdempotency($conversation, $clientMessageId);
        if ($existing instanceof Message) {
            return ['message' => $existing, 'duplicate' => true];
        }

        try {
            $message = DB::transaction(function () use ($conversation, $platform, $sender, $content, $clientMessageId, $type): Message {
                $message = Message::query()->create([
                    'platform_id' => $platform->id,
                    'conversation_id' => $conversation->id,
                    'sender_external_user_map_id' => $sender->id,
                    'type' => $type,
                    'status' => Message::STATUS_SENT,
                    'content' => $content,
                    'client_message_id' => $clientMessageId,
                ]);

                $this->touchConversation($conversation, $message, $sender);

                ConversationParticipant::query()
                    ->where('conversation_id', $conversation->id)
                    ->where('platform_id', $platform->id)
                    ->where('external_user_map_id', '!=', $sender->id)
                    ->increment('unread_count');

                return $message;
            });
        } catch (QueryException $ex) {
            if (! $this->isUniqueViolation($ex)) {
                throw $ex;
            }

            $message = $this->findByIdempotency($conversation, $clientMessageId);

            return ['message' => $message, 'duplicate' => true];
        }

        if ($message instanceof Message) {
            $this->realtime->messageCreated($conversation, $message, $sender);
            $this->realtime->conversationUpdated($conversation, 'updated');
        }

        return ['message' => $message, 'duplicate' => false];
    }

    /**
     * Cursor-paginated history, newest-first fetch, presented oldest-first.
     *
     * Returns at most `$limit` messages with ids strictly below `$before`
     * (or the tail when null). `has_more` is resolved with a limit+1 probe;
     * `next_cursor` = the smallest id on this page (pass it back as `before`).
     *
     * @return array{rows: Collection<int, Message>, before: ?int, limit: int, has_more: bool, next_cursor: ?int}
     */
    public function history(Conversation $conversation, ?int $before, int $limit): array
    {
        $query = Message::query()
            ->with('sender')
            ->where('conversation_id', $conversation->id)
            ->where('platform_id', $conversation->platform_id);

        if ($before !== null) {
            $query->where('id', '<', $before);
        }

        $rows = $query->orderByDesc('id')->limit($limit + 1)->get();

        /** @var bool $hasMore */
        $hasMore = $rows->count() > $limit;

        /** @var Collection<int, Message> $page */
        $page = $rows->take($limit)->reverse()->values();

        return [
            'rows' => $page,
            'before' => $before,
            'limit' => $limit,
            'has_more' => $hasMore,
            'next_cursor' => $hasMore ? (int) $page->first()->id : null,
        ];
    }

    private function findByIdempotency(Conversation $conversation, string $clientMessageId): ?Message
    {
        return Message::query()
            ->where('platform_id', $conversation->platform_id)
            ->where('conversation_id', $conversation->id)
            ->where('client_message_id', $clientMessageId)
            ->first();
    }

    /**
     * Keep the denormalized list/sort metadata (conversations.last_message_*)
     * transactionally in sync with the newest insert.
     */
    private function touchConversation(Conversation $conversation, Message $message, ExternalUserMap $sender): void
    {
        $conversation->forceFill([
            'status' => Conversation::STATUS_ACTIVE,
            'last_message_id' => $message->id,
            'last_message_at' => $message->created_at ?? now(),
            'last_message_excerpt' => Str::limit(
                $message->content,
                (int) config('chat.message.excerpt_length', 120)
            ),
            'last_message_sender_external_user_map_id' => $sender->id,
        ])->save();
    }

    private function isUniqueViolation(QueryException $ex): bool
    {
        $message = $ex->getMessage();

        return str_contains($message, 'SQLSTATE[23000]')
            || str_contains($message, 'SQLSTATE[23505]')
            || str_contains($message, 'UNIQUE constraint failed')
            || str_contains($message, 'messages_platform_conversation_client');
    }
}
