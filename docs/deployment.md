# MyVivahAI — Deployment Guide

## Objective

MyVivahAI is designed so the **default deployment needs almost nothing**: a PHP +
MySQL shared-hosting account runs the full chat service in REST mode with
DB-backed presence. Realtime (WebSockets) and Redis are **optional enhancers**
added only when the hosting tier can support them — and turning them on never
changes the REST contract.

## Capability Matrix

| Capability | Shared hosting (default) | VPS / cloud (realtime) |
|---|---|---|
| Chat REST (create/send/history/read/unread) | ✅ always | ✅ always |
| Presence (DB-backed `platform_external_user_map`) | ✅ always | ✅ always |
| Presence sweep cron (`chat:presence-sweep`) | ✅ cron | ✅ cron |
| Push events (`message.created` / `message.read` / `conversation.updated`) | ❌ (widget polls instead) | ✅ via realtime |
| Online/offline push (`user.online` / `user.offline`) | ❌ (poll presence endpoint) | ✅ via realtime |
| Redis dependency | none | none (optional) |
| Queue worker dependency | none | none (broadcast inline) |
| Reverb/Soketi/Pusher server | none | one of (for WebSockets) |

**Principle:** MySQL is the source of truth. Every broadcast fires **after** the
commit; failures of the realtime lane are rescued and logged, never surfaced to
the REST caller. If realtime is off or down, the widget falls back to polling
history + the presence endpoint and loses nothing that matters (only latency).

## Minimum requirements

| Item | Requirement |
|---|---|
| PHP | 8.2 or newer (Laravel 12) |
| MySQL | 5.7 + (8 recommended), InnoDB + utf8mb4; the `myvivah` database |
| Storage | Laravel filesystem (default `local`) |
| Cron | one line: `* * * * * php /path/to/artisan chat:presence-sweep` |
| Composer | only needed for **deploys / upgrades** and when enabling realtime |

There is deliberately **no** dependency on Redis, a queue worker, a long-running
process, or a dedicated realtime server for the default REST-only mode.

## Recommended production architecture (realtime tier)

```
Browser widget
   │  (HTTPS)
   ▼
Client platform backend          ── calls socket/auth (server-to-server) ──┐
   │                                                                        │
   ▼ (WebSocket, signed auth)                                               ▼
Reverb / Soketi / Pusher  ◄────── receives broadcasts ─────────────────  MyVivahAI app
   ▲                                                                        │
   └────────────── app_key:hmac‑sha256(socket_id:channel, app_secret)       │
        re-verifies the subscription signature  ◄──────────────────────── (commits → broadcasts)
                                                                             │
                                   MySQL (source of truth) ◄────────────────┘
```

1. The client backend authenticates with a platform PASETO and calls
   `POST /api/v1/chat/socket/auth` for each thread the user has open; it
   forwards the returned `auth` to its widget.
2. The widget opens a WebSocket to the realtime server and subscribes to
   `private-chat.{id}` / `presence-chat.{platform}`. The server re-verifies the
   signed `auth` with the same server-only `app_secret`.
3. MyVivahAI commits message rows to MySQL first, then broadcasts
   `message.created` / `message.read` / `conversation.updated` /
   `user.online` / `user.offline`.

## Enabling realtime (optional)

Steps (only when the hosting tier supports a persistent socket server):

1. Install the vendor client:
   - Reverb (self-hosted server): `composer require laravel/reverb pusher/pusher-php-server`
   - Or Soketi / Pusher / Ably: `composer require pusher/pusher-php-server`
2. Set environment:

   | Variable | Default | Meaning |
   |---|---|---|
   | `BROADCAST_CONNECTION` | `null` | `reverb` / `soketi` / `pusher` / `ably` / `log` |
   | `CHAT_REALTIME_ENABLED` | `false` | master kill-switch for event dispatch |
   | `REVERB_APP_KEY` | — | Pusher-protocol app key — also **signs** `socket/auth` responses |
   | `REVERB_APP_SECRET` | — | server-only secret — used for the signature HMAC |
   | `REVERB_APP_ID` / `REVERB_HOST` / `REVERB_PORT` / `REVERB_SCHEME` | — | driver + server endpoint |
   | `SOKETI_HOST` / `SOKETI_PORT` | `127.0.0.1` / `6001` | when using the `soketi` lane |

   Both `reverb` and `soketi` lanes point at the same pusher-protocol defaults;
   the signing keys are **config**, never exposed to browsers or event payloads.

   The widget receives realtime coordinates (enabled, connection, scheme, host,
   port, app key) from `POST /api/v1/widget/session` (`data.realtime`) — set the
   coordinates the widget should advertise:

   | Variable | Default | Meaning |
   |---|---|---|
   | `CHAT_REALTIME_CONNECTION` | `reverb` | `reverb` / `pusher` / `none` (falls back to `broadcasting.default`) |
   | `CHAT_REALTIME_SCHEME` | `wss` | `wss` / `ws` advertised to browsers |
   | `CHAT_REALTIME_HOST` | `127.0.0.1` | public socket host the browser connects to |
   | `CHAT_REALTIME_PORT` | `8080` | public socket port |
   | `CHAT_REALTIME_PUSHER_CLUSTER` | `mt1` | used when `connection = pusher` |
   | `WIDGET_SESSION_TTL_SECONDS` | `900` | widget session lifetime (capped at `widget.session.max_ttl_seconds`) |
3. Run the socket server:
   - Reverb: `php artisan reverb:start`
   - Soketi/Pusher: provided by their own infra.
4. Confirm the widget bus: with `BROADCAST_CONNECTION=null` + `CHAT_REALTIME_ENABLED=false`
   nothing is dispatched and REST keeps working — safe to ship before step 1-3.

## Redis: optional, never required

`redis` remains a config-only broadcast connection and a possible future cache /
queue lane (ADR-010 allows a Redis-backed profile later). **No feature depends on
Redis today.** Shared hosting with only the database driver is fully supported.

## Cron (always required, both tiers)

| Schedule | Command | Purpose |
|---|---|---|
| every minute | `php artisan chat:presence-sweep` | flip stale-online users offline + broadcast each transition (bounded batch, `lockForUpdate` re-check) |

Optional `--platform=<public_id|slug>` narrows a sweep to one platform.

## Multi-platform isolation & scaling notes

- Every chat/integration table carries `platform_id` and is queried platform-scoped
  (`docs/platform-isolation.md`).
- Conversation list + presence reads are served from the DB (`last_message_at`
  ordering, denormalized unread counts, presence index `(platform_id, presence_status)`)
  — no cache invalidation needed at Phase 3D/3E scale.
- When a fleet deploys behind a load balancer, realtime is a single socket server
  (or a shared Pusher/Soketi cluster); broadcasts are synchronous and the DB write
  already happened, so a fan-out miss only delays the push, never loses the message.

## Deploy checklist

- [ ] `composer install --no-dev --optimize-autoloader` (deploy box)
- [ ] Migrations applied: `php artisan migrate --force` (19 migrations on MySQL)
- [ ] `php artisan config:cache` + `php artisan route:cache` (env baked at deploy)
- [ ] Cron: presence sweep every minute
- [ ] HTTPS enforced by the host/proxy; `APP_URL` correct
- [ ] Optionally enable realtime (steps above) when the tier supports it
- [ ] Serve `public/js/myvivah-widget.js` at the widget URL clients embed
- [ ] Spot-check: send a message via REST, read history, heartbeat + read presence —
      all independent of realtime.
- [ ] Widget check: `POST /api/v1/widget/session` (platform token) → embed token in
      `MyVivahAIWidget.init({session:{...}})`; open a thread, send, refresh.