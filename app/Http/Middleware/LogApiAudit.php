<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Models\ApiAuditLog;
use App\Services\ApiAuditService;
use App\Services\ValidatedPasetoToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Per-request audit row for AUTHENTICATED platform API calls (phase-3a §13).
 *
 * Append-only, privacy-preserving: only ip_hash + request/response checksums
 * and minimal dimensions are stored — never raw bodies, tokens, or PII.
 *
 * Place AFTER ValidatePlatformToken so the resolved platform/key context is on
 * the request; authorizations aborted earlier (invalid token, insufficient
 * scope) are audited by the middleware that rejected them.
 *
 * Wraps the downstream stack so that even REJECTED requests (throttled 429,
 * domain errors) are audited: the row is written in `finally` with the
 * exception's status code.
 */
class LogApiAudit
{
    public function __construct(
        private readonly ApiAuditService $audit,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next, ?string $event = null)
    {
        $start = hrtime(true);
        $response = null;
        $status = 500;

        try {
            $response = $next($request);
            $status = $response->getStatusCode();
        } catch (Throwable $ex) {
            $status = $this->statusFor($ex);

            throw $ex;
        } finally {
            /** @var ValidatedPasetoToken|null $validated */
            $validated = $request->attributes->get('paseto');

            $this->audit->record(
                $event ?? ApiAuditLog::EVENT_REQUEST,
                $validated?->platform,
                $validated?->apiKey,
                [
                    'ip_hash' => hash('sha256', $request->ip() ?? ''),
                    'endpoint' => $request->path(),
                    'method' => $request->method(),
                    'status_code' => $status,
                    'duration_ms' => (int) round((hrtime(true) - $start) / 1e6),
                    'request_checksum' => hash('sha256', (string) json_encode($request->all())),
                    'response_checksum' => $response instanceof Response
                        ? hash('sha256', $response->getContent() ?: '')
                        : '',
                ]
            );
        }

        return $response;
    }

    private function statusFor(Throwable $ex): int
    {
        if ($ex instanceof ApiException) {
            return $ex->status();
        }

        if ($ex instanceof HttpExceptionInterface) {
            return $ex->getStatusCode();
        }

        return 500;
    }
}
