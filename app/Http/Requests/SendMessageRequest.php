<?php

namespace App\Http\Requests;

/**
 * POST /api/v1/chat/conversations/{conversation}/messages — send a message.
 *
 * `client_message_id` is REQUIRED and is the idempotency key: retrying the same
 * logical send (same platform + conversation + client id) returns the original
 * row instead of duplicating it (docs/realtime-chat.md §Delivery Flow).
 */
class SendMessageRequest extends ApiFormRequest
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
            'client_message_id' => [
                'required',
                'string',
                'between:'.(int) config('chat.message.client_id_min', 8).','.(int) config('chat.message.client_id_max', 64),
                'regex:/^[A-Za-z0-9_-]+$/',
            ],
            'content' => ['required', 'string', 'max:'.(int) config('chat.message.max_length', 4000)],
            'type' => ['sometimes', 'string', 'in:text'],
        ];
    }
}
