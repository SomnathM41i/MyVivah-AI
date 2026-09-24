<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * Server-to-server bootstrap request (Phase 4).
 *
 * POST /api/v1/widget/session — the platform's backend picks WHICH of its users
 * gets a widget session. `external_user_id` is mandatory and governed by the
 * same identity rules as the rest of the chat API (max 255).
 */
class WidgetSessionRequest extends ApiFormRequest
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
            'external_user_id' => [
                'required',
                'string',
                'max:'.(int) config('chat.conversation.external_user_id_max', 255),
                Rule::notIn(['']),
            ],
        ];
    }
}
