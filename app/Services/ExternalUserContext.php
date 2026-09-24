<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\ExternalUserMap;
use App\Models\Platform;
use Illuminate\Http\Request;

/**
 * Resolves the ACTING external user for a chat API call.
 *
 * Server-to-server chat endpoints are authenticated by the PLATFORM (PASETO);
 * the platform then asserts WHICH of its own users is acting via the
 * `X-External-User-Id` header. The platform is the source of truth for its
 * users, so this header + the verified platform token is the correct trust
 * boundary — it is still strictly platform-scoped: an id only resolves inside
 * the calling platform's own `platform_external_user_map`, so a value that
 * belongs to another platform can never be "found".
 *
 * WIDGET calls (Phase 4) are different and safer: the acting user is bound INSIDE
 * the verified widget session token (`sub` + `external_user_id` claim) — the
 * browser can never pick its own identity. When a `widget_session` request
 * attribute is present it takes absolute priority and the `X-External-User-Id`
 * header is IGNORED entirely (a spoofed header cannot override an already-issued
 * session, and never triggers mapping based on attacker-controlled input).
 */
class ExternalUserContext
{
    public const HEADER = 'X-External-User-Id';

    /**
     * Resolve the acting external user strictly inside one platform's namespace.
     *
     * @throws ApiException VALIDATION_FAILED (missing/oversized header) or
     *                      NOT_FOUND (id not mapped for this platform)
     */
    public function resolve(Request $request, Platform $platform): ExternalUserMap
    {
        $widget = $request->attributes->get('widget_session');
        if ($widget instanceof ValidatedWidgetSession) {
            if ($widget->platform->isNot($platform)) {
                throw new ApiException('PLATFORM_MISMATCH', 'Widget session platform mismatch.', 403);
            }

            return $widget->user;
        }

        $externalUserId = trim((string) $request->header(self::HEADER));

        if ($externalUserId === '' || mb_strlen($externalUserId) > 255) {
            throw new ApiException(
                'VALIDATION_FAILED',
                'The '.self::HEADER.' header is required (1-255 chars).',
                422
            );
        }

        $map = ExternalUserMap::query()
            ->where('platform_id', $platform->id)
            ->where('external_user_id', $externalUserId)
            ->first();

        if ($map === null) {
            throw new ApiException(
                'NOT_FOUND',
                'No identity mapping found for this external user.',
                404
            );
        }

        return $map;
    }
}
