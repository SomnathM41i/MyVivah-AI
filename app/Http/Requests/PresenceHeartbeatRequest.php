<?php

namespace App\Http\Requests;

/**
 * POST /api/v1/chat/presence — presence heartbeat (Phase 3E).
 *
 * `status` is optional and defaults to `online`. An explicit `offline` heartbeat
 * marks the user offline and broadcasts user.offline on the transition.
 */
class PresenceHeartbeatRequest extends ApiFormRequest
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
            'status' => ['sometimes', 'string', 'in:online,offline'],
        ];
    }
}
