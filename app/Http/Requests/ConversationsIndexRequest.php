<?php

namespace App\Http\Requests;

/**
 * GET /api/v1/chat/conversations — page-based conversation list (ordered by
 * last-message time; page-based because ordering mutates as messages arrive).
 */
class ConversationsIndexRequest extends ApiFormRequest
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
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'between:1,'.(int) config('chat.list.max_per_page', 100)],
        ];
    }
}
