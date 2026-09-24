<?php

namespace App\Services;

use App\Models\ExternalUserMap;
use App\Models\Platform;
use App\Models\PlatformApiKey;
use App\Models\PlatformIntegration;
use DateTimeImmutable;

/**
 * A validated widget session token + its fully hydrated context (Phase 4).
 *
 * The token is bound to ONE external user inside ONE platform. The resolved
 * `user` map is the ACTING identity for every downstream widget call — it can
 * never be influenced by a browser header (see ExternalUserContext).
 *
 * @final
 */
class ValidatedWidgetSession
{
    /**
     * @param  list<string>  $scopes
     */
    public function __construct(
        public readonly string $token,
        public readonly Platform $platform,
        public readonly PlatformIntegration $integration,
        public readonly PlatformApiKey $apiKey,
        public readonly string $jti,
        public readonly string $externalUserId,
        public readonly ExternalUserMap $user,
        public readonly string $audience,
        public readonly DateTimeImmutable $issuedAt,
        public readonly DateTimeImmutable $expiresAt,
        public readonly array $scopes,
    ) {}

    public function hasScope(string $scope): bool
    {
        return in_array($scope, $this->scopes, true);
    }

    public function expiresInSeconds(): int
    {
        return max(0, $this->expiresAt->getTimestamp() - $this->issuedAt->getTimestamp());
    }
}
