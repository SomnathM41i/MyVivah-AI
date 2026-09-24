<?php

namespace App\Http\Middleware;

use App\Services\ValidatedPasetoToken;
use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * PER-PLATFORM rate limit for authenticated v1 routes (phase-3a §14/§15, Phase 3C).
 *
 * Why a dedicated middleware instead of the `throttle:integration` alias:
 * Laravel's request pipeline priority-sorts the `ThrottleRequests` middleware
 * to run BEFORE custom middleware — meaning it would execute before
 * ValidatePlatformToken has hydrated the `paseto` request attribute. This
 * middleware is a plain class (no priority reordering), so it runs exactly
 * where it is listed: after the platform context is available.
 *
 * Policy:
 *   - keyed by the VERIFIED platform id (never client input) — one platform
 *     can never consume another's quota,
 *   - ceiling read from `platform_integrations.rate_limit_per_minute`
 *     (fallback `config('api.rate_limit_per_minute')`, default 60),
 *   - when a request exceeds the ceiling it throws ThrottleRequestsException so
 *     the standardized RATE_LIMITED envelope (+ Retry-After) is rendered and,
 *     because LogApiAudit wraps this middleware, the 429 is still audited.
 */
class EnforcePlatformRateLimit
{
    public function __construct(
        private readonly RateLimiter $limiter,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        $paseto = $request->attributes->get('paseto');

        if (! $paseto instanceof ValidatedPasetoToken) {
            // Nothing verified to key on — rely on the global `api` group throttle.
            return $next($request);
        }

        $key = 'integration:'.$paseto->platform->id;
        $maxAttempts = max(1, (int) ($paseto->integration->rate_limit_per_minute ?: config('api.rate_limit_per_minute', 60)));

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = $this->limiter->availableIn($key);

            throw new ThrottleRequestsException(
                'Too Many Requests',
                null,
                ['Retry-After' => $retryAfter],
            );
        }

        $this->limiter->hit($key, 60);

        $response = $next($request);

        $response->headers->set('X-RateLimit-Limit', (string) $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', (string) $this->limiter->remaining($key, $maxAttempts));

        return $response;
    }
}
