<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\IntegrationConfigRequest;
use App\Http\Requests\KeyRevokeRequest;
use App\Http\Resources\ApiKeyResource;
use App\Http\Resources\IntegrationConfigResource;
use App\Models\Platform;
use App\Services\ApiKeyService;
use App\Services\ValidatedPasetoToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * Platform integration self-service endpoints (Phase 3C).
 *
 * All calls require the bootstrap `authentication` scope, which every issued
 * token carries. Platform context ALWAYS comes from the verified PASETO — never
 * from the body/path — so an integration can only manage ITS OWN configuration
 * and keys (strict platform isolation). The raw secret is returned exactly once
 * on rotation and never listed again.
 */
class IntegrationController extends Controller
{
    public function __construct(
        private readonly ApiKeyService $keys,
    ) {}

    /**
     * GET /api/v1/integration/config
     */
    public function config(Request $request): JsonResponse
    {
        $validated = $this->context($request);

        return response()->json([
            'success' => true,
            'data' => [
                'platform' => $this->platformBrief($validated->platform),
                'integration' => new IntegrationConfigResource($validated->integration),
            ],
            'meta' => ['request_id' => (string) Str::uuid()],
        ], 200);
    }

    /**
     * PATCH /api/v1/integration/config
     */
    public function updateConfig(IntegrationConfigRequest $request): JsonResponse
    {
        $validated = $this->context($request);
        $integration = $validated->integration;

        if ($request->exists('base_domain')) {
            $baseDomain = $request->validated('base_domain');

            $integration->base_domain = is_string($baseDomain) && $baseDomain !== ''
                ? $baseDomain
                : null;
        }

        if ($request->exists('allowed_origins')) {
            $allowedOrigins = $request->validated('allowed_origins');

            $integration->allowed_origins = is_array($allowedOrigins)
                ? array_values(array_map('strval', $allowedOrigins))
                : null;
        }

        foreach (['user_search_endpoint', 'user_search_auth_type', 'user_search_auth_header'] as $field) {
            if ($request->exists($field)) {
                $integration->{$field} = $request->validated($field);
            }
        }
        if ($request->exists('user_search_auth_secret')) {
            $secret = $request->validated('user_search_auth_secret');
            $integration->user_search_auth_secret = is_string($secret) && $secret !== '' ? Crypt::encryptString($secret) : null;
        }

        $integration->save();

        return response()->json([
            'success' => true,
            'data' => [
                'platform' => $this->platformBrief($validated->platform),
                'integration' => new IntegrationConfigResource($integration->refresh()),
            ],
            'meta' => [
                'request_id' => (string) Str::uuid(),
                'updated' => true,
            ],
        ], 200);
    }

    /**
     * GET /api/v1/integration/keys — lifecycle metadata only, never secrets.
     */
    public function keys(Request $request): JsonResponse
    {
        $validated = $this->context($request);
        $items = $validated->integration->apiKeys()->latest('id')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'integration' => [
                    'public_id' => $validated->integration->public_id,
                ],
                'keys' => ApiKeyResource::collection($items),
            ],
            'meta' => [
                'request_id' => (string) Str::uuid(),
                'count' => $items->count(),
            ],
        ], 200);
    }

    /**
     * POST /api/v1/integration/keys/rotate — mint a new primary key.
     *
     * Demotes the current primary to a grace backup (usable for
     * `paseto.rotation_grace_seconds`, default 24h) and returns the new
     * secret ONE time. The rotation itself is audited (kids only).
     */
    public function rotate(Request $request): JsonResponse
    {
        $validated = $this->context($request);

        [$newKey, $secret] = $this->keys->rotate($validated->integration);
        $items = $validated->integration->apiKeys()->latest('id')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'rotated' => true,
                'current_kid' => $newKey->key_fingerprint,
                'client_secret' => $secret,
                'client_secret_visible_once' => true,
                'keys' => ApiKeyResource::collection($items),
            ],
            'meta' => ['request_id' => (string) Str::uuid()],
        ], 200);
    }

    /**
     * POST /api/v1/integration/keys/revoke — hard-revoke one of OUR OWN keys.
     *
     * Strict isolation: a fingerprint that does not belong to this platform's
     * integration resolves to 404, never to another platform's key.
     */
    public function revoke(KeyRevokeRequest $request): JsonResponse
    {
        $validated = $this->context($request);
        $kid = (string) $request->validated('key_fingerprint');

        $key = $validated->integration->apiKeys()
            ->where('key_fingerprint', $kid)
            ->first();

        if ($key === null) {
            throw new ApiException('NOT_FOUND', 'No API key found for this fingerprint.', 404);
        }

        $this->keys->revokeKey($validated->integration, $key);

        return response()->json([
            'success' => true,
            'data' => [
                'revoked' => true,
                'key_fingerprint' => $kid,
            ],
            'meta' => ['request_id' => (string) Str::uuid()],
        ], 200);
    }

    /**
     * @return array{public_id: string, slug: string, status: string}
     */
    private function platformBrief(Platform $platform): array
    {
        return [
            'public_id' => $platform->public_id,
            'slug' => $platform->slug,
            'status' => $platform->status,
        ];
    }

    private function context(Request $request): ValidatedPasetoToken
    {
        $validated = $request->attributes->get('paseto');

        if (! $validated instanceof ValidatedPasetoToken) {
            throw new ApiException('INVALID_TOKEN', 'Missing platform context.', 401);
        }

        return $validated;
    }
}
