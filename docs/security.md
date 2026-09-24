# MyVivahAI — Security Documentation

## Overview

This document defines the security requirements and threat considerations for the MyVivahAI platform. It covers authentication, API security, widget security, webhook security, and data security.

**Threat model:** The platform must protect:

1. MyVivahAI accounts (platform owners)
2. The external platforms' data (via cross-platform isolation)
3. End users' identities and messages
4. Stored credentials (API keys, secrets)
5. Financial/payment and subscription data

---

## Authentication

### Account Registration & Verification

- Password hashing: **Bcrypt or Argon2id** (Laravel default bcrypt is acceptable; Argon2id recommended if hosted PHP supports it).
- Email verification enforced: account cannot be used for service access until email is verified.
- Phone verification: open decision; if implemented, OTP with expiry + attempt limits.
- All verification flows rate-limited.

### Password Handling

- Minimum length 8 (recommended 12+), no forced composition rules (NIST guidance).
- Never log passwords or verification tokens.
- Hash comparisons via `Hash::check` (constant-time).
- Password reset via signed, expiring link + optional challenge.

### Session Security

- Session ID in HttpOnly, Secure, SameSite=Lax cookie (`config/session.php`).
- Session driver: Redis or database (recommended over file for multi-server).
- Session rotation/regeneration on login and privilege change.
- Inactivity timeout: configurable (recommended default 30 min).
- Dashboard shows active sessions; user can revoke.

### Token Expiration

- Remember-me tokens rotated on use (via `remember_token` rotation).
- All short-lived integration tokens (widget identity) expire (see Widget Security).

---

## API Security

### MyVivahAI's Own APIs

- All client-dashboard APIs require authenticated platform-owner session.
- All widget-facing APIs require platform context + identity token verification.
- **Platform→MyVivahAI API auth uses PASETO v4.local** (ADR-013, implemented in Phase 3B-1):
  - `POST /api/v1/auth/token` — client exchanges `client_id` (platform `public_id`) + `client_secret` (base64url API key) for a short-lived PASETO. Scopes derived from `platform_service_access` entitlements at issue time; **never client-supplied**.
  - Tokens carry claims `aud`, `platform_id`, `sub`, `kid` (key fingerprint), `iat`, `exp` (≤1h), `jti`, `scope`.
  - `Authorization: Bearer <paseto>` on every protected v1 route; the platform is resolved **from the verified token**, never from the body.
  - Revocation: `POST /api/v1/auth/revoke` blacklists the token `jti`; key/integration revocation is immediate via `revoked_at` state.
- **API key lifecycle (Phases 3B-1/3B-2):** every integration key has a state machine `active → rotated (backup) → revoked` (`platform_api_keys.status`):
  - Rotation demotes the active key to `rotated` and promotes a new key to `active`; the raw secret is returned exactly once and is **never stored or logged** (DB keeps only the AES-256-CBC ciphertext + SHA-256 fingerprint used as the PASETO `kid`).
  - Rotated keys stay usable for a configurable grace window (`paseto.rotation_grace_seconds`, default 24 h) so in-flight clients can migrate; a rotated key presented after grace expires is **hard-revoked on sight** and rejected with `TOKEN_REVOKED`.
  - Only `active` keys resolve as a primary key; tokens never carry platform/body-claimed context.
- Platform-scoped security audit trail appended for every auth outcome and authenticated API call (see Audit Logging).
- Rate limiting on `auth/token` (`paseto.issue` named limiter, per-IP default) — 429 beyond threshold.
- CSRF protection via Laravel middleware on web routes.
- `Accept: application/json`-driven JSON responses for API routes.
- All API errors use the stable envelope `{success, error{code,status,message,request_id}, meta}` (see phase-3a-api-integration-plan.md §11).

### Client Platform API Credentials (the client's own APIs)

MyVivahAI stores credentials used to call the client's external APIs:

| Requirement | Detail |
|---|---|
| Encryption at rest | Credentials encrypted with Laravel `Crypt` (AES-256-CBC / AES-256-GCM via key in `APP_KEY`) |
| Not in plaintext DB | Stored in dedicated `api_credentials` table as `secret_encrypted` |
| Masking in UI | Only masked hint (e.g., last 4 chars) shown in dashboard |
| Rotation | Admin/owner-triggered rotation; credential revision tracked |
| Access scope | Only the owning platform's dashboard can read/write; widget never receives them |

### Rate Limiting

- Login attempts: e.g., 5/min per IP+email.
- Registration: e.g., 10/hr per IP.
- API test runs: e.g., 30/min per platform (dashboard capability — pending implementation).
- Chat message send: per-user limit (see realtime-chat.md).
- **API token issuance** (`POST /api/v1/auth/token`): **5/min**, keyed by `client_id` (else IP) — `config('paseto.issue_throttle')`, Phase 3B-1.
- **Per-platform authenticated API ceiling** (all other `/api/v1/*`): **default 60/min** keyed purely by the *verified* platform id from the token (`integration:<platform_id>`) — `config('api.rate_limit_per_minute')`, per-row override `platform_integrations.rate_limit_per_minute`, enforced by `App\Http\Middleware\EnforcePlatformRateLimit` (Phase 3C). Because the key never derives from client-supplied values, one platform **cannot** consume another platform's quota and cannot evade its own ceiling by presenting a different key.
  - Why a plain middleware class instead of the `throttle:` alias: Laravel priority-sorts the `ThrottleRequests` middleware *before* custom middleware, which would run it before the platform context exists (`ValidatePlatformToken`). The dedicated class preserves execution order (see `decorate` order in `routes/api.php`).
- Global throttling per platform across its endpoints: framework `throttle:api` (60/min/IP) also applies to the v1 api group.
- Exceeded ceilings return `429 RATE_LIMITED` with a `Retry-After` header and `retry_after_seconds`; the rejected request is still audit-logged (`LogApiAudit` wraps the rate-limit middleware).

### Request Validation

- Every write request validated through Laravel Form Requests.
- Input sanitization for message content (strip/escape on render).
- Max sizes enforced (message length, file sizes when attachments come).
- JSON body size limits.

### API Versioning

- All external-facing widget APIs prefixed `/api/v1`.
- Dashboard APIs can be unversioned initially but a version prefix is recommended.
- Breaking changes introduced via new major version only.

### Realtime Channel Authorization & Signing (Phase 3E)

Realtime adds a browser-facing subscription surface, so channel access is
**always** authorized server-side (never by the widget leaning on channel
obscurity):

- Channels are `private-chat.{conversation public_id}` (threads) and
  `presence-chat.{platform public_id}` (presence). Names carry **only public
  ULIDs** — never internal ids, secrets, or profile data.
- `POST /api/v1/chat/socket/auth` (scope `realtime_chat:read`, platform PASETO +
  `X-External-User-Id`) asserts participant membership (private) or platform
  membership (presence, restricted to the token's **own** platform) and returns
  the **Pusher-protocol signature**
  `auth = app_key:hmac-sha256(socket_id:channel[:channel_data], app_secret)`.
  The `app_secret` is server-only config; the realtime server re-verifies the
  signature with the same secret, so nothing secret ever reaches the browser and
  the API simply decides WHO may subscribe.
- Denials are `403 CHANNEL_DENIED` — a non-participant, foreign-platform or
  unknown-channel request is indistinguishable, so channel existence is not
  disclosed.
- The PASETO credential call is server-to-server: the platform backend calls
  `socket/auth` on behalf of its user and hands the signed `auth` to its widget
  (AGENTS.md §10 — raw user ids from the browser are never trusted alone).
- Presence is a **hint, never an authorization mechanism**: reading a user's
  presence never grants channel access, and subscriptions are re-checked on every
  `socket/auth` call.
- Messages are **persisted before broadcast** and REST never depends on realtime
  (`ShouldRescue` swallows a dead transport), so a compromised or unavailable
  realtime lane cannot silently drop authoritative data.

---

## Widget Security

### Secure User Identification (Core Concern)

Raw external user IDs must **never** be trusted from the browser. The widget authenticates via a short-lived signed token issued by the client's backend.

Token requirements:

| Property | Requirement |
|---|---|
| Issuer | Client platform backend only (server-side) |
| Signing | Shared secret (registered platform secret) |
| Claims | `platform` (public_id), `external_user_id`, `jti` (unique), `iat`, `exp` |
| Expiry | 5–15 minutes (recommended); refresh flow required |
| Replay protection | Unique `jti` checked/revoked server-side within validity window |
| Algorithm | **PASETO v4.local** — Resolved (ADR-006, see decisions.md) |

Verification flow on MyVivahAI:

1. Parse token.
2. Verify signature — must match the **platform's** registered secret (not a global master secret).
3. Verify `platform` claim matches the platform context.
4. Verify `exp` not passed.
5. Check `jti` not previously used (short-lived cache).
6. Resolve external user within that platform scope.

**Important: MyVivahAI verifies the client's identity token against the platform-specific secret.** This is the mechanism preventing cross-platform impersonation.

### Platform Identification

- Public platform ID (`platforms.public_id`) is used in widget config.
- It is **not** secret — it's a public identifier.
- Identity tokens must still be verified per-platform as above.
- No global-next tokens; authorization always resolves against the requesting platform.

### Conversation Authorization

- WebSocket subscription to `private-chat.{conversation public_id}` is authorized
  server-side via `POST /api/v1/chat/socket/auth`: only participants are signed in
  (Phase 3E; other channels are `403 CHANNEL_DENIED`).
- Message send: validated that sender is a participant and conversation platform == sender platform.
- Message reads: only participants.
- Conversation listing: only conversations where the user is a participant in the user's platform.

### Preventing Unauthorized Access

| Attack | Control |
|---|---|
| User ID spoofing | Signed token (never raw ID) |
| Token replay | `jti` reuse check + short expiry |
| Cross-platform data | platform_id enforced on every DB query and channel |
| Forged WebSocket subscribe | Private/presence channel auth server-side (`socket/auth` — participant/member enforcement + Pusher-protocol signature) |
| Tampered widget config | Config resolved server-side; public fields only in page |
| ID enumeration | ULID public IDs |

### Avoiding Exposure of Secret Credentials

- Widget embed script contains **only** public platform id and public config.
- No client API credentials embedded.
- Token endpoint (client-side call) means the secret stays on the client backend.

### Phase 4 — Implemented Widget Trust Model

The product-shaped flows above are implemented as follows (see also `docs/widget-integration.md` §Phase 4):

- **Two-token separation.** The platform backend holds long-lived **platform PASETO** `aud = platform:{slug}`. `/api/v1/widget/session` exchanges it for a **short-lived widget session** `aud = widget:{slug}` bound to ONE external user. The two audiences never cross: a widget token on a platform route, or a platform token on a widget route, is rejected `403 PLATFORM_MISMATCH`. The platform secret/API key is never minted in, nor sent to, the browser.
- **Identity is token-bound.** Every widget call resolves the acting user from the verified token (`sub` + `external_user_id` claim match); `ExternalUserContext` **ignores** a spoofed `X-External-User-Id` header on widget calls, and widget `conversations.store` requires the token user be a participant (422 otherwise). `socket/auth` denial is `403 CHANNEL_DENIED` even when the caller presents another participant's id — no existence leak.
- **Session lifecycle.** Jti with `exp` (default 900 s, capped 1800 s). `POST /api/v1/widget/session/revoke` blacklists the jti (`TOKEN_REVOKED`); expired tokens are `TOKEN_EXPIRED`; the emphasized "jti reuse" and "short expiry" requirements are enforced on every request.
- **Per-origin CORS.** Browser preflights are served for `api/v1/widget/*` only (never platform `api/v1/*`). `integration.allowed_origins` (exact or `https://*.sub.example`) is enforced in `RestrictWidgetOrigins`, which runs **after** Laravel's `HandleCors` appends headers — a non-matching Origin's `Access-Control-Allow-Origin` is stripped so no browser-rendered data is readable cross-site (server-side requests without an Origin are never blocked).
- **Payload hygiene.** Realtime broadcasts and audit traces carry public ULIDs + external ids only; the browser token payload holds no secrets.

| Widget-specific attack | Control |
|---|---|
| Browser edits its own token | Signature + widget-claim check server-side; audience isolation |
| Browser swaps `X-External-User-Id` | Ignored when `widget_session` present; always token-bound |
| Browser opens a thread between two *other* users | Store guard requires the token user in `participant_external_ids` |
| Leaked long-lived platform secret via page source | Only the short-lived widget session ever reaches the browser |
| Cross-platform widget session | `aud`/`platform_id` claims + per-platform-scoped queries |
| Revoked/expired session reuse | jti blacklist + `exp` enforced per request |
- MyVivahAI server-side calls client APIs with stored credentials; the browser never sees those credentials.

---

## Webhook Security

(Applies to future webhook features: payment gateways, client platform webhooks, etc.)

| Requirement | Detail |
|---|---|
| Signature verification | Every webhook verified with HMAC signature using a shared secret; reject mismatches |
| Replay protection | Timestamp window + nonce/idempotency key tracking |
| Idempotency | Store processed event IDs; skip duplicates |
| Rate limiting | Per-source throttling |
| Logging | All webhook deliveries logged (headers redacted) and auditable |
| Secrets | Webhook signing keys encrypted at rest |

---

## Data Security

### Data Minimization

- MyVivahAI stores only what is required for a service.
- External platform remains source of truth for user profiles.
- External user records store minimal cached fields.
- Do not pull entire overseas user databases.

### Encryption

| Data | Protection |
|---|---|
| Passwords | Hashing (never reversible) |
| API credentials | Encrypted at rest (`Crypt`) |
| Payment information | Never stored — handled by gateway; store only gateway references |
| Message content in transit | TLS 1.2+ required |
| Message content at rest | Open decision (DB-encryption vs application-level; weigh indexability vs privacy) |
| Backups | Encrypted backups |

### Sensitive Data Handling

- Emails/phones stored only when required by a service.
- Profile photos cached with referrer-hidden URLs on request.
- Logging must never include: passwords, tokens, full API secrets, payment PAN data.
- Log redaction helpers applied to request/response logs.

### Access Control

- Role-based access within a platform (owner/admin/developer) — open decision on final roles.
- Admin-level MyVivahAI access controlled via additional role gates.
- Every service-layer operation verifies platform ownership.

### Audit Logging

Implemented as an append-only, platform-scoped integration audit trail (Phase 3B-2, migration `2026_09_22_000012_create_api_audit_logs_table`, model `ApiAuditLog`, writer `ApiAuditService`):

- Events: `token_issued`, `token_rejected`, `token_expired`, `invalid_token`, `wrong_platform`, `insufficient_scope`, `key_rotation`, `request`.
- Append-only: no `updated_at`, no soft delete; rows are immutable.
- Platform-scoped "where applicable": pre-auth failures (unknown client, invalid token) record `platform_id = NULL`; resolved-key events carry the key/API fingerprint.
- Privacy: stores `ip_hash`, request/response SHA-256 checksums, and minimal metadata (reasons, key IDs, `jti`); **no raw secrets, token bodies, or PII**.
- Fail-open: an audit write error is caught and logged via `Log::error`; it never fails the request it describes.
- Every authenticated v1 API call also writes a `request` row (endpoint, method, status, duration) via `LogApiAudit` middleware.
- Other recommended audit events (logins, platform/status/integration/subscription changes, admin actions) remain future work alongside the dashboard.

### Backup & Recovery

- Database backups scheduled (RPO target: open decision; recommended daily).
- Restore test run periodically (documented procedure).
- File storage backups/replication.
- Recovery point documented per environment.
- Secrets/failing keys (APP_KEY) backed up securely; loss prevents credential decryption.

---

## Threat Scenarios Checklist

| # | Scenario | Primary Defense |
|---|---|---|
| 1 | Attacker edits widget to claim another user's ID | Signed identity token |
| 2 | Reuse stolen token later | short exp + jti check |
| 3 | User from Platform A reads Platform B conversations | platform_id scoping everywhere |
| 4 | Attacker subscribes to someone else's WebSocket channel | server-side channel authorization (`socket/auth`: participant/member + platform-scoped) + Pusher-protocol signature |
| 5 | Credential leak via page source | no secrets in frontend |
| 6 | Brute-force login | rate limiting + lockout |
| 7 | Replay of payment webhook | signature + idempotency |
| 8 | Massive batch of fake external user references | rate limits + per-platform caps |
| 9 | Message content injection (XSS in chat UI) | escaping on render + CSP |
| 10 | Data breach exposing API secrets | encryption at rest + rotation |

---

## Open Decisions

1. Message content encryption-at-rest approach (openness vs privacy tradeoff).
2. Role model for platform-level collaboration (owner/admin/developer).
3. Whether phone verification is required.
4. HMAC-signed client API auth at MVP (vs static bearer).
5. ~~Audit logging framework~~ — **Resolved**: custom `ApiAuditService` + `api_audit_logs` (append-only) in Phase 3B-2.
6. Whether uploaded attachments (when added) are scanned for malware.
7. Compliance requirements (GDPR, local data residency for India) — confirm scope.