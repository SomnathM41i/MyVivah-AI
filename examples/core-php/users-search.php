<?php

declare(strict_types=1);

// Core PHP/PDO reference endpoint implementing External User Search API v1.
// Web server configuration must load the environment values shown in .env.example.
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function respond(int $status, array $body): never
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    header('Allow: GET');
    respond(405, ['success' => false, 'error' => ['code' => 'METHOD_NOT_ALLOWED', 'message' => 'GET required']]);
}
$provided = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$expected = (string) getenv('MYVIVAH_SEARCH_TOKEN');
$expectedPlatformId = (string) getenv('MYVIVAH_CLIENT_ID');
$platformId = $_SERVER['HTTP_X_MYVIVAHAI_PLATFORM_ID'] ?? '';
$requesterId = $_SERVER['HTTP_X_MYVIVAHAI_REQUESTER_ID'] ?? '';
if ($expected === '' || ! hash_equals('Bearer '.$expected, $provided) || $expectedPlatformId === '' || ! hash_equals($expectedPlatformId, $platformId) || $requesterId === '') {
    respond(401, ['success' => false, 'error' => ['code' => 'UNAUTHORIZED', 'message' => 'Invalid integration credentials']]);
}

$q = trim((string) ($_GET['q'] ?? ''));
$targetId = $_GET['target_external_user_id'] ?? null;
$isTargetCheck = is_string($targetId) && $targetId !== '';
$limit = filter_var($_GET['limit'] ?? 20, FILTER_VALIDATE_INT);
$cursor = $_GET['cursor'] ?? null;
if ((! $isTargetCheck && (mb_strlen($q) < 2 || mb_strlen($q) > 100)) || ($isTargetCheck && (strlen($targetId) > 255 || str_contains($targetId, '/'))) || $limit === false || $limit < 1 || $limit > 20 || ($cursor !== null && ! is_string($cursor)) || ($isTargetCheck && $cursor !== null)) {
    respond(422, ['success' => false, 'error' => ['code' => 'VALIDATION_ERROR', 'message' => 'Invalid query or pagination']]);
}

try {
    $pdo = new PDO((string) getenv('DB_DSN'), (string) getenv('DB_USER'), (string) getenv('DB_PASSWORD'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Example assumes users(id, display_name, profile_photo_url, profile_visible,
    // chat_enabled) and user_blocks(blocker_id, blocked_id). Adapt every predicate
    // to the client's real privacy and eligibility policy. Requester identity
    // comes from the authenticated MyVivahAI-to-platform server request, never JS.
    $afterId = 0;
    if ($cursor !== null && $cursor !== '') {
        $rawCursor = base64_decode($cursor, true);
        $parts = is_string($rawCursor) ? explode('.', $rawCursor, 2) : [];
        if (count($parts) !== 2 || ! ctype_digit($parts[0])) {
            respond(422, ['success' => false, 'error' => ['code' => 'VALIDATION_ERROR', 'message' => 'Invalid cursor']]);
        }
        $expectedMac = hash_hmac('sha256', $parts[0], (string) getenv('MYVIVAH_SEARCH_TOKEN'));
        if (! hash_equals($expectedMac, $parts[1])) {
            respond(422, ['success' => false, 'error' => ['code' => 'VALIDATION_ERROR', 'message' => 'Invalid cursor']]);
        }
        $afterId = (int) $parts[0];
    }

    $requesterStmt = $pdo->prepare('SELECT id FROM users WHERE external_user_id = :external_user_id AND chat_enabled = 1 LIMIT 1');
    $requesterStmt->execute(['external_user_id' => $requesterId]);
    $requesterDbId = $requesterStmt->fetchColumn();
    if ($requesterDbId === false) {
        respond(403, ['success' => false, 'error' => ['code' => 'FORBIDDEN', 'message' => 'Requester is not eligible']]);
    }

    $sql = <<<'SQL'
SELECT u.id, u.external_user_id, u.display_name, u.profile_photo_url
FROM users u
WHERE u.id > :cursor
  AND u.profile_visible = 1
  AND u.chat_enabled = 1
  AND u.id <> :requester_id_self
  AND __MATCH_CONDITION__
  AND NOT EXISTS (
      SELECT 1 FROM user_blocks b
      WHERE (b.blocker_id = :requester_id_blocker AND b.blocked_id = u.id)
         OR (b.blocker_id = u.id AND b.blocked_id = :requester_id_blocked)
  )
ORDER BY u.id ASC
LIMIT :limit
SQL;
    $sql = str_replace('__MATCH_CONDITION__', $isTargetCheck ? 'u.external_user_id = :target_external_id' : "u.display_name LIKE :query ESCAPE '!'", $sql);
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':cursor', $afterId, PDO::PARAM_INT);
    $stmt->bindValue(':requester_id_self', (int) $requesterDbId, PDO::PARAM_INT);
    $stmt->bindValue(':requester_id_blocker', (int) $requesterDbId, PDO::PARAM_INT);
    $stmt->bindValue(':requester_id_blocked', (int) $requesterDbId, PDO::PARAM_INT);
    if ($isTargetCheck) {
        $stmt->bindValue(':target_external_id', $targetId);
    } else {
        $escapedQuery = strtr($q, ['!' => '!!', '%' => '!%', '_' => '!_']);
        $stmt->bindValue(':query', '%'.$escapedQuery.'%');
    }
    $stmt->bindValue(':limit', $limit + 1, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    $hasMore = count($rows) > $limit;
    $rows = array_slice($rows, 0, $limit);
    $nextCursor = null;
    if ($hasMore && $rows !== []) {
        $lastId = (string) end($rows)['id'];
        $mac = hash_hmac('sha256', $lastId, (string) getenv('MYVIVAH_SEARCH_TOKEN'));
        $nextCursor = base64_encode($lastId.'.'.$mac);
    }
    $data = array_map(static fn (array $row): array => [
        'external_user_id' => (string) $row['external_user_id'],
        'display_name' => (string) $row['display_name'],
        'profile_photo_url' => is_string($row['profile_photo_url'] ?? null) ? $row['profile_photo_url'] : null,
    ], $rows);
    respond(200, ['success' => true, 'data' => $data, 'pagination' => ['next_cursor' => $nextCursor, 'has_more' => $hasMore]]);
} catch (Throwable $exception) {
    error_log('MyVivahAI search endpoint error: '.get_class($exception));
    respond(500, ['success' => false, 'error' => ['code' => 'SERVER_ERROR', 'message' => 'Search unavailable']]);
}
