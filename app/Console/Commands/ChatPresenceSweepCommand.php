<?php

namespace App\Console\Commands;

use App\Models\Platform;
use App\Services\PresenceService;
use Illuminate\Console\Command;

/**
 * Mark stale-online users offline and broadcast the transition (Phase 3E).
 *
 * Shared-hosting friendly: DB-backed staleness (no Redis), one transaction per
 * row, bounded batch. Expected run from cron every minute:
 *   * * * * * php /path/to/artisan chat:presence-sweep
 * Optional --platform=<public_id|slug> narrows the sweep to one platform.
 */
class ChatPresenceSweepCommand extends Command
{
    protected $signature = 'chat:presence-sweep
                            {--platform= : optional platform public_id or slug to limit the sweep}';

    protected $description = 'Mark stale-online chat users offline and broadcast user.offline.';

    public function handle(PresenceService $presence): int
    {
        $platforms = Platform::query()
            ->where('status', Platform::STATUS_ACTIVE);

        $scope = $this->option('platform');
        if (is_string($scope) && $scope !== '') {
            $platforms->where(fn ($q) => $q->where('public_id', $scope)->orWhere('slug', $scope));
        }

        $total = 0;
        foreach ($platforms->cursor() as $platform) {
            /** @var Platform $platform */
            $total += $presence->sweep($platform);
        }

        $this->info("Presence sweep complete: {$total} user(s) marked offline.");

        return self::SUCCESS;
    }
}
