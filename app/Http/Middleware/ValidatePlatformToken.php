<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Models\ApiAuditLog;
use App\Models\Platform;
use App\Services\ApiAuditService;
use App\Services\PasetoTokenService;
use App\Support\TokenKeyContext;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Authenticate an API request via `Authorization: Bearer <paseto>`.
 *
 * Fully validates the v4.local token (signature, v4-only allow-list, exp,
 * platform-claim isolation, revocation) and exposes the resolved platform
 * context on the request for downstream authorization. Rejections are written
 * to the audit trail (event chosen from the stable error code).
 */
class ValidatePlatformToken
{
    public function __construct(
        private readonly PasetoTokenService $tokens,
        private readonly ApiAuditService $audit,
    ) {}

    /**
     * @param  Closure(Request): mixed  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization', '');
        if (! is_string($header) || ! str_starts_with($header, 'Bearer ')) {
            $this->auditInvalid($request, ApiAuditLog::EVENT_INVALID_TOKEN, 401);

            return $this->unauthorized('INVALID_TOKEN', 'Missing bearer token.');
        }

        $token = trim(substr($header, 7));
        if ($token === '') {
            $this->auditInvalid($request, ApiAuditLog::EVENT_INVALID_TOKEN, 401);

            return $this->unauthorized('INVALID_TOKEN', 'Missing bearer token.');
        }

        try {
            $validated = $this->tokens->validate($token);
        } catch (ApiException $ex) {
            $this->auditRejection($request, $ex, $token);

            return $this->unauthorized($ex->errorCode(), $ex->getMessage(), $ex->status());
        }

        $request->attributes->set('paseto', $validated);

        return $next($request);
    }

    protected function unauthorized(string $code, string $message, int $status = 401): JsonResponse
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

    /**
     * Map a stable ApiException code to its audit event + platform context.
     */
    private function auditRejection(Request $request, ApiException $ex, string $token): void
    {
        $code = $ex->errorCode();

        switch ($code) {
            case 'TOKEN_EXPIRED':
                $event = ApiAuditLog::EVENT_TOKEN_EXPIRED;
                break;
            case 'PLATFORM_MISMATCH':
                $event = ApiAuditLog::EVENT_WRONG_PLATFORM;
                break;
            case 'TOKEN_REVOKED':
                $event = ApiAuditLog::EVENT_TOKEN_REJECTED;
                break;
            default:
                $event = ApiAuditLog::EVENT_INVALID_TOKEN;
                break;
        }

        [$platform, $key] = TokenKeyContext::resolve($token);

        $this->audit->record($event, $platform, $key, [
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'status_code' => $ex->status(),
            'ip_hash' => hash('sha256', $request->ip() ?? ''),
            'reason' => $code,
        ]);
    }

    private function auditInvalid(Request $request, string $event, int $status): void
    {
        $this->audit->record($event, null, null, [
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'status_code' => $status,
            'ip_hash' => hash('sha256', $request->ip() ?? ''),
            'reason' => $event,
        ]);
    }
}
