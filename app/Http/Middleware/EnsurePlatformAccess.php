<?php

namespace App\Http\Middleware;

use App\Models\ApiAuditLog;
use App\Services\ApiAuditService;
use App\Services\ValidatedPasetoToken;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Scope + entitlement gate for authenticated platform API calls.
 *
 * Usage: `ensure.platform.access:realtime_chat.read` (dot stands for the `:`
 * in a PASETO scope — Laravel splits middleware params on `:`).
 *
 * Enforces BOTH layers from phase-3a §7:
 *  1. the token actually carries the required scope, and
 *  2. the platform holds a LIVE `platform_service_access` entitlement for the
 *     service implied by the scope (has_access = true, effective_until ≥ now).
 * The platform context is always taken from the verified token, never the body.
 * Every denial is written to the audit trail as `insufficient_scope`.
 */
class EnsurePlatformAccess
{
    public function __construct(
        private readonly ApiAuditService $audit,
    ) {}

    /**
     * @param  Closure(Request): mixed  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $scopeDotted)
    {
        $scope = str_replace('.', ':', $scopeDotted);

        /** @var ValidatedPasetoToken|null $validated */
        $validated = $request->attributes->get('paseto');

        if (! $validated instanceof ValidatedPasetoToken) {
            $this->denyAudit($request, $validated, $scope, 'Missing platform context.');

            return $this->denied('SERVICE_NO_ACCESS', 'Missing platform context.', 403);
        }

        if (! $validated->hasScope($scope)) {
            $this->denyAudit($request, $validated, $scope, 'Token lacks required scope.');

            return $this->denied('SERVICE_NO_ACCESS', 'Token lacks required scope.', 403);
        }

        // Bootstrap scopes (e.g. `authentication`) minted on EVERY token are pure
        // gate-keepers: validity was already verified by ValidatePlatformToken, no
        // entitlement row exists for them, so no service lookup is needed.
        if ($scope === config('paseto.authentication_scope')) {
            return $next($request);
        }

        $serviceKey = Str::before($scope, ':');
        $hasEntitlement = $validated->platform
            ->serviceAccess()
            ->where('has_access', true)
            ->where(fn ($q) => $q->whereNull('effective_until')->orWhere('effective_until', '>=', now()))
            ->whereHas('service', fn ($q) => $q->where('key', $serviceKey)->where('is_active', true))
            ->exists();

        if (! $hasEntitlement) {
            $this->denyAudit($request, $validated, $scope, 'Platform lacks a live entitlement for this service.');

            return $this->denied('SERVICE_NO_ACCESS', 'Platform lacks a live entitlement for this service.', 403);
        }

        return $next($request);
    }

    protected function denyAudit(Request $request, ?ValidatedPasetoToken $validated, string $scope, string $reason): void
    {
        $this->audit->record(
            ApiAuditLog::EVENT_INSUFFICIENT_SCOPE,
            $validated?->platform,
            $validated?->apiKey,
            [
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'status_code' => 403,
                'ip_hash' => hash('sha256', $request->ip() ?? ''),
                'scope' => $scope,
                'reason' => $reason,
            ]
        );
    }

    protected function denied(string $code, string $message, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $code,
                'status' => $status,
                'message' => $message,
                'request_id' => (string) Str::uuid(),
            ],
        ], $status);
    }
}
