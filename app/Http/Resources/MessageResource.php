<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A single chat message (docs/integration-api.md §Conversation Messages).
 *
 * `public_id` is the client-facing ULID; `sender` echoes the platform's own
 * external id so the widget can style messages (including "seamlessly
 * yourself" — it knows its own X-External-User-Id). `client_message_id` is
 * echoed so a retried send can be correlated to the original response.
 */
class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->public_id,
            'type' => $this->resource->type,
            'status' => $this->resource->status,
            'content' => $this->resource->content,
            'client_message_id' => $this->resource->client_message_id,
            'sender' => $this->resource->relationLoaded('sender') && $this->resource->sender !== null
                ? new ExternalUserReferenceResource($this->resource->sender)
                : null,
            'sent_at' => $this->resource->created_at?->toISOString(),
        ];
    }
}
