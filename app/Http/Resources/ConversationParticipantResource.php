<?php

namespace App\Http\Resources;

use App\Models\ExternalUserMap;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A conversation participant's public reference — the platform's OWN external
 * id, rendered exactly like `ExternalUserReferenceResource` so a widget never
 * sees any entity other than the two participating users.
 */
class ConversationParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var ExternalUserMap $map */
        $map = $this->resource->relationLoaded('externalUserMap')
            && $this->resource->externalUserMap !== null
            ? $this->resource->externalUserMap
            : null;

        if ($map === null) {
            return ['id' => null];
        }

        return (new ExternalUserReferenceResource($map))->toArray($request);
    }
}
