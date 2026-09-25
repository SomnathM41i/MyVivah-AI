<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Platform;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ExternalPlatformUserSearch
{
    /** @return array{results: list<array<string, mixed>>, next_cursor: ?string, has_more: bool} */
    public function search(Platform $platform, string $requesterId, string $query, int $limit, ?string $cursor): array
    {
        return $this->request($platform, $requesterId, $query, $limit, $cursor, null);
    }

    public function assertTargetEligible(Platform $platform, string $requesterId, string $targetId): void
    {
        $result = $this->request($platform, $requesterId, $targetId, 1, null, $targetId);
        if (count($result['results']) !== 1 || $result['results'][0]['external_user_id'] !== $targetId) {
            throw new ApiException('CHAT_NOT_ALLOWED', 'This user is no longer available for chat.', 403);
        }
    }

    /** @return array{results: list<array<string, mixed>>, next_cursor: ?string, has_more: bool} */
    private function request(Platform $platform, string $requesterId, string $query, int $limit, ?string $cursor, ?string $targetId): array
    {
        $integration = $platform->integration;
        if ($integration === null || ! is_string($integration->user_search_endpoint) || $integration->user_search_endpoint === '') {
            throw new ApiException('SEARCH_NOT_CONFIGURED', 'User search is not configured for this platform.', 503);
        }

        $rateKey = 'widget-search:'.$platform->id.':'.hash('sha256', $requesterId);
        if (RateLimiter::tooManyAttempts($rateKey, 30)) {
            throw new ApiException('RATE_LIMITED', 'Please wait before searching again.', 429);
        }
        RateLimiter::hit($rateKey, 60);

        [$endpoint, $curlResolve] = $this->validatedEndpoint($integration->user_search_endpoint, $integration->user_search_allowed_hosts ?? []);
        $requestId = (string) Str::uuid();
        $headers = ['Accept' => 'application/json', 'X-MyVivahAI-Request-ID' => $requestId, 'X-MyVivahAI-Platform-ID' => $platform->public_id, 'X-MyVivahAI-Requester-ID' => $requesterId];
        $secret = $integration->user_search_auth_secret;
        if (is_string($secret) && $secret !== '') {
            try {
                $secret = Crypt::decryptString($secret);
            } catch (\Throwable) {
                Log::warning('External user search credential could not be decrypted.', ['platform_id' => $platform->public_id]);
                throw new ApiException('SEARCH_UNAVAILABLE', 'User search is temporarily unavailable.', 503);
            }
            $header = $integration->user_search_auth_type === 'header' ? $integration->user_search_auth_header : 'Authorization';
            $value = $integration->user_search_auth_type === 'header' ? $secret : 'Bearer '.$secret;
            $headers[$header] = $value;
        } else {
            throw new ApiException('SEARCH_NOT_CONFIGURED', 'Configure an authentication credential for user search.', 503);
        }

        $started = microtime(true);
        try {
            $parameters = array_filter([
                'q' => $targetId === null ? $query : null,
                'limit' => min(20, max(1, $limit)),
                'cursor' => $cursor,
                'target_external_user_id' => $targetId,
            ], static fn ($value): bool => $value !== null);
            $options = ['stream' => true];
            if ($curlResolve !== null) {
                $options['curl'] = [CURLOPT_RESOLVE => [$curlResolve]];
            }
            $response = Http::withHeaders($headers)->connectTimeout(2)->timeout(5)->withoutRedirecting()->withOptions($options)->get($endpoint, $parameters);
        } catch (ConnectionException $exception) {
            Log::notice('External user search request failed.', ['platform_id' => $platform->public_id, 'request_id' => $requestId, 'duration_ms' => (int) ((microtime(true) - $started) * 1000), 'exception' => class_basename($exception)]);
            throw new ApiException('SEARCH_UNAVAILABLE', 'User search is temporarily unavailable. Your conversations are still available.', 503);
        }

        $duration = (int) ((microtime(true) - $started) * 1000);
        $stream = $response->toPsrResponse()->getBody();
        $contentLength = $response->header('Content-Length');
        $oversized = is_string($contentLength) && ctype_digit($contentLength) && (int) $contentLength > 262144;
        $body = '';
        if (! $oversized) {
            while (! $stream->eof() && strlen($body) <= 262144) {
                $body .= $stream->read(min(8192, 262145 - strlen($body)));
            }
            $oversized = strlen($body) > 262144;
        }
        if (! $response->successful() || $oversized) {
            Log::notice('External user search returned an unusable response.', ['platform_id' => $platform->public_id, 'request_id' => $requestId, 'status' => $response->status(), 'duration_ms' => $duration]);
            throw new ApiException('SEARCH_UNAVAILABLE', 'User search is temporarily unavailable. Your conversations are still available.', 503);
        }

        $payload = json_decode($body, true);
        if (! is_array($payload) || ($payload['success'] ?? false) !== true || ! is_array($payload['data'] ?? null) || ! array_is_list($payload['data']) || count($payload['data']) > 20 || ! is_array($payload['pagination'] ?? null)) {
            Log::notice('External user search response failed schema validation.', ['platform_id' => $platform->public_id, 'request_id' => $requestId, 'duration_ms' => $duration]);
            throw new ApiException('SEARCH_UNAVAILABLE', 'User search is temporarily unavailable. Your conversations are still available.', 503);
        }

        $results = [];
        foreach ($payload['data'] as $item) {
            if (! is_array($item) || ! is_string($item['external_user_id'] ?? null) || ! preg_match('/^[^\/]{1,255}$/u', $item['external_user_id']) || ! is_string($item['display_name'] ?? null) || trim($item['display_name']) === '' || mb_strlen($item['display_name']) > 120) {
                throw new ApiException('SEARCH_UNAVAILABLE', 'User search is temporarily unavailable. Your conversations are still available.', 503);
            }
            if ($item['external_user_id'] === $requesterId) {
                continue;
            }
            $photo = $this->optionalHttpsUrl($item['profile_photo_url'] ?? null);
            $profile = $this->optionalHttpsUrl($item['profile_url'] ?? null);
            $candidate = Crypt::encryptString(json_encode(['platform_id' => $platform->id, 'requester_id' => $requesterId, 'target_id' => $item['external_user_id'], 'expires' => now()->addMinutes(5)->timestamp], JSON_THROW_ON_ERROR));
            $results[] = ['external_user_id' => $item['external_user_id'], 'display_name' => trim($item['display_name']), 'profile_photo_url' => $photo, 'profile_url' => $profile, 'candidate_token' => $candidate];
        }

        $nextCursor = $payload['pagination']['next_cursor'] ?? null;
        $hasMore = ($payload['pagination']['has_more'] ?? false) === true;
        if (($nextCursor !== null && (! is_string($nextCursor) || strlen($nextCursor) > 512)) || ! is_bool($payload['pagination']['has_more'] ?? false) || $hasMore !== ($nextCursor !== null)) {
            throw new ApiException('SEARCH_UNAVAILABLE', 'User search is temporarily unavailable. Your conversations are still available.', 503);
        }
        Log::info('External user search completed.', ['platform_id' => $platform->public_id, 'request_id' => $requestId, 'duration_ms' => $duration, 'result_count' => count($results)]);

        return ['results' => $results, 'next_cursor' => $nextCursor, 'has_more' => $hasMore];
    }

    /** @return array{target_id: string} */
    public function validateCandidate(string $token, Platform $platform, string $requesterId): array
    {
        try {
            $payload = json_decode(Crypt::decryptString($token), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            throw new ApiException('INVALID_SEARCH_RESULT', 'Choose a user from the latest search results.', 422);
        }
        if (! is_array($payload) || ($payload['platform_id'] ?? null) !== $platform->id || ($payload['requester_id'] ?? null) !== $requesterId || ! is_string($payload['target_id'] ?? null) || ($payload['expires'] ?? 0) < now()->timestamp || $payload['target_id'] === $requesterId) {
            throw new ApiException('INVALID_SEARCH_RESULT', 'Choose a user from the latest search results.', 422);
        }

        return ['target_id' => $payload['target_id']];
    }

    /** @return array{string, ?string} URL and a cURL DNS pin when production requires it. */
    private function validatedEndpoint(string $url, array $allowedHosts): array
    {
        $parts = parse_url($url);
        $host = strtolower((string) ($parts['host'] ?? ''));
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $port = is_array($parts) ? ($parts['port'] ?? null) : null;
        if ($parts === false || $host === '' || isset($parts['user']) || isset($parts['pass']) || isset($parts['fragment']) || isset($parts['query']) || (! app()->environment('local', 'testing') && ($scheme !== 'https' || ($port !== null && $port !== 443))) || ! in_array($scheme, ['http', 'https'], true) || filter_var($host, FILTER_VALIDATE_IP)) {
            throw new ApiException('SEARCH_NOT_CONFIGURED', 'The configured user-search endpoint is not allowed.', 503);
        }
        if (in_array($host, ['localhost', 'localhost.localdomain'], true) || str_ends_with($host, '.localhost') || str_ends_with($host, '.local')) {
            throw new ApiException('SEARCH_NOT_CONFIGURED', 'The configured user-search endpoint is not allowed.', 503);
        }
        $allowedHosts = array_map(static fn ($allowed): string => strtolower((string) $allowed), $allowedHosts);
        if (! in_array($host, $allowedHosts, true)) {
            throw new ApiException('SEARCH_NOT_CONFIGURED', 'Add the search API host to this platform’s allowed origins.', 503);
        }
        $curlResolve = null;
        if (! app()->environment('local', 'testing')) {
            if (! extension_loaded('curl')) {
                throw new ApiException('SEARCH_UNAVAILABLE', 'User search requires the cURL PHP extension.', 503);
            }
            $records = dns_get_record($host, DNS_A | DNS_AAAA) ?: [];
            $addresses = [];
            foreach ($records as $record) {
                if (isset($record['ip'])) {
                    $addresses[] = $record['ip'];
                }
                if (isset($record['ipv6'])) {
                    $addresses[] = $record['ipv6'];
                }
            }
            if ($addresses === []) {
                throw new ApiException('SEARCH_NOT_CONFIGURED', 'The configured user-search host could not be resolved.', 503);
            }
            foreach ($addresses as $address) {
                if (! filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    throw new ApiException('SEARCH_NOT_CONFIGURED', 'The configured user-search host must resolve to a public address.', 503);
                }
            }
            $address = $addresses[0];
            if (str_contains($address, ':')) {
                $address = '['.$address.']';
            }
            $curlResolve = $host.':'.($port ?? 443).':'.$address;
        }

        return [$url, $curlResolve];
    }

    private function optionalHttpsUrl(mixed $value): ?string
    {
        return is_string($value) && strlen($value) <= 2048 && str_starts_with(strtolower($value), 'https://') && filter_var($value, FILTER_VALIDATE_URL) ? $value : null;
    }
}
