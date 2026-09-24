<?php

namespace App\Services;

use App\Models\Platform;
use App\Models\PlatformApiKey;
use App\Models\PlatformIntegration;
use DateTimeImmutable;

/**
 * A successfully validated token + its fully hydrated platform context.
 * Every downstream authorization decision reads from this object — never from
 * client-supplied payloads.
 *
 * @final
 */
class ValidatedPasetoToken
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
        public readonly string $subject,
        public readonly string $audience,
        public readonly DateTimeImmutable $issuedAt,
        public readonly DateTimeImmutable $expiresAt,
        public readonly array $scopes,
    ) {}

    public function hasScope(string $scope): bool
    {
        return in_array($scope, $this->scopes, true);
    }

    public function hasAllScopes(array $scopes): bool
    {
        foreach ($scopes as $scope) {
            if (! $this->hasScope($scope)) {
                return false;
            }
        }

        return true;
    }
}
