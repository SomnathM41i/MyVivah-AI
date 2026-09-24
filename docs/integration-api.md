# MyVivahAI — Integration API Reference (server-side v1)

> This document describes MyVivahAI's **server-to-server** REST API — the endpoints external platforms call once they hold credentials. It is implemented and covered by tests (`tests/Feature/IntegrationApiEndToEndTest.php`, `tests/Feature/ChatApiEndToEndTest.php`, `IntegrationOnboardingTest.php`, `tests/Feature/RealtimeChatTest.php`) as **Phase 3C** (integration surface), **Phase 3D** (chat foundation) and **Phase 3E** (presence + realtime channel authorization).

## Overview

- Base path: `/api/v1`
- Content type: `application/json` (request and response)
- Versioning: path-versioned (`v1`); breaking changes require a new version.
- All endpoints below (except `auth/token`) require `Authorization: Bearer <access_token>`.
- Every response uses the stable envelope (see [Error Shape](#error-shape)).

## Authentication

### `POST /api/v1/auth/token` — exchange credentials for a PASETO access token

Verifies the platform's API key (fingerprint-pinned, `hash_equals`) and issues a PASETO v4.local token bound to the platform + granted scopes.

| Field | Type | Notes |
|---|---|---|
| `client_id` | string | Platform `public_id` (ULID) |
| `client_secret` | string | API key secret (base64url; shown exactly once at issuance) |

Success → `200`:

```json
{
  "success": true,
  "data": {
    "token_type": "bearer",
    "access_token": "v4.local....",
    "expires_in": 3600,
    "scope": ["authentication", "realtime_chat:read", "realtime_chat:write"],
    "jti": "..."
  },
  "meta": { "request_id": "..." }
}
```

`scope` is always derived from the platform's service entitlements; `authentication` is always present.

- Throttled via the `paseto.issue` limiter (**5/min**, keyed by `client_id` when supplied, else by IP) → `429 RATE_LIMITED`.
- Errors: `INVALID_CLIENT` 401 (unknown/revoked/inactive), `SERVICE_NO_ACCESS` 403 (requested scope not granted), `PLATFORM_SUSPENDED` 403 (`platform`/`integration` not `active`).

### `POST /api/v1/auth/revoke` — revoke the current token

Revokes the presented token's `jti` (blacklist); subsequent use → `401 TOKEN_REVOKED`.

| Field | Type | Notes |
|---|---|---|
| `token` | string | The access token to revoke (optional; defaults to the presented token) |

---

## Platform self-description

### `GET /api/v1/platform/me` — integration + entitlements for the calling platform

Scope: `authentication`. Returns the platform identity, live service entitlements (with per-scope access), and the integration runtime settings:

- `integration.base_domain` — configured CORS/allowed domain
- `integration.rate_limit_per_minute` — per-platform request ceiling
- `integration.token_ttl_seconds` — issued token lifetime (capped by `config('paseto.max_ttl_seconds')`)

---

## External user identity

The external platform is the source of truth for user profiles; MyVivahAI stores only the identity reference map (`platform_external_user_map`).

### `POST /api/v1/users/verify` — resolve-or-create an external user reference

Scope: `realtime_chat:write`. Idempotent upsert on `(platform_id, external_user_id)`.

| Field | Type | Notes |
|---|---|---|
| `external_user_id` | string | The client platform's own user id (URL-safe, no `/`) |
| `local_public_id` | string | Optional MyVivahAI user `public_id` to attach |

Success `200` → `{ public_id, external_user_id, local_public_id, synced_at, last_seen_at }`. No profile data is mirrored.

### `GET /api/v1/users/{external_user_id}` — lookup an external user reference

Scope: `realtime_chat:read`. Returns the same reference shape; bumps `last_seen_at`. `404 NOT_FOUND` if the reference does not exist **for the calling platform** (cross-platform lookups never succeed).

### `GET /api/v1/users` — paginated list of the platform's external user references

Scope: `realtime_chat:read`.

| Query | Default | Notes |
|---|---|---|
| `page` | 1 | Page index |
| `per_page` | 20 | Capped at 100 |

Response `data` is a list of references; `meta.pagination` carries `{current_page, per_page, total, last_page}`.

---

## Integration configuration & key management

Scope: `authentication` for all of the following.

### `GET /api/v1/integration/config` — read integration runtime configuration

Returns `base_domain` (and any future capabilities). Never returns secrets.

### `PATCH /api/v1/integration/config` — update integration configuration

| Field | Type | Notes |
|---|---|---|
| `base_domain` | string, nullable | Valid absolute URL, ≤ 255 chars |
| `allowed_origins` | array, nullable | ≤ 20 origin URLs, each ≤ 255 chars |

Invalid values → `422 VALIDATION_FAILED` with per-field `errors`.

### `GET /api/v1/integration/keys` — list the platform's API keys

Returns key metadata only — `key_fingerprint` (SHA-256 prefix), `status` (`active` | `rotated` | `revoked`), `created_at`, `rotated_at`, `revoked_at`. **Raw secrets are never returned.**

### `POST /api/v1/integration/keys/rotate` — rotate the active key

Demotes the current primary to `rotated`, promotes a new `active` key. The raw secret is returned **exactly once** in the response:

```json
{
  "success": true,
  "data": {
    "key_fingerprint": "<sha256 prefix>",
    "client_id": "<platform public_id>",
    "client_secret": "<NEW base64url secret — store now, cannot be retrieved again>"
  },
  "meta": { "request_id": "..." }
}
```

A previously-rotated key still validates during the grace window (`config('paseto.rotation_grace_seconds')`, default 24 h) and is **hard-revoked on sight** after expiry.

### `POST /api/v1/integration/keys/revoke` — revoke a specific key

| Field | Type | Notes |
|---|---|---|
| `key_fingerprint` | string (40 chars) | Prefix of the SHA-256 fingerprint to revoke |

Revoked keys are rejected immediately (`401 TOKEN_REVOKED`). Unknown fingerprint → `404 NOT_FOUND`. Key lookups are scoped to the calling platform — one platform cannot revoke another's keys.

---

## Chat API (Phase 3D REST + Phase 3E realtime)

Base `/api/v1/chat`. Authenticated with `Authorization: Bearer <platform PASETO>` and scoped: **`realtime_chat:write`** for conversation create, message send and mark-read; **`realtime_chat:read`** for list, detail and history. All chat requests except conversation creation require the **`X-External-User-Id`** header naming which of the platform's users is acting (server-to-server; the header value only ever resolves inside the calling platform's own identity map).

> Integrity note: the acting-user assertion is done by the PLATFORM (it owns its identities); MyVivahAI enforces that the value exists in the platform-scoped map and that the caller is a participant — it does not accept an arbitrary browser-supplied user id (AGENTS.md §10).

### `POST /api/v1/chat/conversations` — resolve-or-create a two-party thread

| Field | Type | Notes |
|---|---|---|
| `participant_external_ids` | array[2] | Exactly two distinct external user ids (order-insensitive) |

Deterministic: the same pair always returns the same conversation (`meta.created` = whether it was newly created). No `X-External-User-Id` required. Unmapped ids are resolved-or-created inside the platform's map.

### `GET /api/v1/chat/conversations` — the acting user's conversations

Query: `page` (default 1), `per_page` (default 20, max 100). Ordered by last message time, empty threads last. Returns the standard paginated `data` plus `meta.pagination`.

### `GET /api/v1/chat/conversations/{id}` — one thread (acting user's POV)

Includes `unread_count` and `last_read_at` (the acting user's read state) and the denormalized `last_message` (`excerpt`, `sent_at`, `sender_id`).

### `POST /api/v1/chat/conversations/{id}/read` — advance-only mark-read

| Field | Type | Notes |
|---|---|---|
| `last_read_message_id` | int (optional) | Opaque read cursor from a prior response; omit body → mark everything read |

Never regresses the cursor. Response: `{ conversation_id, last_read_message_id, last_read_at, unread_count }`.

### `GET /api/v1/chat/conversations/{id}/messages` — cursor-paginated history

Query: `limit` (1–100, default 50), `before` (opaque cursor returned as `next_cursor`). Newest-first pages presented ASC. Response rows are `MessageResource` (`id` = ULID, `type`, `status`, `content`, `client_message_id`, `sender`, `sent_at`). `meta.pagination`: `{ before, limit, has_more, next_cursor }`.

### `POST /api/v1/chat/conversations/{id}/messages` — idempotent send

| Field | Type | Notes |
|---|---|---|
| `client_message_id` | string, required | 8–64 chars, `[A-Za-z0-9_-]+`; the idempotency key |
| `content` | string, required | ≤ 4000 chars |
| `type` | string, optional | `text` (only value at MVP) |

Retries converge on the ORIGINAL message (same `data.id`) with `meta.duplicate: true` — no duplicates, even under concurrency. `201` on success.

### Presence (Phase 3E — DB-backed, works with or without realtime)

Presence state lives in MyVivahAI's DB (`platform_external_user_map`); NONE of
these calls require a realtime server, Redis, or a WebSocket. When realtime is
enabled (`CHAT_REALTIME_ENABLED=true` + a configured broadcast connection),
presence **transitions** additionally broadcast `user.online` / `user.offline` on
`presence-chat.{platform public_id}`.

#### `POST /api/v1/chat/presence` — heartbeat the acting user

Scope: `realtime_chat:write`. Requires `X-External-User-Id`.

| Field | Type | Notes |
|---|---|---|
| `status` | string (optional) | `online` (default) or `offline` |

Writes the heartbeat and returns the new state. `changed` is `false` for
repeated same-status heartbeats (nothing is re-broadcast):

```json
{ "success": true,
  "data": { "external_user_id": "u1", "presence_status": "online",
            "presence_seen_at": "…", "changed": true },
  "meta": { "request_id": "…", "offline_after_seconds": 90,
            "realtime_enabled": true } }
```

#### `GET /api/v1/chat/presence/{external_user_id}` — read a user's presence

Scope: `realtime_chat:read`. Returns the effective state; a user whose last
heartbeat is older than `offline_after_seconds` reads as `offline`. This is the
**polling fallback** for widgets when realtime is off. Unknown or
other-platform ids → `404 NOT_FOUND` (isolation-preserving).

### Realtime channel authorization (Phase 3E)

#### `POST /api/v1/chat/socket/auth` — authorize a realtime channel subscription

Scope: `realtime_chat:read`. Requires `X-External-User-Id`. The **platform
backend** calls this on behalf of its logged-in user and hands the response to
its widget; the PASETO credential never reaches the browser.

| Field | Type | Notes |
|---|---|---|
| `socket_id` | string | Pusher-form socket id, `[0-9]+\.[0-9]+` |
| `channel_name` | string | `private-chat.{conversation public_id}` or `presence-chat.{platform public_id}` |

Server-side checks (always enforced): conversation channels require the actor to
be a **participant** of that thread; presence channels require the actor to be a
**mapped user of the same platform** (the platform public_id must match the
token's own platform). Success:

```json
{ "success": true,
  "data": { "authorized": true,
            "auth": "<app_key>:<hmac-sha256(socket_id:channel[:channel_data], app_secret)>",
            "channel_data": "<JSON or null — presence only>" },
  "meta": { "request_id": "…" } }
```

`auth` is a Pusher-protocol subscription signature the realtime server
(Reverb/Soketi/Pusher) re-verifies with the same server-only `app_secret`; it is
derived from config and never exposed inside event payloads. Presence
subscriptions also return `channel_data`
`{"user_id":"<internal id>","user_info":{"external_user_id":"…","presence_status":"…"}}`.
Denials (non-participant, foreign platform, unknown channel) → `403 CHANNEL_DENIED`
— never a 404, so channel existence is not disclosed.

### Chat-scoped validation/errors

- Missing/oversized `X-External-User-Id` → `422 VALIDATION_FAILED`; unmapped → `404 NOT_FOUND` (isolation-preserving).
- Non-member acting user, or a thread belonging to another platform → `404 NOT_FOUND`.
- Missing scope → `403 SERVICE_NO_ACCESS` (audited as `insufficient_scope`).
- Realtime channel denial → `403 CHANNEL_DENIED`; malformed `socket_id` → `422 VALIDATION_FAILED`.
- All chat requests are audited under the shared stack and rate-limited per platform (below).

---

## Widget API (Phase 4)

Widget routes are **browser-facing** (`api/v1/widget/**`) and authenticate with a
**widget session token**, not a platform token. A widget session is a short-lived
PASETO v4.local minted server-to-server by the platform backend:

- `aud = widget:{slug}`, `sub = external_user_id`, claims `platform_id` /
  `external_user_id` / `scope = [realtime_chat:read, realtime_chat:write]`, jti.
- Lifetime `config('widget.session.ttl_seconds')` (default 900 s), capped at
  `max_ttl_seconds` (1800). Revocable (`session/revoke`), expiry enforced per request.
- Widget tokens are rejected on platform routes and platform tokens on widget
  routes (`403 PLATFORM_MISMATCH`) — the audiences are isolated by design.

### `POST /api/v1/widget/session` — server-to-server widget bootstrap

Platform token (must hold `realtime_chat:write`) + body `external_user_id`.
The response is the **only** thing a platform backend sends to its browser.

```json
{
  "success": true,
  "data": {
    "token_type": "Widget",
    "access_token": "v4.local....",
    "expires_in": 900,
    "expires_at": "2026-09-23T12:00:00+00:00",
    "self": { "external_user_id": "4582", "local_public_id": "01H...", "presence_status": "offline", "presence_seen_at": null },
    "channels": { "private": "private-chat", "presence": "presence-chat" },
    "realtime": { "enabled": false, "connection": "none", "app_key": "", "scheme": "wss", "host": "", "port": 0, "path": "/app/" }
  },
  "meta": { "request_id": "<uuid>" }
}
```

### `POST /api/v1/widget/session/revoke` — revoke the current widget session

Authenticated with the **widget** token itself (not the platform one). Blacklists
its `jti`; subsequent requests → `401 TOKEN_REVOKED`.

### Widget routes (wire-identical to their `api/v1/chat/**` counterparts)

`conversations` (list/resolve-open/show), `conversations/{id}/read` (markRead),
`conversations/{id}/messages` (cursor history + idempotent send),
`presence` (heartbeat), `presence/me`, `presence/{external_user_id}`,
`socket/auth`, `integration/users?q=`, `users/{external_user_id}`.

Differences from the platform routes:

- **No `X-External-User-Id` header** — the acting user is the token-bound user.
  A spoofed header is ignored (`422`/identity confusion is impossible).
- Opening a thread this user is not a participant of → `422 VALIDATION_FAILED`.
- CORS preflights are served for `api/v1/widget/*` only; if the platform sets
  `integration.allowed_origins`, non-matching browser origins have the
  `Access-Control-Allow-Origin` header stripped (exact or `https://*.sub.example`).

---

## Rate limiting

| Policy | Key | Ceiling |
|---|---|---|
| Token issuance (`auth/token`) | `client:<client_id>` or `ip:<ip>` | `config('paseto.issue_throttle')` default **5/min** |
| Authenticated v1 routes | verified platform id (`integration:<platform_id>`) | `platform_integrations.rate_limit_per_minute`, fallback `config('api.rate_limit_per_minute')` default **60/min** |
| Global api group | IP | framework `throttle:api` (see `bootstrap/app.php`) |

- The per-platform ceiling is **isolation-safe**: it is keyed by the *verified* platform id from the token, so one platform can never consume another platform's quota, and a platform cannot lower its own limit by sending different credentials (token fixed to one platform).
- Success responses include `X-RateLimit-Limit` / `X-RateLimit-Remaining`.
- Exceeding a limit → `429` with `Retry-After` header and `retry_after_seconds` in the envelope; the request is still written to the audit log.

## Error Shape

All failures use the stable envelope:

```json
{
  "success": false,
  "error": {
    "code": "MACHINE_READABLE_CODE",
    "status": 401,
    "message": "Human-readable summary",
    "request_id": "<uuid>",
    "errors": {}
  },
  "meta": { "request_id": "<uuid>" }
}
```

| Code | HTTP | Meaning |
|---|---|---|
| `INVALID_CLIENT` | 401 | Unknown/revoked/inactive client credentials |
| `INVALID_TOKEN` | 401 | Missing, malformed, or otherwise invalid token |
| `TOKEN_EXPIRED` | 401 | Token past `exp` |
| `TOKEN_REVOKED` | 401 | Token or key/jti revoked |
| `PLATFORM_MISMATCH` | 403 | Token audience/subject do not match the calling platform |
| `PLATFORM_SUSPENDED` | 403 | Platform/integration not `active` |
| `SERVICE_NO_ACCESS` | 403 | Scope not granted by live entitlement |
| `INSUFFICIENT_SCOPE` | 403 | Valid token but missing the required scope |
| `CHANNEL_DENIED` | 403 | Realtime subscription not authorized (non-participant / foreign platform / unknown channel) — existence never disclosed |
| `NOT_FOUND` | 404 | Resource does not exist for this platform (isolation: never another's) |
| `VALIDATION_FAILED` | 422 | Request validation failed (`errors` carries field messages) |
| `METHOD_NOT_ALLOWED` | 405 | Route exists but HTTP method not allowed |
| `RATE_LIMITED` | 429 | Rate ceiling reached (`Retry-After` set) |
| `INTERNAL_ERROR` | 500 | Unexpected server error (details never leak) |

## Audit & tracing

- Every authenticated request is recorded in the append-only `api_audit_logs` (event `request`, IP hash, SHA-256 payload checksum) — including rejected `429`/`4xx` responses.
- Audit events also cover `token_issued`, `token_rejected`, `token_expired`, `invalid_token`, `wrong_platform`, `insufficient_scope`, and `key_rotation`.
- `meta.request_id` (and the audit `request_id`) correlate a client-visible failure with the server-side audit row.

## Onboarding (operator tooling)

`php artisan integration:onboard {slug} {--name=} {--service-key=realtime_chat}` provisions an idempotent platform + integration + active API key + service entitlement in one shot and prints the one-time client secret (table: slug, public_id, client_secret, scope). Safe to re-run — re-provisioning returns the same platform and key metadata without rotating keys.