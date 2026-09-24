<?php

namespace App\Services;

use App\Models\ExternalUserMap;
use App\Models\Platform;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Basic per-user presence (Phase 3E) — REST-first, REALM-optional.
 *
 * State lives in the DB (`platform_external_user_map.presence_status` +
 * `presence_seen_at`), so it works with zero realtime infra and on shared
 * hosting. There is NO Redis (or any cache) dependency:
 *   - heartbeat() persists + BUMPS seen_at; broadcasts ONLY on transitions,
 *   - read()/state() compute staleness from seen_at (never from a TTL store),
 *   - sweep() flips stale online rows offline in bulk for cron.
 *
 * Principal: presence is a hint, never an authorization mechanism — channel
 * access is always re-checked server-side at subscription time.
 */
class PresenceService
{
    public function __construct(
        private readonly RealtimeBroadcaster $realtime,
    ) {}

    /**
     * Record a presence heartbeat and persist it. `$wanted` defaults to online;
     * an explicit offline heartbeat genuinely marks the user offline.
     *
     * @return array{external_user_id: string, presence_status: string, presence_seen_at: Carbon, changed: bool, offline_after_seconds: int}
     */
    public function heartbeat(Platform $platform, ExternalUserMap $user, ?string $wanted = null): array
    {
        $wanted = $this->normaliseOnlineOffline($wanted, ExternalUserMap::PRESENCE_ONLINE);
        $now = now();

        $previous = $user->presence_status ?? ExternalUserMap::PRESENCE_OFFLINE;
        if ($previous === ExternalUserMap::PRESENCE_ONLINE && $this->isStale($user, $now)) {
            $previous = ExternalUserMap::PRESENCE_OFFLINE;
        }

        $changed = $previous !== $wanted;

        // `last_seen_at` is the identity-map "seen" timestamp; a heartbeat is
        // also a sighting, so reconcile both in one write.
        $user->forceFill([
            'presence_status' => $wanted,
            'presence_seen_at' => $now,
            'last_seen_at' => $now,
        ])->save();

        if ($changed) {
            $this->realtime->presenceChanged($platform, $user, $wanted, $now);
        }

        return [
            'external_user_id' => $user->external_user_id,
            'presence_status' => $wanted,
            'presence_seen_at' => $now,
            'changed' => $changed,
            'offline_after_seconds' => $this->offlineAfterSeconds(),
        ];
    }

    /**
     * Effective presence for one map row, lazily transitioning STALE online →
     * offline (persisted + broadcast exactly once).
     *
     * @return array{external_user_id: string, presence_status: string, presence_seen_at: Carbon|null}
     */
    public function state(Platform $platform, ExternalUserMap $user): array
    {
        $now = now();

        if ($user->presence_status === ExternalUserMap::PRESENCE_ONLINE && $this->isStale($user, $now)) {
            $this->heartbeat($platform, $user, ExternalUserMap::PRESENCE_OFFLINE);
        }

        $user->refresh();

        $seen = $user->presence_seen_at;

        return [
            'external_user_id' => $user->external_user_id,
            'presence_status' => $user->presence_status,
            'presence_seen_at' => $seen instanceof Carbon ? $seen : ($seen === null ? null : Carbon::make($seen)),
        ];
    }

    /**
     * Bulk-marks stale online rows offline (broadcast each transition) and
     * returns the number flipped. Safe to run from cron every minute.
     */
    public function sweep(Platform $platform, ?Carbon $asOf = null): int
    {
        $asOf = $asOf ?? now();
        $cutoff = $asOf->copy()->subSeconds($this->stateIdleSeconds());

        $ids = ExternalUserMap::query()
            ->where('platform_id', $platform->id)
            ->where('presence_status', ExternalUserMap::PRESENCE_ONLINE)
            ->where(fn ($q) => $q->whereNull('presence_seen_at')->orWhere('presence_seen_at', '<', $cutoff))
            ->limit($this->sweepLimit())
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id);

        $flipped = 0;
        foreach ($ids as $id) {
            $flipped += DB::transaction(function () use ($platform, $id): int {
                /** @var ExternalUserMap|null $locked */
                $locked = ExternalUserMap::query()
                    ->whereKey($id)
                    ->lockForUpdate()
                    ->first();

                if (! $locked instanceof ExternalUserMap || $locked->presence_status !== ExternalUserMap::PRESENCE_ONLINE) {
                    return 0; // heartbeat won the race / already offline
                }

                $seen = $locked->presence_seen_at;
                if ($seen !== null && $seen->gte(now()->subSeconds($this->stateIdleSeconds()))) {
                    return 0; // heartbeat refreshed the row since we selected it
                }

                $locked->forceFill(['presence_status' => ExternalUserMap::PRESENCE_OFFLINE])->save();

                $this->realtime->presenceChanged(
                    $platform,
                    $locked,
                    ExternalUserMap::PRESENCE_OFFLINE,
                    now(),
                );

                return 1;
            });
        }

        return $flipped;
    }

    public function isOnline(ExternalUserMap $user, ?Carbon $asOf = null): bool
    {
        return $user->presence_status === ExternalUserMap::PRESENCE_ONLINE
            && ! $this->isStale($user, $asOf ?? now());
    }

    private function isStale(ExternalUserMap $user, Carbon $now): bool
    {
        $seen = $user->presence_seen_at;

        return $seen === null || $seen->lt($now->copy()->subSeconds($this->offlineAfterSeconds()));
    }

    private function normaliseOnlineOffline(?string $status, string $default): string
    {
        return $status === ExternalUserMap::PRESENCE_OFFLINE
            ? ExternalUserMap::PRESENCE_OFFLINE
            : $default;
    }

    private function offlineAfterSeconds(): int
    {
        return max(1, (int) config('chat.realtime.presence_offline_after_seconds', 90));
    }

    private function stateIdleSeconds(): int
    {
        return max(1, (int) config('chat.realtime.presence_sweep_idle_after_seconds', 120));
    }

    private function sweepLimit(): int
    {
        return max(1, (int) config('chat.realtime.presence_sweep_limit', 1000));
    }
}
