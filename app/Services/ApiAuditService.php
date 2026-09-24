<?php

namespace App\Services;

use App\Models\ApiAuditLog;
use App\Models\Platform;
use App\Models\PlatformApiKey;
use Illuminate\Support\Facades\Log;

/**
 * Append-only security/integration audit writer (phase-3a §5.2/§13).
 *
 * Design rules:
 *   - One writer. Every audit row flows through record().
 *   - FAIL-OPEN: an audit write must never break the request it describes —
 *     exceptions are caught and logged, the caller continues.
 *   - Privacy: only event + checksums/ip_hash + minimal metadata. Raw secrets,
 *     token bodies, and PII are never stored.
 */
class ApiAuditService
{
    /**
     * Record a single audit event.
     *
     * @param  array<string, mixed>  $context  optional request/response dimensions
     *                                         (ip_hash, endpoint, method, status_code,
     *                                         duration_ms, request_checksum, response_checksum)
     *                                         — anything else is merged into `metadata`.
     */
    public function record(
        string $event,
        ?Platform $platform = null,
        ?PlatformApiKey $key = null,
        array $context = [],
    ): void {
        try {
            $data = array_filter([
                'platform_id' => $platform?->id,
                'api_key_id' => $key?->id,
                'event' => $event,
            ], fn ($v) => $v !== null);

            foreach (self::DIMENSIONS as $dimension) {
                if (array_key_exists($dimension, $context)) {
                    $data[$dimension] = $context[$dimension];
                }
            }

            $metadata = array_diff_key($context, array_flip(self::DIMENSIONS));
            if ($metadata !== []) {
                $data['metadata'] = $metadata;
            }

            ApiAuditLog::query()->create($data);
        } catch (\Throwable $ex) {
            Log::error('Audit write failed', [
                'event' => $event,
                'error' => $ex->getMessage(),
            ]);
        }
    }

    public function tokenIssued(Platform $platform, PlatformApiKey $key, array $context = []): void
    {
        $this->record(ApiAuditLog::EVENT_TOKEN_ISSUED, $platform, $key, $context);
    }

    public function tokenRejected(?Platform $platform = null, ?PlatformApiKey $key = null, array $context = []): void
    {
        $this->record(ApiAuditLog::EVENT_TOKEN_REJECTED, $platform, $key, $context);
    }

    public function tokenExpired(?Platform $platform = null, ?PlatformApiKey $key = null, array $context = []): void
    {
        $this->record(ApiAuditLog::EVENT_TOKEN_EXPIRED, $platform, $key, $context);
    }

    public function invalidToken(?Platform $platform = null, ?PlatformApiKey $key = null, array $context = []): void
    {
        $this->record(ApiAuditLog::EVENT_INVALID_TOKEN, $platform, $key, $context);
    }

    public function wrongPlatform(?Platform $platform = null, ?PlatformApiKey $key = null, array $context = []): void
    {
        $this->record(ApiAuditLog::EVENT_WRONG_PLATFORM, $platform, $key, $context);
    }

    public function insufficientScope(?Platform $platform = null, ?PlatformApiKey $key = null, array $context = []): void
    {
        $this->record(ApiAuditLog::EVENT_INSUFFICIENT_SCOPE, $platform, $key, $context);
    }

    /**
     * Record a key rotation. The fingerprints (kids) are safe to store; the raw
     * secret survives only in the caller's return value (one-time).
     */
    public function keyRotated(
        Platform $platform,
        ?PlatformApiKey $previous,
        ?PlatformApiKey $current,
        array $context = [],
    ): void {
        $this->record(ApiAuditLog::EVENT_KEY_ROTATION, $platform, $current, $context);
    }

    /**
     * Column dimensions of api_audit_logs (everything else goes to `metadata`).
     *
     * @var list<string>
     */
    private const DIMENSIONS = [
        'ip_hash',
        'endpoint',
        'method',
        'status_code',
        'duration_ms',
        'request_checksum',
        'response_checksum',
    ];
}
