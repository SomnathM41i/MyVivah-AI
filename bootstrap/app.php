<?php

use App\Exceptions\ApiException;
use App\Http\Middleware\RestrictWidgetOrigins;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Phase 4 widget CORS restriction. PREPENDED (outermost) on purpose:
        // HandleCors (default global stack) APPENDS the CORS response headers
        // on its way OUT, and a middleware only sees those headers if it ran
        // EARLIER on the request path — i.e. LAST on the response path.
        // RestrictWidgetOrigins therefore strips ACAO after HandleCors added it,
        // enforcing per-platform `allowed_origins` for both preflight and real
        // requests without reconfiguring the static CorsService.
        $middleware->prepend(RestrictWidgetOrigins::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Standardized API error envelope (phase-3a-api-integration-plan.md §11).
        // Every handler below is scoped to `api/*`; non-API requests fall back to
        // Laravel's default rendering. Render callbacks are tried in order and the
        // first non-null response wins.
        $onApi = fn (Request $request): bool => $request->is('api/*');

        $envelope = function (string $code, int $status, string $message, Request $request, array $extra = []) {
            $error = [
                'code' => $code,
                'status' => $status,
                'message' => $message,
                'request_id' => (string) Str::uuid(),
            ] + $extra;

            return response()->json(['success' => false, 'error' => $error], $status);
        };

        // 1) Domain errors (401/403/404/422... from controllers/services).
        $exceptions->render(function (ApiException $e, Request $request) use ($onApi, $envelope) {
            if (! $onApi($request)) {
                return null;
            }

            $extra = $e->errors() !== null ? ['errors' => $e->errors()] : [];

            return $envelope($e->errorCode(), $e->status(), $e->getMessage(), $request, $extra);
        });

        // 2) Rate limiting (429) — include the Retry-After hint for clients.
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) use ($onApi, $envelope) {
            if (! $onApi($request)) {
                return null;
            }

            $retryAfter = $e->getHeaders()['Retry-After'] ?? null;

            $response = $envelope('RATE_LIMITED', 429, 'Too many requests. Try again later.', $request, [
                'retry_after_seconds' => is_numeric($retryAfter) ? (int) $retryAfter : null,
            ]);

            if (is_numeric($retryAfter)) {
                $response->headers->set('Retry-After', (string) (int) $retryAfter);
            }

            return $response;
        });

        // 3) Everything else on API routes keeps a single envelope:
        //    - scaffolded response exceptions (FormRequest VALIDATION_FAILED) pass through,
        //    - other HTTP exceptions (404 NOT_FOUND / 405 METHOD_NOT_ALLOWED / ...) are mapped,
        //    - genuine internal errors become INTERNAL_ERROR without leaking internals.
        $exceptions->render(function (Throwable $e, Request $request) use ($onApi, $envelope) {
            if (! $onApi($request)) {
                return null;
            }

            if ($e instanceof HttpResponseException) {
                return null;
            }

            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
                $code = match ($status) {
                    404 => 'NOT_FOUND',
                    405 => 'METHOD_NOT_ALLOWED',
                    default => 'REQUEST_FAILED',
                };

                Log::warning('API request failed', [
                    'status' => $status,
                    'exception' => $e::class,
                ]);

                return $envelope($code, $status, $e->getMessage(), $request);
            }

            Log::error('Unhandled API error', [
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return $envelope('INTERNAL_ERROR', 500, 'Internal server error.', $request);
        });
    })->create();
