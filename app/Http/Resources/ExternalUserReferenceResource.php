<?php

namespace App\Http\Resources;

use App\Models\ExternalUserMap;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Platform-scoped external→local user identity reference (Phase 3C / plan §16).
 *
 * Data-minimization: mapping + sync timestamps only. The external platform
 * stays the profile source of truth — MyVivahAI never mirrors it.
 */
class ExternalUserReferenceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var ExternalUserMap $map */
        $map = $this->resource;

        return [
            'platform' => [
                'public_id' => $map->platform->public_id,
            ],
            'external_user_id' => $map->external_user_id,
            'local_public_id' => $map->local_public_id,
            'synced_at' => $map->synced_at?->toIso8601String(),
            'last_seen_at' => $map->last_seen_at?->toIso8601String(),
        ];
    }
}
