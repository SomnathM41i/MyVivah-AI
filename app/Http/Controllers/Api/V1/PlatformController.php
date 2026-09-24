<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\PlatformServiceAccess;
use App\Services\ValidatedPasetoToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlatformController extends Controller
{
    /**
     * GET /api/v1/platform/me — authenticated self-describe: the platform's
     * integration + current entitlements (phase-3a §9). Every value is derived
     * from the verified token — never from the request body.
     */
    public function me(Request $request): JsonResponse
    {
        /** @var ValidatedPasetoToken|null $validated */
        $validated = $request->attributes->get('paseto');
        if (! $validated instanceof ValidatedPasetoToken) {
            throw new ApiException('INVALID_TOKEN', 'Missing platform context.', 401);
        }

        $platform = $validated->platform;
        $entitlements = $platform->serviceAccess()
            ->with('service')
            ->get()
            ->map(fn (PlatformServiceAccess $row) => [
                'service_key' => $row->service->key,
                'has_access' => (bool) $row->has_access,
                'effective_until' => $row->effective_until?->toIso8601String(),
            ])
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'data' => [
                'platform' => [
                    'public_id' => $platform->public_id,
                    'slug' => $platform->slug,
                    'status' => $platform->status,
                ],
                'integration' => [
                    'public_id' => $validated->integration->public_id,
                    'status' => $validated->integration->status,
                    'paseto_version' => $validated->integration->paseto_version,
                    'rate_limit_per_minute' => (int) ($validated->integration->rate_limit_per_minute ?: config('api.rate_limit_per_minute', 60)),
                    'token_ttl_seconds' => (int) ($validated->integration->token_ttl_seconds ?: config('paseto.default_ttl_seconds', 3600)),
                    'last_active_at' => $validated->integration->last_active_at?->toIso8601String(),
                ],
                'entitlements' => $entitlements,
                'scopes' => $validated->scopes,
            ],
            'meta' => ['request_id' => (string) Str::uuid()],
        ], 200);
    }
}
