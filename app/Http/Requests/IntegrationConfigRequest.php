<?php

namespace App\Http\Requests;

/**
 * Self-service integration configuration (Phase 3C).
 *
 * Only the operator-tunable connection fields are mutable via the API:
 *   - base_domain   — the external platform's own origin (single value).
 *   - allowed_origins — CORS allowlist of origins (never `*` with credentials).
 * Fixed policy fields (paseto_version, token_ttl_seconds, rate_limit_per_minute,
 * status) stay out of reach until the admin surface exists.
 */
class IntegrationConfigRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization is fully handled by route middleware
     * (ValidatePlatformToken + EnsurePlatformAccess:authentication); scope checks
     * derive from the verified token, never from the body.
     */
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
            'base_domain' => ['sometimes', 'nullable', 'url', 'max:255'],
            'allowed_origins' => ['sometimes', 'nullable', 'array', 'max:20'],
            'allowed_origins.*' => ['url', 'max:255'],
        ];
    }
}
