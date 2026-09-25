<?php

declare(strict_types=1);

// Include from a page served after the platform has authenticated its user.
// The MyVivahAI client secret is server-only. Never print it or put it in JS.
session_start();
if (empty($_SESSION['external_user_id'])) {
    http_response_code(401);
    exit('Login required');
}
$apiBase = rtrim((string) getenv('MYVIVAH_API_BASE'), '/');
$clientId = (string) getenv('MYVIVAH_CLIENT_ID');
$clientSecret = (string) getenv('MYVIVAH_CLIENT_SECRET');
$request = static function (string $url, string $method, ?array $body, ?string $bearer = null): array {
    $headers = ['Accept: application/json', 'Content-Type: application/json'];
    if ($bearer !== null) {
        $headers[] = 'Authorization: Bearer '.$bearer;
    }
    $handle = curl_init($url);
    curl_setopt_array($handle, [CURLOPT_RETURNTRANSFER => true, CURLOPT_CUSTOMREQUEST => $method, CURLOPT_HTTPHEADER => $headers, CURLOPT_POSTFIELDS => $body === null ? null : json_encode($body), CURLOPT_CONNECTTIMEOUT => 3, CURLOPT_TIMEOUT => 8, CURLOPT_FOLLOWLOCATION => false]);
    $raw = curl_exec($handle);
    $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
    curl_close($handle);
    $decoded = is_string($raw) ? json_decode($raw, true) : null;
    if ($status < 200 || $status >= 300 || ! is_array($decoded) || ($decoded['success'] ?? false) !== true) {
        throw new RuntimeException('MyVivahAI request failed');
    }

    return $decoded['data'];
};

try {
    $token = $request($apiBase.'/auth/token', 'POST', ['client_id' => $clientId, 'client_secret' => $clientSecret]);
    $session = $request($apiBase.'/widget/session', 'POST', ['external_user_id' => (string) $_SESSION['external_user_id']], $token['access_token']);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode(['success' => true, 'data' => $session], JSON_UNESCAPED_SLASHES);
} catch (Throwable $exception) {
    error_log('Widget session bootstrap failed: '.get_class($exception));
    http_response_code(503);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Chat is temporarily unavailable']);
}
