<?php

namespace App\Support;

use App\Models\Platform;
use App\Models\PlatformApiKey;
use ParagonIE\Paseto\Parser;

/**
 * Best-effort platform/key context from an UNVERIFIED bearer token.
 *
 * Used only for audit attribution on REJECTED requests: the footer `kid` is
 * unwrapped (non-cryptographic) and the owning key/platform loaded so denial
 * rows stay platform-scoped. Never trust the payload — the footer is visibly
 * signed, but we never act on ANY claim here beyond finding the key row.
 */
final class TokenKeyContext
{
    /**
     * @return array{0: Platform|null, 1: PlatformApiKey|null}
     */
    public static function resolve(string $token): array
    {
        try {
            $footer = json_decode(Parser::extractFooter($token), true);
            $kid = is_array($footer) && isset($footer['kid']) && is_string($footer['kid'])
                ? $footer['kid']
                : null;
        } catch (\Throwable) {
            return [null, null];
        }

        if ($kid === null || $kid === '') {
            return [null, null];
        }

        $key = PlatformApiKey::query()
            ->with('integration.platform')
            ->where('key_fingerprint', $kid)
            ->first();

        return $key === null
            ? [null, null]
            : [$key->integration->platform, $key];
    }
}
