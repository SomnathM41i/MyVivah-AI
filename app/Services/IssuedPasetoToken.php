<?php

namespace App\Services;

use DateTimeImmutable;

/**
 * Result of an in-memory token issuance (never persisted raw token).
 *
 * @final
 */
class IssuedPasetoToken
{
    /**
     * @param  list<string>  $scopes
     */
    public function __construct(
        public readonly string $token,
        public readonly string $jti,
        public readonly DateTimeImmutable $issuedAt,
        public readonly DateTimeImmutable $expiresAt,
        public readonly int $ttlSeconds,
        public readonly array $scopes,
    ) {}

    public function expiresInSeconds(): int
    {
        return max(0, $this->expiresAt->getTimestamp() - $this->issuedAt->getTimestamp());
    }
}
