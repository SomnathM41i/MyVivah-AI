<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Models\ApiAuditLog;
use App\Services\ApiAuditService;
use App\Services\ValidatedPasetoToken;
use App\Services\ValidatedWidgetSession;
use App\Services\WidgetSessionService;
use App\Support\TokenKeyContext;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Authenticate a widget (browser) API request via `Authorization: Bearer`.
 *
 * Widget tokens are issued by /api/v1/widget/session (a server-to-server
 * endpoint). They are v4.local like platform tokens but carry widget-claim
 * isolation (`aud = "widget:{slug}"`, identity = token-bound user). This
 * middleware:
 *
 *   1. fully validates the widget token (signature, v4-only allow-list, exp,
 *      widget claims, revocation, mapped identity),
 *   2. sets `widget_session` (the authoritative ValidatedWidgetSession — used by
 *      ExternalUserContext so the browser can never impersonate another user), and
 *   3. ALSO sets an adapted `paseto` (ValidatedPasetoToken shaped from the widget
 *      session) so ALL downstream scope/entitlement/audit/rate-limit middleware
 *      and controllers run unchanged.
 *
 * Rejections mirror platform-token audit events (never discloses payload data).
 */
class ValidateWidgetToken
{
    public function __construct(
        private readonly WidgetSessionService $sessions,
        private readonly ApiAuditService $audit,
    ) {}

    /**
     * @param  Closure(Request): mixed  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization', '');
        if (! is_string($header) || ! str_starts_with($header, 'Bearer ')) {
            $this->auditInvalid($request, ApiAuditLog::EVENT_INVALID_TOKEN, 401);

            return $this->unauthorized('INVALID_TOKEN', 'Missing bearer token.');
        }

        $token = trim(substr($header, 7));
        if ($token === '') {
            $this->auditInvalid($request, ApiAuditLog::EVENT_INVALID_TOKEN, 401);

            return $this->unauthorized('INVALID_TOKEN', 'Missing bearer token.');
        }

        try {
            $widget = $this->sessions->validate($token);
        } catch (ApiException $ex) {
            $this->auditRejection($request, $ex, $token);

            return $this->unauthorized($ex->errorCode(), $ex->getMessage(), $ex->status());
        }

        $request->attributes->set('widget_session', $widget);
        $request->attributes->set('paseto', $this->adapt($widget));

        return $next($request);
    }

    /**
     * Shape the widget session as a ValidatedPasetoToken so existing middleware
     * (EnsurePlatformAccess / LogApiAudit / EnforcePlatformRateLimit) treats the
     * widget caller like any platform-authenticated caller.
     */
    private function adapt(ValidatedWidgetSession $widget): ValidatedPasetoToken
    {
        return new ValidatedPasetoToken(
            token: $widget->token,
            platform: $widget->platform,
            integration: $widget->integration,
            apiKey: $widget->apiKey,
            jti: $widget->jti,
            subject: $widget->externalUserId,
            audience: $widget->audience,
            issuedAt: $widget->issuedAt,
            expiresAt: $widget->expiresAt,
            scopes: $widget->scopes,
        );
    }

    protected function unauthorized(string $code, string $message, int $status = 401): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $code,
                'status' => $status,
                'message' => $message,
                'request_id' => (string) Str::uuid(),
            ],
        ], $status);
    }

    private function auditRejection(Request $request, ApiException $ex, string $token): void
    {
        $code = $ex->errorCode();

        switch ($code) {
            case 'TOKEN_EXPIRED':
                $event = ApiAuditLog::EVENT_TOKEN_EXPIRED;
                break;
            case 'PLATFORM_MISMATCH':
                $event = ApiAuditLog::EVENT_WRONG_PLATFORM;
                break;
            case 'TOKEN_REVOKED':
                $event = ApiAuditLog::EVENT_TOKEN_REJECTED;
                break;
            default:
                $event = ApiAuditLog::EVENT_INVALID_TOKEN;
                break;
        }

        [$platform, $key] = TokenKeyContext::resolve($token);

        $this->audit->record($event, $platform, $key, [
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'status_code' => $ex->status(),
            'ip_hash' => hash('sha256', $request->ip() ?? ''),
            'reason' => $code.' (widget session)',
        ]);
    }

    private function auditInvalid(Request $request, string $event, int $status): void
    {
        $this->audit->record($event, null, null, [
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'status_code' => $status,
            'ip_hash' => hash('sha256', $request->ip() ?? ''),
            'reason' => $event.' (widget session)',
        ]);
    }
}
