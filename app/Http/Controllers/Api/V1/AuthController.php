<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\IssueTokenRequest;
use App\Models\Platform;
use App\Models\PlatformApiKey;
use App\Models\PlatformIntegration;
use App\Services\ApiAuditService;
use App\Services\PasetoTokenService;
use App\Services\ValidatedPasetoToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct(
        private readonly PasetoTokenService $tokens,
        private readonly ApiAuditService $audit,
    ) {}

    /**
     * POST /api/v1/auth/token — credential exchange (client_id + client_secret)
     * for a short-lived PASETO v4.local token (phase-3a §9 / §20-6).
     *
     * The secret is verified against the encrypted-at-rest key material via
     * its fingerprint; the plaintext is never persisted, logged, or returned.
     * Success → `token_issued`, failure → `token_rejected` (audit trail).
     */
    public function issue(IssueTokenRequest $request): JsonResponse
    {
        $platform = null;
        $key = null;

        try {
            $platform = Platform::query()
                ->with('integration.apiKeys')
                ->where('public_id', $request->validated('client_id'))
                ->first();

            if ($platform === null) {
                throw new ApiException('INVALID_CLIENT', 'Unknown client.', 401);
            }

            $this->assertPlatformActive($platform);

            $integration = $platform->integration;
            if ($integration === null) {
                throw new ApiException('INVALID_CLIENT', 'Platform has no integration.', 401);
            }
            $this->assertIntegrationUsable($integration);

            $secret = (string) $request->validated('client_secret');
            $key = $this->findMatchingKey($integration, $secret);
            // Constant-time comparison once the candidate key is pinned by fingerprint.
            if ($key === null || ! hash_equals($key->toSymmetricKey()->encode(), $secret)) {
                throw new ApiException('INVALID_CLIENT', 'Invalid client credentials.', 401);
            }

            // Authorization comes from T8 entitlements (phase-3a §6.2), not the client.
            $scopes = $this->tokens->scopesFor($platform);
            $requested = $this->normalizeRequestedScopes($request);
            if ($requested !== null) {
                $unknown = array_diff($requested, $scopes);
                if ($unknown !== []) {
                    throw new ApiException('SERVICE_NO_ACCESS', 'Requested scope not granted.', 403);
                }
                $scopes = array_values(array_intersect($scopes, $requested));
            }

            $issued = $this->tokens->issue($integration, $key, $scopes);
        } catch (ApiException $ex) {
            $this->audit->tokenRejected($platform, $key, [
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'status_code' => $ex->status(),
                'ip_hash' => hash('sha256', $request->ip() ?? ''),
                'reason' => $ex->errorCode(),
            ]);

            throw $ex;
        }

        $this->audit->tokenIssued($platform, $key, [
            'jti' => $issued->jti,
            'scope' => $issued->scopes,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'token_type' => 'Bearer',
                'access_token' => $issued->token,
                'expires_in' => $issued->expiresInSeconds(),
                'scope' => $issued->scopes,
                'jti' => $issued->jti,
            ],
            'meta' => ['request_id' => (string) Str::uuid()],
        ], 200);
    }

    /**
     * POST /api/v1/auth/revoke — revoke the presented token (jti blacklist).
     */
    public function revoke(Request $request): JsonResponse
    {
        $validated = $request->attributes->get('paseto');
        if (! $validated instanceof ValidatedPasetoToken) {
            throw new ApiException('INVALID_TOKEN', 'Missing platform context.', 401);
        }

        $header = (string) $request->header('Authorization', '');
        $token = trim(substr($header, 7));

        $this->tokens->revoke($token);

        return response()->json([
            'success' => true,
            'data' => ['revoked' => true, 'jti' => $validated->jti],
            'meta' => ['request_id' => (string) Str::uuid()],
        ], 200);
    }

    private function assertPlatformActive(Platform $platform): void
    {
        if ($platform->status !== Platform::STATUS_ACTIVE) {
            throw new ApiException('PLATFORM_SUSPENDED', 'This platform is not active.', 403);
        }
    }

    private function assertIntegrationUsable(PlatformIntegration $integration): void
    {
        if ($integration->revoked_at !== null) {
            throw new ApiException('INVALID_CLIENT', 'This integration has been revoked.', 401);
        }
        if ($integration->status !== PlatformIntegration::STATUS_ACTIVE) {
            throw new ApiException('PLATFORM_SUSPENDED', 'This integration is not active.', 403);
        }
        if ($integration->paseto_version !== PlatformIntegration::PASETO_V4_LOCAL) {
            throw new ApiException('INVALID_CLIENT', 'Unsupported PASETO version.', 401);
        }
    }

    /**
     * Pin the API key by fingerprinting the presented secret (avoids iterating /
     * decrypting every row), then return it for constant-time comparison.
     *
     * Rotation-aware: a demoted (rotated) key may still issue tokens while inside
     * its grace window; once the window has elapsed it is hard-revoked on sight.
     */
    private function findMatchingKey(PlatformIntegration $integration, string $secret): ?PlatformApiKey
    {
        $fingerprint = PlatformApiKey::fingerprint($secret);

        $key = $integration->apiKeys()
            ->where('key_fingerprint', $fingerprint)
            ->where('status', '!=', PlatformApiKey::STATUS_REVOKED)
            ->whereNull('revoked_at')
            ->first();

        if ($key === null) {
            return null;
        }

        if ($key->status === PlatformApiKey::STATUS_ROTATED && ! $key->isUsable()) {
            $key->hardRevoke();

            throw new ApiException(
                'TOKEN_REVOKED',
                'This API key was rotated and its grace period has expired.',
                401
            );
        }

        return $key->isUsable() ? $key : null;
    }

    /**
     * @return list<string>|null
     */
    private function normalizeRequestedScopes(IssueTokenRequest $request): ?array
    {
        $scopes = $request->validated('scope');
        if (! is_array($scopes) || $scopes === []) {
            return null;
        }

        $result = [];
        foreach ($scopes as $scope) {
            if (is_string($scope) && $scope !== '') {
                $result[] = $scope;
            }
        }

        return $result === [] ? null : array_values(array_unique($result));
    }
}
