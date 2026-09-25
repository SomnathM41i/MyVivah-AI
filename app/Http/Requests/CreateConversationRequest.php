<?php

namespace App\Http\Requests;

/**
 * POST /api/v1/chat/conversations — resolve-or-create a two-party thread
 * (docs/realtime-chat.md §Conversation Model; MVP = two participants).
 */
class CreateConversationRequest extends ApiFormRequest
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
        $count = (int) config('chat.conversation.participants', 2);
        $maxId = (int) config('chat.conversation.external_user_id_max', 255);

        $widget = $this->attributes->has('widget_session');

        return [
            'candidate_token' => [$widget ? 'required' : 'prohibited', 'string', 'max:4096'],
            'participant_external_ids' => [$widget ? 'sometimes' : 'required', 'array', "size:{$count}", 'distinct'],
            'participant_external_ids.*' => [
                'required',
                'string',
                "between:1,{$maxId}",
                // URL-safe path segment (AGENTS.md §11 — used in identifiers).
                'regex:/^[^\/]+$/u',
            ],
        ];
    }

    /**
     * Belt-and-braces alongside the `distinct` rule: any duplicate pair reaching
     * here is rejected up front so the pair-key (sortPair) can never explode.
     */
    public function after(): array
    {
        return [
            function ($validator): void {
                /** @var mixed $ids */
                $ids = $validator->getData()['participant_external_ids'] ?? null;

                if (is_array($ids) && count($ids) !== count(array_unique($ids))) {
                    $validator->errors()->add(
                        'participant_external_ids',
                        'participant_external_ids must be distinct.'
                    );
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $count = (int) config('chat.conversation.participants', 2);

        return [
            'participant_external_ids.size' => "Exactly {$count} participant_external_ids are required.",
            'participant_external_ids.distinct' => 'participant_external_ids must be distinct.',
        ];
    }
}
