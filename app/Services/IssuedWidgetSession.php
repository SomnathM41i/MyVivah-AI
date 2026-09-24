<?php

namespace App\Services;

use DateTimeImmutable;

/**
 * Result of an in-memory widget session issuance (never persisted raw token).
 *
 * @final
 */
class IssuedWidgetSession
{
    /**
     * @param  list<string>  $scopes
     */
    public function __construct(
        public readonly string $token,
        public readonly string $externalUserId,
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
