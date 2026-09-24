<?php

namespace App\Http\Requests;

/**
 * POST /api/v1/chat/conversations/{conversation}/read — advance the acting
 * user's read cursor. Omitting `last_read_message_id` marks everything read;
 * when present it never moves the cursor backwards (docs/realtime-chat.md
 * §Unread Counts).
 */
class MarkReadRequest extends ApiFormRequest
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
            'last_read_message_id' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
