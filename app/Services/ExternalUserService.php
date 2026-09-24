<?php

namespace App\Services;

use App\Models\ExternalUserMap;
use App\Models\Platform;
use DateTimeInterface;
use Illuminate\Database\QueryException;

/**
 * External→local user identity mapping (phase-3a-api-integration-plan.md §8).
 *
 * Rules enforced here:
 *   - Strict platform isolation — every read/write is scoped by `platform_id`
 *     derived from the verified token, never from the request body.
 *   - Identity key UNIQUE(platform_id, external_user_id); a concurrent create
 *     that trips the unique constraint is reconciled by one re-read + update.
 *   - The external platform stays the source of truth: we persist the mapping +
 *     sync timestamps + minimal integration metadata, never a profile mirror.
 */
class ExternalUserService
{
    /**
     * Upsert the identity mapping for an external user.
     *
     * Platform context is passed explicitly from the verified token. Returns the
     * map with Eloquent's `wasRecentlyCreated` set when a new row was inserted.
     */
    public function resolveOrCreate(
        Platform $platform,
        string $externalUserId,
        ?string $localPublicId = null,
        ?array $metadata = null,
        ?DateTimeInterface $now = null,
    ): ExternalUserMap {
        $now = $now ?? now();

        // Retry once on unique-violation so concurrent first-time upserts converge.
        for ($attempt = 1; ; $attempt++) {
            try {
                return $this->upsertOnce($platform, $externalUserId, $localPublicId, $metadata, $now);
            } catch (QueryException $ex) {
                if ($attempt >= 2 || ! $this->isUniqueViolation($ex)) {
                    throw $ex;
                }
            }
        }
    }

    /**
     * Resolve an external user strictly within one platform's namespace.
     */
    public function resolve(Platform $platform, string $externalUserId): ?ExternalUserMap
    {
        return ExternalUserMap::query()
            ->where('platform_id', $platform->id)
            ->where('external_user_id', $externalUserId)
            ->first();
    }

    private function upsertOnce(
        Platform $platform,
        string $externalUserId,
        ?string $localPublicId,
        ?array $metadata,
        DateTimeInterface $now,
    ): ExternalUserMap {
        $map = $this->resolve($platform, $externalUserId);

        if ($map === null) {
            return ExternalUserMap::query()->create([
                'platform_id' => $platform->id,
                'external_user_id' => $externalUserId,
                'local_public_id' => $localPublicId,
                'metadata' => $metadata,
                'synced_at' => $now,
                'last_seen_at' => $now,
            ]);
        }

        $map->forceFill([
            'synced_at' => $now,
            'last_seen_at' => $now,
        ]);
        if ($localPublicId !== null) {
            $map->local_public_id = $localPublicId;
        }
        if ($metadata !== null) {
            $map->metadata = $metadata;
        }
        $map->save();

        return $map;
    }

    private function isUniqueViolation(QueryException $ex): bool
    {
        $message = $ex->getMessage();

        // SQLSTATE 23000 = integrity constraint violation (MySQL: 1062 duplicate;
        // SQLite: 1555 UNIQUE constraint failed).
        return str_contains($message, 'SQLSTATE[23000]')
            || str_contains($message, 'SQLSTATE[23505]')
            || str_contains($message, 'UNIQUE constraint failed')
            || str_contains($message, 'platform_external_user_identity');
    }
}
