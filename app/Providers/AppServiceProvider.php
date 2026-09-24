<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Token-issuance throttle (phase-3a §14 / §12): per-IP burst guard on the
        // credential-exchange endpoint, keyed by client id when one is supplied
        // so a shared/NAT IP cannot starve other platforms. Named limiter so
        // tests can swap policy.
        RateLimiter::for('paseto.issue', function (Request $request): Limit {
            [$maxAttempts, $decayMinutes] = array_pad(
                explode(',', (string) config('paseto.issue_throttle', '5,1')),
                2,
                '1'
            );

            $clientId = (string) $request->input('client_id', '');
            $key = $clientId !== ''
                ? 'client:'.$clientId
                : 'ip:'.(string) $request->ip();

            return Limit::perMinutes((int) $decayMinutes, max(1, (int) $maxAttempts))
                ->by($key);
        });
    }
}
