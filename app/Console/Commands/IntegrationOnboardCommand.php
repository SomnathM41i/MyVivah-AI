<?php

namespace App\Console\Commands;

use App\Services\PlatformOnboardingService;
use Illuminate\Console\Command;

/**
 * Idempotent programmatic onboarding for a platform integration (Phase 3C).
 *
 *   php artisan integration:onboard matrimonyband --name="Matrimony Band"
 *
 * Prints the platform `client_id` and, only on first provisioning, the
 * ONE-TIME `client_secret`. The secret is never stored or recoverable — if
 * lost, rotate keys: `POST /api/v1/integration/keys/rotate`.
 */
class IntegrationOnboardCommand extends Command
{
    protected $signature = 'integration:onboard
                            {slug : unique platform slug (URL-safe, used as the PASETO audience)}
                            {--name= : display name for the platform}
                            {--service=realtime_chat : service key to entitle on first provisioning}';

    protected $description = 'Provision a platform API integration (idempotent) and print one-time credentials.';

    public function handle(PlatformOnboardingService $onboard): int
    {
        $slug = (string) $this->argument('slug');
        $name = (string) $this->option('name');
        $service = (string) $this->option('service');

        [$platform, $integration, $secret] = $onboard->onboard($slug, $name, $service);

        $this->table(
            ['client_id', 'platform', 'status', 'integration', 'status'],
            [[
                $platform->public_id,
                $platform->slug,
                $platform->status,
                $integration->public_id,
                $integration->status,
            ]],
        );

        if ($secret !== null) {
            $this->warn('One-time client_secret — store it securely now, it will NEVER be shown again:');
            $this->line((string) $secret);
        } else {
            $this->info('A primary key already exists for this platform — no new secret was issued.');
            $this->line('To mint a fresh one-time secret: POST /api/v1/integration/keys/rotate');
        }

        return self::SUCCESS;
    }
}
