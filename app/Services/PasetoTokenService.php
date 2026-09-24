<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Platform;
use App\Models\PlatformApiKey;
use App\Models\PlatformIntegration;
use App\Models\PlatformServiceAccess;
use DateTimeImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use ParagonIE\Paseto\Builder;
use ParagonIE\Paseto\Exception\PasetoException;
use ParagonIE\Paseto\Exception\RuleViolation;
use ParagonIE\Paseto\JsonToken;
use ParagonIE\Paseto\Parser;
use ParagonIE\Paseto\Protocol\Version4;
use ParagonIE\Paseto\ProtocolCollection;

/**
 * PASETO v4.local token issuance + validation (ADR-013, phase-3a §6).
 *
 * v4.local ONLY — the parser allow-list is restricted to Version4 and Purpose
 * local, so v1–v3 and `public` tokens are rejected cryptographically (no alg
 * confusion). No hand-rolled crypto; paragonie/paseto does AEAD (XChaCha20-Poly1305).
 */
class PasetoTokenService
{
    /**
     * Issue a short-lived v4.local token for a platform, signed with one of its
     * symmetric keys. `sub` = platform public_id, `aud` = "platform:{slug}",
     * `platform_id` = public_id, `kid` (footer + claim) = key fingerprint.
     *
     * @param  list<string>  $scopes
     */
    public function issue(
        PlatformIntegration $integration,
        PlatformApiKey $key,
        array $scopes,
        ?\DateTimeInterface $now = null
    ): IssuedPasetoToken {
        $now = $now ?? new DateTimeImmutable;
        $now = DateTimeImmutable::createFromInterface($now);

        if (! in_array(PlatformIntegration::PASETO_V4_LOCAL, [$integration->paseto_version], true)) {
            throw new ApiException(
                'INVALID_TOKEN',
                'This integration does not support v4.local tokens.',
                401
            );
        }

        $jti = (string) Str::uuid();
        $ttl = $this->ttlFor($integration);
        $expiresAt = $now->modify("+{$ttl} seconds");

        $builder = Builder::getLocal($key->toSymmetricKey(), new Version4);
        $builder
            ->setAudience('platform:'.$integration->platform->slug)
            ->setSubject($integration->platform->public_id)
            ->setJti($jti)
            ->setIssuedAt($now)
            ->setExpiration($expiresAt)
            ->set('platform_id', $integration->platform->public_id)
            ->set('kid', $key->key_fingerprint)
            ->set('scope', $scopes)
            ->setFooterArray(['kid' => $key->key_fingerprint]);

        return new IssuedPasetoToken(
            token: $builder->toString(),
            jti: $jti,
            issuedAt: DateTimeImmutable::createFromInterface($now),
            expiresAt: DateTimeImmutable::createFromInterface($expiresAt),
            ttlSeconds: $ttl,
            scopes: $scopes,
        );
    }

    /**
     * Validate a presented bearer token end-to-end:
     *
     *   1. cryptographically verify (v4.local ONLY + purpose local) with the key
     *      identified by the footer `kid` fingerprint;
     *   2. enforce `exp` (parser rules);
     *   3. enforce platform isolation — `aud`/`sub`/`platform_id` claims MUST match
     *      the platform owning the key (never trusted from the client);
     *   4. reject revoked keys, integrations, and platforms.
     *
     * @throws ApiException INVALID_TOKEN / TOKEN_EXPIRED / TOKEN_REVOKED /
     *                      PLATFORM_MISMATCH / PLATFORM_SUSPENDED
     */
    public function validate(string $token, ?\DateTimeInterface $now = null): ValidatedPasetoToken
    {
        $now = $now ?? new DateTimeImmutable;

        $apiKey = $this->keyFromToken($token, $now);
        $decoded = $this->parseVersion4Local($token, $apiKey);

        $this->assertClaimsMatchPlatform($decoded, $apiKey->integration->platform);

        if ($this->isJtiRevoked($decoded->getJti())) {
            throw new ApiException('TOKEN_REVOKED', 'This token has been revoked.', 401);
        }

        $claims = $decoded->getClaims();
        $scopes = isset($claims['scope']) && is_array($claims['scope'])
            ? array_values(array_map('strval', $claims['scope']))
            : [];

        return new ValidatedPasetoToken(
            token: $token,
            platform: $apiKey->integration->platform,
            integration: $apiKey->integration,
            apiKey: $apiKey,
            jti: $decoded->getJti(),
            subject: $decoded->getSubject(),
            audience: $decoded->getAudience(),
            issuedAt: DateTimeImmutable::createFromInterface($decoded->getIssuedAt()),
            expiresAt: DateTimeImmutable::createFromInterface($decoded->getExpiration()),
            scopes: $scopes,
        );
    }

    /**
     * Resolve the API key a token claims (`kid` footer), verifying key +
     * integration lifecycle first. Shared custom-token seam (e.g. widget
     * sessions) so crypto + key-lookup logic never forks.
     *
     * @throws ApiException INVALID_TOKEN / TOKEN_REVOKED / PLATFORM_SUSPENDED
     */
    public function keyFromToken(string $token, ?\DateTimeInterface $now = null): PlatformApiKey
    {
        try {
            $kid = $this->kidFromFooter(Parser::extractFooter($token));
        } catch (ApiException $ex) {
            throw $ex;
        } catch (\Throwable $ex) {
            throw new ApiException('INVALID_TOKEN', 'Malformed PASETO.', 401, null, $ex);
        }

        $apiKey = PlatformApiKey::query()
            ->with(['integration.platform'])
            ->where('key_fingerprint', $kid)
            ->first();

        if ($apiKey === null) {
            throw new ApiException('INVALID_TOKEN', 'Unknown key identifier.', 401);
        }

        $this->assertKeyUsable($apiKey, $now);
        $this->assertIntegrationUsable($apiKey->integration);

        return $apiKey;
    }

    /**
     * Parse + cryptographically verify a v4.local token with a resolved key
     * (v4-only allow-list, signature, `exp`). Shared by platform AND widget
     * token validation.
     *
     * @throws ApiException TOKEN_EXPIRED / INVALID_TOKEN
     */
    public function parseVersion4Local(string $token, PlatformApiKey $apiKey): JsonToken
    {
        try {
            $parser = Parser::getLocal(
                $apiKey->toSymmetricKey(),
                new ProtocolCollection(new Version4)
            );

            return $parser->parse($token);
        } catch (RuleViolation $ex) {
            throw new ApiException('TOKEN_EXPIRED', $ex->getMessage(), 401, null, $ex);
        } catch (PasetoException $ex) {
            throw new ApiException('INVALID_TOKEN', 'Token validation failed.', 401, null, $ex);
        } catch (\Throwable $ex) {
            throw new ApiException('INVALID_TOKEN', 'Token validation failed.', 401, null, $ex);
        }
    }

    /**
     * Whether a token id has been blacklisted (speeds up custom-token seams
     * without re-validating the whole payload).
     */
    public function isJtiRevoked(string $jti): bool
    {
        return Cache::has('paseto.jti.revoked.'.$jti);
    }

    /**
     * Blacklist a token's `jti` for the remainder of its lifetime (immediate revocation).
     */
    public function revoke(string $token): void
    {
        try {
            $footer = Parser::extractFooter($token);
            $kid = $this->kidFromFooter($footer);
        } catch (\Throwable $ex) {
            throw new ApiException('INVALID_TOKEN', 'Malformed PASETO.', 401, null, $ex);
        }

        $apiKey = PlatformApiKey::query()->where('key_fingerprint', $kid)->first();
        if ($apiKey === null) {
            throw new ApiException('INVALID_TOKEN', 'Unknown key identifier.', 401);
        }

        $validated = $this->validate($token);
        $ttl = max(1, $validated->expiresAt->getTimestamp() - $validated->issuedAt->getTimestamp());
        Cache::put(
            'paseto.jti.revoked.'.$validated->jti,
            true,
            $ttl,
        );
    }

    /**
     * Scopes granted to a platform at issue time, derived from T8 entitlements
     * (never client-supplied). `authentication` is always included so auth-only
     * self-describe endpoints stay protected.
     *
     * @return list<string>
     */
    public function scopesFor(Platform $platform): array
    {
        $scopes = [config('paseto.authentication_scope')];

        $entitlements = PlatformServiceAccess::query()
            ->with('service')
            ->where('platform_id', $platform->id)
            ->where('has_access', true)
            ->where(fn ($q) => $q->whereNull('effective_until')->orWhere('effective_until', '>=', now()))
            ->get();

        foreach ($entitlements as $entitlement) {
            $map = config("paseto.service_scopes.{$entitlement->service->key}", []);
            foreach ($map as $scope) {
                if (is_string($scope) && ! in_array($scope, $scopes, true)) {
                    $scopes[] = $scope;
                }
            }
        }

        return $scopes;
    }

    /**
     * Parse the JSON footer and return its `kid` (or throw).
     */
    private function kidFromFooter(string $footer): string
    {
        $decoded = json_decode($footer, true);
        if (! is_array($decoded) || ! isset($decoded['kid']) || ! is_string($decoded['kid'])) {
            throw new ApiException('INVALID_TOKEN', 'Missing key identifier.', 401);
        }

        return $decoded['kid'];
    }

    private function assertKeyUsable(PlatformApiKey $key, ?\DateTimeInterface $now = null): void
    {
        // A rotated key presented after its grace window is hard-revoked on sight
        // (phase-3a §6.3) — the state transition is persisted so later attempts fail.
        if ($key->status === PlatformApiKey::STATUS_ROTATED && ! $key->isUsable($now)) {
            $key->hardRevoke($now);

            throw new ApiException(
                'TOKEN_REVOKED',
                'This API key was rotated and its grace period has expired.',
                401
            );
        }

        if (! $key->isUsable($now)) {
            throw new ApiException('TOKEN_REVOKED', 'This API key has been revoked.', 401);
        }
    }

    private function assertIntegrationUsable(PlatformIntegration $integration): void
    {
        if ($integration->revoked_at !== null) {
            throw new ApiException('TOKEN_REVOKED', 'This integration has been revoked.', 401);
        }
        if ($integration->status !== PlatformIntegration::STATUS_ACTIVE) {
            throw new ApiException(
                'PLATFORM_SUSPENDED',
                'This integration is not active.',
                403,
            );
        }
        if ($integration->platform->status !== Platform::STATUS_ACTIVE) {
            throw new ApiException(
                'PLATFORM_SUSPENDED',
                'This platform is not active.',
                403,
            );
        }
    }

    private function assertClaimsMatchPlatform($decoded, Platform $platform): void
    {
        if ($decoded->getAudience() !== 'platform:'.$platform->slug) {
            throw new ApiException('PLATFORM_MISMATCH', 'Token audience does not match this platform.', 403);
        }
        if ($decoded->getSubject() !== $platform->public_id) {
            throw new ApiException('PLATFORM_MISMATCH', 'Token subject does not match this platform.', 403);
        }
        if ($decoded->get('platform_id') !== $platform->public_id) {
            throw new ApiException('PLATFORM_MISMATCH', 'Token payload does not match this platform.', 403);
        }
    }

    private function ttlFor(PlatformIntegration $integration): int
    {
        $default = (int) config('paseto.default_ttl_seconds', 3600);
        $max = (int) config('paseto.max_ttl_seconds', 3600);
        $ttl = (int) ($integration->token_ttl_seconds ?: $default);

        return min(max(1, $ttl), $max);
    }
}
