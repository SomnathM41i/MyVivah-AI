# MyVivahAI — Real-Time Chat Service

## Overview

The Real-Time Chat service is the first service offered by MyVivahAI. Client platforms integrate the chat widget into their websites. End users (the client platform's users) can search for other users, open conversations, and exchange real-time messages.

This document describes the chat domain: conversations, messages, participants, WebSockets, presence, delivery, authorization, and data model considerations.

---

## Scope

**In scope (MVP):**

- Conversations between two users of the same platform
- Real-time message send/receive — **implemented** (Phase 3D REST + 3E broadcast)
- Conversation history
- Unread counts
- Timestamps
- Conversation list
- User search (driven by client's search API)
- Online/offline presence — **implemented** (Phase 3E, DB-backed / REST-first)

**Not in scope (MVP):**

- Group conversations
- Voice/video calls
- Attachments (future consideration)
- Typing indicators (future consideration)
- Explicit "delivered" receipts and typing indicators (future; read receipts ARE
  implemented: `message.read` broadcast on the REST mark-read in Phase 3D/3E)
- Cross-platform conversations (a user from Platform A chatting with a user from Platform B) — **explicitly out of scope and disallowed**

---

## Core Concepts

| Concept | Definition |
|---|---|
| Conversation | A private, two-party message thread |
| Participant | One of the users in a conversation |
| Message | A single chat entry in a conversation |
| Message Status | Delivery/sent status markers |
| External User | A user on the client platform referenced by ID |
| Presence | Online/offline state of a user |

---

## Conversation Model

- A conversation has exactly two participants.
- A conversation is scoped to a platform.
- Both participants must belong to the same platform.
- Conversation identity should be stable for a given pair of users (look up existing conversation before creating a new one).
- The deduplication key can be a normalized pair of external user IDs scoped by platform.

```
Platform = P
Users: U1 (id=113), U2 (id=841)
Conversation key: P / sorted pair → "113:841" (normalized)
```

---

## Message Model

A message includes:

- Message ID (public, unique)
- Conversation ID
- Sender (external user ID)
- Content
- Type (text by default; future: image, file, system)
- State (sent, delivered, read — where implemented)
- Created at / Pushed at timestamps
- Client-generated message ID for deduplication (idempotency)

---

## Real-Time Transport

### Implemented (Phase 3E) — optional, REST-first

Realtime messaging is an **optional layer on top of the REST API**: MySQL stays
the source of truth, every broadcast fires ONLY after a successful commit, and
with `CHAT_REALTIME_ENABLED=false` (the shared-hosting default) the whole REST
surface works unchanged — the widget simply polls history, mark-read and the
presence endpoints. Shared hosting ships today with **zero realtime infra**.

- **Broadcast driver** — Laravel broadcasting (`config/broadcasting.php`).
  Default connection is `null` (no-op); `log` for local debugging; the
  pusher-protocol lanes `reverb` / `soketi` / `pusher` (+ `ably`, `redis`) are
  pre-wired **config-only** — enabling one requires the vendor HTTP package at
  runtime (`composer require pusher/pusher-php-server`; `laravel/reverb` for the
  server binary). See `docs/deployment.md`.
- **Dispatch** — all events extend `App\Events\RealtimeEvent`
  (`ShouldBroadcastNow` → inline, **no queue worker**; `ShouldRescue` → a dead or
  unreachable transport is logged and swallowed, never a 500; `broadcastWhen()` →
  the global `chat.realtime.enabled` kill-switch). `App\Services\RealtimeBroadcaster`
  is the single dispatch point and every call site fires it **after its own
  `DB::transaction` closes** — the row is committed before the world hears about it.
- **Channels** (Pusher naming; `App\Chat\ChatChannels`):

  | Channel | Name (client visible) | Subscribes | Events |
  |---|---|---|---|
  | Private conversation | `private-chat.{conversation public_id}` | participants only | `message.created`, `message.read`, `conversation.updated` |
  | Platform presence | `presence-chat.{platform public_id}` | mapped users of that platform | `user.online`, `user.offline` |

  Channel names carry ONLY public ULIDs — never internal keys, secrets or profile
  data (AGENTS.md §15). Laravel's `PrivateChannel`/`PresenceChannel` prepend their
  own `private-`/`presence-`, so event channel objects use the bare transport name
  (`ChatChannels::conversationTransport()`/`presenceTransport()`) to avoid
  `private-private-chat.*`.
- **Events** (minimal display payload, never secrets):

  | Event | Payload |
  |---|---|
  | `message.created` | `conversation_id`, `message_id`, `sender_id` (platform's external id), `type`, `status`, `content`, `client_message_id`, `sent_at` |
  | `message.read` | `conversation_id`, `reader_id`, `last_read_message_id`, `unread_count`, `read_at` |
  | `conversation.updated` | `conversation_id`, `reason` (`created`/`updated`), `status`, `participants` (external ids), `last_message` (`excerpt`/`sent_at`/`sender_id`) or `null`, `updated_at` |
  | `user.online` / `user.offline` | `external_user_id`, `status`, `seen_at` |

  Semantics: a new message emits `message.created` **plus** `conversation.updated`
  (`reason=updated`); resolving a brand-new conversation emits
  `conversation.updated` (`reason=created`); duplicate sends **never** re-broadcast;
  repeated same-status presence heartbeats **never** re-fire.
- **Subscription authorization** — `POST /api/v1/chat/socket/auth` (scope
  `realtime_chat:read`), enforced by `App\Chat\ChatChannelAuthorizer`: verifies the
  platform PASETO + `X-External-User-Id`, asserts **participant** (private) or
  **platform member** (presence, and only the token's own platform), and returns
  the Pusher-protocol signature
  `auth = app_key:hmac-sha256(socket_id:channel[:channel_data], app_secret)` that
  the realtime server re-verifies with the same server-only secret. Denials are
  `403 CHANNEL_DENIED` (never a 404 — channel existence is not disclosed); presence
  subscriptions also return `channel_data`
  `{user_id:"<internal id>", user_info:{external_user_id, presence_status}}`.
- **Presence** — DB-backed, no Redis (see §Presence below).

### Channel Naming (implemented)

```
private-chat.{conversation public_id}   — thread events
presence-chat.{platform public_id}      — presence events
```

> The pre-Phase-3E proposal (`private-vivah.{platformId}.user.{externalUserId}` /
> `private-vivah.{platformId}.conversation.{conversationId}`) is superseded by the
> names above, which avoid carrying platform/user identity redundantly (the
> platform is already implied by the token + actor header) and use public ULIDs.

### Connection Sequence (Phase 3E)

`socket/auth` is a server-to-server call: the platform **backend** (which holds the
PASETO credentials) calls it on behalf of its logged-in user and hands the signed
`auth` to its widget. The full widget identity-token bootstrap remains a Phase 5
task and will reuse this same endpoint.

```
1. Widget requests a short-lived identity token from the CLIENT backend
2. Client backend authenticates to MyVivahAI (platform PASETO) and, per open
   thread, POSTs /api/v1/chat/socket/auth with X-External-User-Id
3. MyVivahAI asserts membership and returns { auth, channel_data? } for
   private-chat.{id} / presence-chat.{platform}
4. Client backend passes the signed auth to its widget
5. Widget opens the WebSocket and subscribes, presenting the server-signed auth
6. Realtime server (Reverb/Soketi/Pusher) re-verifies the signature
7. message.created / message.read / conversation.updated / user.online /
   user.offline deliver in real time
```

> The platform owns the actor assertion; MyVivahAI only verifies the id resolves
> inside the platform-scoped map and that the caller is a participant/member
> (AGENTS.md §10) — an arbitrary browser-supplied id is never trusted.

---

## Delivery Flow

### Sending a message (REST, Phase 3D)

```
1. Sender widget POSTs to /api/v1/chat/conversations/{conversation}/messages
   with Bearer platform PASETO + X-External-User-Id: {sender} + body:
   { client_message_id, content, type }
2. Server:
   - Verifies platform token + realtime_chat:write scope
   - Resolves the actor STRICTLY inside the platform's identity map
   - Verifies conversation belongs to same platform (else 404)
   - Verifies sender is a participant (else 404)
   - Idempotency: UNIQUE(platform_id, conversation_id, client_message_id) —
     a retry returns the ORIGINAL row with meta.duplicate=true, never a copy
   - Persists message + updates conversation.last_message_* + increments the
     OTHER participant's unread_count — all in one transaction
3. Sender receives 201 with server message public_id (ULID) and meta.duplicate
```

> Realtime broadcast is implemented as an **optional** layer (Phase 3E) — see
> §Real-Time Transport. The REST send above never depends on it: MySQL is the
> source of truth and the broadcast fires only after the commit.

### Receiving a message

```
1. Recipient socket receives `message.created` on private-chat.{conversation}
2. Widget appends to conversation UI
3. Widget increments the unread badge for that conversation
4. Widget updates timestamp
5. When the conversation is open, widget (via client backend) calls
   POST /conversations/{id}/read; the READER's other participant receives
   `message.read` and the widget clears the badge (advance-only cursor)
```

---

## REST API (Phase 3D — implemented)

Base: `/api/v1/chat` — authenticated with `Authorization: Bearer <platform PASETO>`
and the same scoped middleware stack as every authenticated integration call
(`ValidatePlatformToken` → `EnsurePlatformAccess` → `LogApiAudit` →
`EnforcePlatformRateLimit`). All responses use the standard envelope
(`docs/integration-api.md`).

**Acting user:** all endpoints EXCEPT creation require the
`X-External-User-Id` header naming WHICH of the platform's users is acting.
Resolution is strictly platform-scoped (the value only resolves inside the
calling platform's own `platform_external_user_map`); missing/oversized → `422
VALIDATION_FAILED`, unmapped → `404 NOT_FOUND`.

Scopes: create/send/read = `realtime_chat:write`; list/detail/history =
`realtime_chat:read`.

| Method | Path | Purpose | Scope |
|---|---|---|---|
| POST | `/conversations` | Resolve-or-create a two-party thread (deterministic; order-insensitive pair) | write |
| GET | `/conversations` | The acting user's conversations, last-message-first (NULL threads last) + per-user unread | read |
| GET | `/conversations/{id}` | One thread from the acting user's POV (read state included) | read |
| POST | `/conversations/{id}/read` | Advance-only mark-read + unread reset | write |
| GET | `/conversations/{id}/messages` | Cursor-paginated history (newest-first pages, ASC rows) | read |
| POST | `/conversations/{id}/messages` | Idempotent send | write |

`{id}` is the conversation ULID (`[a-zA-Z0-9]+`).

### Conversation resource (list/detail/create)

```json
{
  "id": "01HMB7R...",
  "status": "active",
  "participants": [ { "external_user_id": "u1", "...": "reference fields" } ],
  "last_message": { "excerpt": "…", "sent_at": "…", "sender_id": "u1" },
  "unread_count": 2,
  "last_read_at": "…",
  "empty_msg": null,
  "created_at": "…"
}
```

`unread_count`/`last_read_at` are the ACTING user's read state (per-participant).
`last_message` is the denormalized excerpt column (not a full message body).

### Message resource (send/history)

```json
{
  "id": "01HMB...", "type": "text", "status": "sent",
  "content": "…", "client_message_id": "…",
  "sender": { "external_user_id": "u1", "…": "reference fields" },
  "sent_at": "…"
}
```

### Idempotency

`client_message_id` (8–64 chars, `[A-Za-z0-9_-]+` REQUIRED) is the idempotency
key, scoped by `(platform_id, conversation_id)`. Retries converge on the
original row and respond `meta.duplicate: true`. Concurrent first-sends race
onto the unique index and are reconciled by one re-read.

### History & cursor pagination

Query params: `limit` (1–100, default 50) and `before` (an **opaque, internal**
message-id cursor). Pages are newest-first but rows are returned ASC; the API
answers:

```json
"meta": { "pagination": { "before": null, "limit": 10, "has_more": true, "next_cursor": 31 } }
```

`next_cursor` (= the smallest message id on the current page) is passed back as
`before` for the next page; `has_more: false` when the tail is reached. The
cursor is opaque by design — internal ids are never serialized as data, so they
cannot be confused with the public ULID ids.

### Mark-read

`POST /conversations/{id}/read` with optional `{ "last_read_message_id": <opaque cursor> }`.
Omitting the body marks everything read. Advance-only: a body referencing an
older cursor is clamped at the current position (never regresses). Response:

```json
{ "data": { "conversation_id": "…", "last_read_message_id": 33, "last_read_at": "…", "unread_count": 0 } }
```

---

## History & Pagination

- Conversation list: page-based (`page`/`per_page`, default 20, max 100), ordered
  by last-message time desc with empty threads sorted last (NULLS LAST).
- Messages per conversation: cursor-based via the opaque `before` cursor
  (`docs/realtime-chat.md §REST API` above).
- Widget may choose to load the last N messages, then lazy-load older ones on scroll.

---

## Unread Counts

Implemented strategy (Phase 3D):

- `conversation_participants.last_read_message_id` = per-participant read cursor.
- `unread_count` = a denormalized INT fast-path maintained incrementally:
  incremented for every OTHER participant inside the message-send transaction,
  reset to 0 inside the mark-read transaction. It never drifts and needs no
  periodic reconciliation.
- Own messages never affect the sender's unread count.
- Read is advance-only: a cursor can only move forward.

---

## Presence

Implemented Phase 3E — **DB-backed, REST-first, Redis-optional**:

- State lives on `platform_external_user_map` (`presence_status` enum
  `online|offline`, nullable `presence_seen_at`; index
  `(platform_id, presence_status)`), so presence works on shared hosting with no
  Redis and no TTL store.
- `POST /api/v1/chat/presence` (scope `realtime_chat:write`) — the acting user's
  heartbeat. Optional `status` (default `online`; explicit `offline` marks
  offline). Persists, bumps `presence_seen_at` AND `last_seen_at`, returns
  `{ external_user_id, presence_status, presence_seen_at, changed }` +
  `meta.offline_after_seconds` / `meta.realtime_enabled`. Broadcasts
  `user.online`/`user.offline` **only on actual transitions** (repeated
  same-status heartbeats are silent, `changed=false`).
- `GET /api/v1/chat/presence/{external_user_id}` (scope `realtime_chat:read`) —
  effective presence for a platform-mapped user, lazily transitioning
  stale-online → offline (persisted + broadcast once). Unknown/cross-platform id
  → `404 NOT_FOUND`. This is the **polling fallback** when realtime is off.
- **Staleness is always computed** from `presence_seen_at` vs
  `config('chat.realtime.presence_offline_after_seconds')` (default 90s): a user
  whose last heartbeat is older than the window reads as offline regardless of the
  stored status — no TTL eviction semantics to get wrong.
- `php artisan chat:presence-sweep` — cron-friendly bulk reconciliation: selects
  online rows idle beyond `presence_sweep_idle_after_seconds` (default 120s,
  bounded by `presence_sweep_limit`), flips each stale row offline inside its own
  `lockForUpdate` transaction (re-checking staleness so a racing heartbeat wins),
  and broadcasts the transition. Optional `--platform=<public_id|slug>` narrows
  to one platform. Suggested cron: `* * * * * php /path/to/artisan chat:presence-sweep`.
- **Presence is a hint, never an authorization mechanism** — channel access is
  always re-checked server-side at subscription time (`ChatChannelAuthorizer`).

---

## Authorization Rules

| Check | Rule |
|---|---|
| Platform scope | Conversation must belong to the caller's platform |
| Participant | Only participants can read/send messages in a conversation |
| Token validity | Identity token must be valid, unexpired, signed |
| Conversation creation | Both users must be same-platform; optional Chat Permission API check |
| Search | Search is performed via client's API on behalf of the platform |

No user may query another platform's conversations, messages, or users.

---

## Chat Permission API Integration

When the client configures the optional Chat Permission API:

- Before opening a conversation with user B, the widget (or server) queries the client's `can-chat` endpoint.
- If `allowed=false`, the widget shows the platform-specific reason.
- In MVP, this check happens at conversation-open time; enforcement on message send is recommended for strictness. **Open decision.**

---

## Rate Limits (Recommended)

| Action | Limit |
|---|---|
| Message send per user | e.g., 30 msg/min (configurable per platform) |
| Conversation create per user | e.g., 20/hr |
| WebSocket connect attempts | e.g., 10/min |
| Auth token exchanges | e.g., 30/min |

---

## Future Considerations

- Typing indicators (via WebSocket `typing` events)
- Read receipts (message status updates)
- Attachments (file upload to MyVivahAI storage; client controls policy)
- Moderation hooks (client moderation API to approve/reject messages) — future
- Historical message export for the client platform
- Blocking/reporting flows

---

## Open Decisions

1. Whether presence is included in the MVP. — **Resolved (Phase 3E)**: included;
   DB-backed (no Redis), REST-first, transitions broadcast when realtime is on.
2. Unread count consistency strategy — **Resolved (Phase 3D)**: incremental
   `unread_count` maintained inside the send/read transactions (no periodic
   recompute needed; own messages excluded; advance-only read cursor); read
   receipts additionally broadcast via `message.read` (Phase 3E).
3. Whether chat permission is enforced server-side at message send (recommended) or only at conversation open (lighter).
4. Message retention policy (keep indefinitely vs prune after N days) and the client's right to export.
5. Whether group chats are ever in scope.
6. WebSocket server — **Resolved**: Laravel Reverb (ADR-010). The Phase 3E
   implementation is driver-agnostic (Pusher protocol; `reverb`/`soketi`/`pusher`
   lanes pre-wired config-only in `config/broadcasting.php`, default `null`); the
   actual server package (`laravel/reverb` + client) still requires a runtime
   Composer install where hosting permits it.
7. Whether messages may include structured payloads (e.g., matrimony profile cards) — future service consideration.
8. History pagination cursor — **Resolved (Phase 3D)**: opaque internal message-id
   cursor (`before`/`next_cursor`) instead of public ULIDs, avoiding same-timestamp
   ordering ambiguity within a millisecond ULID clock.