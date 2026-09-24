<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\ExternalUserMap;
use App\Models\Platform;
use App\Models\PlatformApiKey;
use App\Models\PlatformIntegration;
use DateTimeImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use ParagonIE\Paseto\Builder;
use ParagonIE\Paseto\JsonToken;
use ParagonIE\Paseto\Protocol\Version4;

/**
 * Widget session PASETO issuance + validation (Phase 4).
 *
 * A widget session token is v4.local (same symmetric keys as platform tokens —
 * the CLIENT backend calls this through /api/v1/widget/session, so its secret is
 * still only ever held server-side), but its claims are DIFFERENT and narrower:
 *
 *   aud = "widget:{slug}"       (never `platform:{slug}` — see WidgetChannels)
 *   sub = external_user_id      (the browser user, not the platform)
 *   platform_id / external_user_id claims
 *   scope = [realtime_chat:read, realtime_chat:write]
 *
 * Because the `aud`/`platform_id` claim checks are audience-prefix-specific, a
 * widget token can never be replayed on platform-token routes (and vice versa).
 */
class WidgetSessionService
{
    /** Exactly the two chat scopes the widget needs (and nothing else). */
    public const CHAT_SCOPES = ['realtime_chat:read', 'realtime_chat:write'];

    public function __construct(
        private readonly PasetoTokenService $tokens,
        private readonly ExternalUserService $users,
    ) {}

    public static function audiencePrefix(): string
    {
        return (string) config('widget.audience_prefix', 'widget:');
    }

    public static function audienceFor(Platform $platform): string
    {
        return self::audiencePrefix().$platform->slug;
    }

    /**
     * Mint a short-lived widget session for one external user of one platform,
     * signed with one of the platform's symmetric keys.
     *
     * @throws ApiException INVALID_TOKEN (unsupported PASETO version)
     */
    public function issue(
        PlatformIntegration $integration,
        PlatformApiKey $key,
        string $externalUserId,
        ?\DateTimeInterface $now = null,
    ): IssuedWidgetSession {
        $now = DateTimeImmutable::createFromInterface($now ?? new DateTimeImmutable);

        if (! in_array(PlatformIntegration::PASETO_V4_LOCAL, [$integration->paseto_version], true)) {
            throw new ApiException(
                'INVALID_TOKEN',
                'This integration does not support v4.local tokens.',
                401
            );
        }

        $jti = (string) Str::uuid();
        $ttl = $this->ttlSeconds();
        $expiresAt = $now->modify("+{$ttl} seconds");
        $scopes = self::CHAT_SCOPES;

        $builder = Builder::getLocal($key->toSymmetricKey(), new Version4);
        $builder
            ->setAudience(self::audienceFor($integration->platform))
            ->setSubject($externalUserId)
            ->setJti($jti)
            ->setIssuedAt($now)
            ->setExpiration($expiresAt)
            ->set('platform_id', $integration->platform->public_id)
            ->set('external_user_id', $externalUserId)
            ->set('kid', $key->key_fingerprint)
            ->set('scope', $scopes)
            ->setFooterArray(['kid' => $key->key_fingerprint]);

        return new IssuedWidgetSession(
            token: $builder->toString(),
            externalUserId: $externalUserId,
            jti: $jti,
            issuedAt: $now,
            expiresAt: $expiresAt,
            ttlSeconds: $ttl,
            scopes: $scopes,
        );
    }

    /**
     * Validate a presented widget session token end-to-end:
     *
     *   1. cryptographically verify (v4.local only) with the key from the footer kid;
     *   2. enforce `exp`;
     *   3. enforce widget-claim isolation — `aud`/`subject`/`platform_id` MUST
     *      match the platform owning the key AND `subject` === `external_user_id`
     *      claim (the browser can never pick its own identity);
     *   4. resolve the ACTING user strictly inside that platform's identity map
     *      (a token for an un-mapped user is rejected — no implicit upsert);
     *   5. reject revoked keys/integrations/platforms and revoked jtis.
     *
     * @throws ApiException INVALID_TOKEN / TOKEN_EXPIRED / TOKEN_REVOKED /
     *                      PLATFORM_MISMATCH / PLATFORM_SUSPENDED
     */
    public function validate(string $token, ?\DateTimeInterface $now = null): ValidatedWidgetSession
    {
        $now = $now ?? new DateTimeImmutable;

        $apiKey = $this->tokens->keyFromToken($token, $now);
        $decoded = $this->tokens->parseVersion4Local($token, $apiKey);
        $platform = $apiKey->integration->platform;

        $this->assertWidgetClaims($decoded, $platform);

        $externalUserId = $decoded->getSubject();
        $user = $this->users->resolve($platform, $externalUserId);

        if (! $user instanceof ExternalUserMap) {
            throw new ApiException(
                'INVALID_TOKEN',
                'Widget session identity is not mapped for this platform.',
                401
            );
        }

        if ($this->tokens->isJtiRevoked($decoded->getJti())) {
            throw new ApiException('TOKEN_REVOKED', 'This widget session has been revoked.', 401);
        }

        $claims = $decoded->getClaims();
        $scopes = isset($claims['scope']) && is_array($claims['scope'])
            ? array_values(array_map('strval', $claims['scope']))
            : [];

        return new ValidatedWidgetSession(
            token: $token,
            platform: $platform,
            integration: $apiKey->integration,
            apiKey: $apiKey,
            jti: $decoded->getJti(),
            externalUserId: $externalUserId,
            user: $user,
            audience: $decoded->getAudience(),
            issuedAt: DateTimeImmutable::createFromInterface($decoded->getIssuedAt()),
            expiresAt: DateTimeImmutable::createFromInterface($decoded->getExpiration()),
            scopes: $scopes,
        );
    }

    /**
     * Blacklist a validated widget session's `jti` for the remainder of its life.
     */
    public function revoke(ValidatedWidgetSession $session): void
    {
        $ttl = max(1, $session->expiresInSeconds());

        Cache::put(
            'paseto.jti.revoked.'.$session->jti,
            true,
            $ttl,
        );
    }

    /**
     * The `data` block returned to the browser for a freshly issued session
     * (shared by the widget/session endpoint and the local demo).
     *
     * @param  array<string, mixed>  $realtime
     * @return array<string, mixed>
     */
    public function present(IssuedWidgetSession $issued, ExternalUserMap $map, array $realtime): array
    {
        return [
            'token_type' => 'Widget',
            'access_token' => $issued->token,
            'expires_in' => $issued->expiresInSeconds(),
            'expires_at' => $issued->expiresAt->format(DATE_ATOM),
            'self' => [
                'external_user_id' => $map->external_user_id,
                'local_public_id' => $map->local_public_id,
                'presence_status' => $map->presence_status ?? ExternalUserMap::PRESENCE_OFFLINE,
                'presence_seen_at' => $map->presence_seen_at?->toISOString(),
            ],
            'channels' => [
                'private' => (string) config('chat.realtime.channel_names.private', 'private-chat'),
                'presence' => (string) config('chat.realtime.channel_names.presence', 'presence-chat'),
            ],
            'realtime' => $realtime,
        ];
    }

    private function assertWidgetClaims(JsonToken $decoded, Platform $platform): void
    {
        if ($decoded->getAudience() !== self::audienceFor($platform)) {
            throw new ApiException('PLATFORM_MISMATCH', 'Widget session does not belong to this platform.', 403);
        }

        if ($decoded->get('platform_id') !== $platform->public_id) {
            throw new ApiException('PLATFORM_MISMATCH', 'Widget session payload does not match this platform.', 403);
        }

        $subject = $decoded->getSubject();
        if ($subject === '') {
            throw new ApiException('INVALID_TOKEN', 'Widget session is missing its user identity.', 401);
        }

        if ($decoded->get('external_user_id') !== $subject) {
            throw new ApiException('PLATFORM_MISMATCH', 'Widget session user claims do not match.', 403);
        }
    }

    private function ttlSeconds(): int
    {
        $default = (int) config('widget.session.ttl_seconds', 900);
        $max = (int) config('widget.session.max_ttl_seconds', 1800);

        return min(max(1, $default), $max);
    }
}
