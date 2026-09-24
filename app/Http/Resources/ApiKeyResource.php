<?php

namespace App\Http\Resources;

use App\Models\PlatformApiKey;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Key lifecycle metadata (Phase 3C / plan §16).
 *
 * Deliberately NEVER carries key material: only the public fingerprint (`kid`),
 * lifecycle status, and timestamps. Secrets are returned one-time on
 * create/rotate and forgotten everywhere else.
 */
class ApiKeyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var PlatformApiKey $key */
        $key = $this->resource;

        return [
            'kid' => $key->key_fingerprint,
            'status' => $key->status,
            'is_primary' => (bool) $key->is_primary,
            'token_ttl_seconds' => (int) $key->token_ttl_seconds,
            'created_at' => $key->created_at?->toIso8601String(),
            'rotated_at' => $key->rotated_at?->toIso8601String(),
            'revoked_at' => $key->revoked_at?->toIso8601String(),
            'grace_expires_at' => $key->graceExpiresAt()?->toIso8601String(),
        ];
    }
}
