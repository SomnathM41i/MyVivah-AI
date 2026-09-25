<?php

namespace App\Http\Resources;

use App\Models\PlatformIntegration;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Wire representation of a platform integration's self-service configuration
 * (Phase 3C / plan §16). Never exposes the encrypted key material or internal id.
 */
class IntegrationConfigResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var PlatformIntegration $integration */
        $integration = $this->resource;

        return [
            'public_id' => $integration->public_id,
            'status' => $integration->status,
            'paseto_version' => $integration->paseto_version,
            'base_domain' => $integration->base_domain,
            'allowed_origins' => $integration->allowed_origins ?? [],
            'user_search_endpoint' => $integration->user_search_endpoint,
            'user_search_auth_type' => $integration->user_search_auth_type,
            'user_search_auth_header' => $integration->user_search_auth_header,
            'user_search_credential_configured' => filled($integration->user_search_auth_secret),
            'rate_limit_per_minute' => (int) ($integration->rate_limit_per_minute ?: config('api.rate_limit_per_minute', 60)),
            'token_ttl_seconds' => (int) ($integration->token_ttl_seconds ?: config('paseto.default_ttl_seconds', 3600)),
            'last_active_at' => $integration->last_active_at?->toIso8601String(),
        ];
    }
}
