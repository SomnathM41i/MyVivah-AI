<?php

namespace App\Http\Requests;

/**
 * GET /api/v1/chat/conversations/{conversation}/messages — cursor-paginated
 * history. `before` is an opaque message cursor returned as `next_cursor`;
 * `limit` bounds the page (docs/realtime-chat.md §History & Pagination).
 */
class MessageHistoryRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'limit' => ['sometimes', 'integer', 'between:1,'.(int) config('chat.history.max_limit', 100)],
            'before' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
