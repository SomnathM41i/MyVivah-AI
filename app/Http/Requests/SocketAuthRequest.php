<?php

namespace App\Http\Requests;

/**
 * POST /api/v1/chat/socket/auth — realtime channel subscription authorization
 * (Phase 3E). The widget sends the Pusher-style handshake body; this API asserts
 * platform ownership + participant/member authorization and returns a signed
 * `auth` for the realtime server.
 */
class SocketAuthRequest extends ApiFormRequest
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
            // Pusher socket ids look like "123456.789012".
            'socket_id' => ['required', 'string', 'regex:/^[0-9]+\.[0-9]+$/'],
            'channel_name' => ['required', 'string', 'max:120'],
        ];
    }
}
