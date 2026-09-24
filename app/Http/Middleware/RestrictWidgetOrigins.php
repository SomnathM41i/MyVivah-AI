<?php

namespace App\Http\Middleware;

use App\Services\ValidatedPasetoToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Per-platform CORS origin restriction for widget routes (Phase 4).
 *
 * `config/cors.php` is deliberately wide (the widget is bearer-token protected,
 * so any-origin CORS leaks nothing on its own). Operators who want the API to
 * ALSO refuse response-bearing requests (including the browser preflight benefit
 * of light caching) from unknown origins can set `allowed_origins` on the
 * platform integration. This middleware is the dynamic half of that rule:
 *
 *   integration.allowed_origins = [ "https://a.example", "https://*.sub.example" ]
 *
 *   - when the list is EMPTY → passthrough (the wildcard config still applies)
 *   - when the request Origin does not match ANY entry (exact match or a `*.`
 *     prefix wildcard) after the global HandleCors middleware has run → the
 *     Access-Control-Allow-Origin header is STRIPPED so no browser will expose
 *     the response to that origin. Actual requests are never blocked server-side
 *     (only access control), because non-browser clients don't send Origins at all.
 */
class RestrictWidgetOrigins
{
    /**
     * @param  Closure(Request): Response  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (! is_array($origins = $this->allowedOrigins($request)) || $origins === []) {
            return $response;
        }

        $origin = $request->headers->get('Origin');

        if ($origin === null || $origin === '' || ! $this->matchesAny($origin, $origins)) {
            $response->headers->remove('Access-Control-Allow-Origin');
            $response->headers->remove('Access-Control-Expose-Headers');
            $response->headers->remove('Access-Control-Max-Age');
        }

        return $response;
    }

    /**
     * @return list<string>
     */
    private function allowedOrigins(Request $request): array
    {
        /** @var ValidatedPasetoToken|null $validated */
        $validated = $request->attributes->get('paseto');

        if (! $validated instanceof ValidatedPasetoToken) {
            return [];
        }

        $allowed = $validated->integration->allowed_origins ?? [];

        if (is_array($allowed)) {
            return array_values(array_filter(array_map('strval', $allowed), static fn ($o) => $o !== ''));
        }

        return [];
    }

    /**
     * @param  list<string>  $patterns
     */
    private function matchesAny(string $origin, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if ($this->matches($origin, $pattern)) {
                return true;
            }
        }

        return false;
    }

    private function matches(string $origin, string $pattern): bool
    {
        if ($pattern === $origin) {
            return true;
        }

        // A single `*` is a host-segment wildcard inside an otherwise exact URL,
        // e.g. `https://*.sub.example` → any subdomain of `sub.example`.
        if (substr_count($pattern, '*') === 1) {
            [$prefix, $suffix] = explode('*', $pattern, 2);

            return $prefix !== ''
                && str_starts_with($origin, $prefix)
                && str_ends_with($origin, $suffix)
                && strlen($origin) >= strlen($prefix) + strlen($suffix) + 1;
        }

        return false;
    }
}
