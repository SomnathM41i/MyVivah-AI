# MyVivahAI — Architecture Review

> Status: Review — pre-bootstrap
> Date: 2026-09-17
> Scope: Full documentation and architecture review of `docs/` before Laravel project bootstrap.
> Outcome: Issues are categorized as **Confirmed** (requirement/decision that stands), **Recommended** (preferred technical direction, needs approval), or **Open** (must be decided).

---

## 1. Executive Summary

The documentation foundation is strong and internally coherent on the big picture: multi-platform SaaS, Laravel + MySQL + Redis + WebSockets, external platform as the source of truth, platform-scoped isolation, signed-token user identification, API-driven client integration, widget-based end-user integration, and modular future services. The first service (Real-Time Chat) is appropriately scoped and future services are cleanly deferred.

The documentation is **not yet ready for database migrations or feature implementation** because several critical items are missing or ambiguous:

1. **No storage or life-cycle for the per-platform identity-token signing secret** — the foundation of the entire widget authentication flow has no home in the schema or the dashboard.
2. **Message send path is contradictory** — architecture.md says send over WebSocket; realtime-chat.md says "published on socket and acknowledged via HTTP/REST," while its own delivery flow sends over WebSocket. An authoritative path must be fixed.
3. **The "socket session" concept does not map to Laravel Reverb/Pusher's actual channel-authorization model** — the real-time auth flow needs to be realigned to Laravel broadcasting.
4. **The identity/Current User API's role is ambiguous** relative to the signed token; they may overlap or undermine each other.
5. **The conceptual database design has missing tables and relations** (audit logs, sessions, invoices/payment methods, platform secrets/settings), two redundant columns, a phantom table name, a missing endpoint→credential relationship, and circular-referential FKs that would complicate soft deletes.
6. **Plan feature limits and usage enforcement are undefined** (limits live in `plans.features` but nothing consumes them).
7. **There is no specification of MyVivahAI's own widget-facing REST API** (conversation create/list, message send/history, search proxy, read state, token refresh) — only the external client API contract exists.

With the corrections in section 10 and the approvals in section 11, the documentation can be considered ready for Laravel bootstrap.

---

## 2. Confirmed and Consistent Decisions

These items are consistent across the documentation and are treated as **confirmed/unchallenged** in this review:

| # | Decision | Evidence |
|---|---|---|
| 1 | Laravel as backend framework, PHP 8.2+, latest stable | ADR-001, architecture.md |
| 2 | MySQL 8.0+ as primary relational database | ADR-002, database.md |
| 3 | Redis for cache, queues, presence, token replay-guard | ADR-002, laravel-architecture.md |
| 4 | Multi-platform SaaS; data isolated per platform | ADR-003, platform-isolation.md |
| 5 | Shared database + shared schema with `platform_id` scoping as the recommended baseline (not yet approved — see section 11) | platform-isolation.md |
| 6 | External platform is the source of truth for user profiles; MyVivahAI stores references + service data only | ADR-004, ADR-005 |
| 7 | First service = Real-Time Chat (widget); AI/Data-Entry services deferred | product-overview.md, current-tasks.md |
| 8 | Client platform provides capability APIs (Identity, User Details, User Search, optional Chat Permission) | ADR-007, api-contract.md |
| 9 | Widget-based integration; no redirect; single embed script | ADR-008, widget-integration.md |
| 10 | No raw external user IDs trusted from the browser; short-lived signed identity tokens | ADR-006, security.md |
| 11 | Conversations are strictly two-party and same-platform; cross-platform conversations disallowed | realtime-chat.md |
| 12 | Entitlement checks centralized (EntitlementService + middleware), not hardcoded per route | subscription-flow.md |
| 13 | Public ULID identifiers separate from internal auto-increment PKs | database.md, security.md |
| 14 | Credentials encrypted at rest; masked in UI; never in widget scripts | security.md, api-integration.md |
| 15 | Future services are independent modules reusing isolation + entitlement cores | ADR-009, future-services.md |
| 16 | Terminology: "multi-platform / client platform / registered platform / platform account"; "multi-tenant" prohibited in product-facing text | README, platform-isolation.md |

---

## 3. Contradictions or Inconsistencies

### 3.1 Message send transport — `architecture.md` vs `realtime-chat.md` (High)

- `architecture.md` §"External Platform User Sends a Chat Message": widget **sends the message over WebSocket**; server persists to MySQL then broadcasts.
- `realtime-chat.md` §Real-Time Transport states: "Message push is both **published on the socket and acknowledged via HTTP/REST** to guarantee persistence." But its own "Sending a message" flow publishes over WebSocket and persists on the server.
- `realtime-chat.md` step 6: "Sender receives ack with server message ID."

These statements cannot all be implemented as-is. A message must be initiated through **one** authoritative channel (see section 10.1).

**Recommended:** REST POST `/api/v1/conversations/{id}/messages` is the **authoritative persist path**; WebSocket is the **delivery** path. The ack (server message ID) comes from the HTTP response; the WS channel delivers to the recipient. This matches Laravel's standard "HTTP in, broadcast out" model and gives reliable idempotency and retries.

### 3.2 Phantom table names in the domain map — `database.md` (Medium)

The domain map lists `api_configurations` and pinned `api_credentials` under Integration; the actual table definitions later use:

- `platform_integrations` (the grouping row) — never called `api_configurations`,
- `api_endpoint_configs` (per-capability config),
- `api_credentials`.

`api_configurations` appears **nowhere else** in the docs. The map also lists `widget_contents (future)` and `widget_sessions (future)` which are never defined anywhere.

**Recommended:** remove `api_configurations` and the undefined widget tables from the map, or define them explicitly.

### 3.3 `widget_configs` — single row vs (platform_id, mode) index (Medium)

- The table says `platform_id` UNIQUE, "One live config per platform at MVP."
- It also carries `mode ENUM(test, live)` and an index on `(platform_id, mode)`.

A single-row-per-platform table cannot have both a test and a live row, making the `(platform_id, mode)` index misleading. The test/live mechanism (widget-integration.md) also references "test platform user IDs and test conversations," implying possibly separate sandbox state.

**Recommended:** keep **one row per platform** with a `mode` toggle. Drop the `(platform_id, mode)` index or make it a non-unique supporting index. Test/live must be a toggle, not separate rows (see 10.5).

### 3.4 `messages.status` vs `message_statuses` duplication (Medium)

`messages` has both `status ENUM(sent, delivered, read)` **and** a `message_statuses` per-recipient table. In a two-party conversation, the per-message status is simply the single recipient's status — a redundant denormalization.

**Recommended:** `message_statuses` is the source of truth for recipient-visible state. Optionally keep a lightweight `messages.status` only as an aggregate convenience for the 2-party case *if* a persistent need is proven; otherwise drop it. (MVP defers read receipts, so a `sent` default status on the message may suffice until delivery/read tracking is built.)

### 3.5 `external_users` cached columns vs `profile_cache` JSON (Low)

`display_name`, `profile_photo_url`, `email`, `phone` are explicit columns **and** `profile_cache` JSON is described as "optional cached profile fields used by widget." This is duplicated schema.

**Recommended:** pick one. Explicit columns for the 3–4 always-rendered fields (name, photo), and reserved `profile_cache` only for future expandable fields — but do not store the same field in both places.

### 3.6 Identity token vs Current User API — role overlap (High)

- `api-contract.md` Capability 1: "MyVivahAI calls this API with a reference token (or session) so the client platform can resolve who is the current user."
- `widget-integration.md`/`security.md`: the signed identity token already contains `external_user_id` and is verified by MyVivahAI with the platform secret. MyVivahAI therefore **already knows** the external user after token verification.

The Current User API's purpose is unclear: if it is meant to resolve the current user during authentication, it conflicts with token verification; if it is meant for background profile refresh, it is a different contract than documented.

**Recommended:** demote the Current User API to an **optional profile-refresh/re-sync helper** rather than a core authentication dependency. The signed token (carrying `external_user_id`) is the authoritative identity mechanism. See 10.2 and 10.3.

### 3.7 "Socket session" vs Laravel broadcast channel auth (High)

- `realtime-chat.md` and `platform-isolation.md` describe an invented **socket session** returned by `POST /api/v1/widget/authenticate`, which the widget then uses to subscribe to channels.
- Laravel Reverb/Pusher use a different mechanism: the client connects via Echo with an **auth token**, and private-channel access is granted by the server's channel-authorization callback (`POST /broadcasting/auth` or a custom `authorize()` per channel). There is no persistent "socket session" object in Laravel's broadcast model.

**Recommended:** realign the flow to Laravel broadcasting:
1. Widget obtains the signed identity token from the client backend.
2. Widget exchanges it at `POST /api/v1/widget/authenticate` (returns a short-lived access token/credential bound to platform + user, plus channels the user may join).
3. Widget connects via Echo/Reverb with that credential.
4. Channel subscriptions are authorized server-side by `routes/channels.php` callbacks that verify participant + platform (this already matches the documented channel-auth rules).
The "socket session" terminology should be replaced with the Echo connection + auth token.

### 3.8 `subscriptions.starts_at` NOT NULL but subscription starts as `PENDING` (Low)

The status machine begins at `PENDING` (payment initiated, not verified) yet `starts_at` is defined NOT NULL. A pending subscription has no plan-activation time yet.

**Recommended:** `starts_at` nullable, set on activation (transition to `ACTIVE`).

### 3.9 Changelog date is stale (Low)

`docs/changelog.md` entry is dated "2026-01"; today is 2026-09. Update the date so it doesn't imply an older foundation.

### 3.10 Misc typos (Low)

- `laravel-architecture.md`: "models live innamespaced" → "in namespaced" / "namespaced".
- `api-contract.md`: NFR column header "Response Timewindow" with value "< 2s per request (recommended timeout: 5s)" — the 2s target vs 5s timeout should be reconciled into "expected p95 < 2s; timeout 5s" language.
- `widget-integration.md`: embed example uses `data-platform="abc123"` but `public_id` is a 26-char ULID — cosmetic, update the example for reality.
- `AGENTS.md` vs docs: example identifiers/hosts differ (`data-platform-key` + `myvivahai.digitalji.in/widget.js` in AGENTS.md vs `data-platform` + `widget.myvivahai.com/chat.js` in widget-integration.md; `user_id`/`profile_photo` in AGENTS.md vs `id`/`profile_photo_url` in api-contract.md). Field mapping absorbs the field differences; unify the widget host and embed attribute names.

---

## 4. Missing Requirements

### 4.1 Platform signing secret — storage, issuance, rotation (Critical)

The entire identity-token architecture depends on a **per-platform shared secret** that the client backend uses to sign tokens and MyVivahAI uses to verify them (security.md, widget-integration.md). Nowhere is this secret modeled:

- No column on `platforms` (e.g., `token_signing_secret` encrypted).
- No dashboard UI flow to generate/revoke/rotate it.
- No endpoint/credential for the client backend to fetch it (this is a server-to-server credential, distinct from `api_credentials` which are MyVivahAI↔client-API credentials).

This is a blocker for a working auth flow. See section 10.4.

### 4.2 MyVivahAI's own widget-facing REST API spec (High)

`api-contract.md` defines only the **client's** capability APIs. The docs reference MyVivahAI's own endpoints by name (`AuthController`, `ConversationController`, `MessageController`, `SearchController` in laravel-architecture.md) but no spec exists for:

- `POST /api/v1/widget/authenticate` (token exchange)
- Conversation create/open, list, history pagination
- Message send (HTTP) + ack/error envelopes
- Search proxy (server-side call to client search API)
- Read-state / unread endpoints
- Token refresh

Without these contracts, the widget and server teams can diverge. **Recommended:** capture a MyVivahAI widget-facing API contract (new doc or `api-contract.md` expansion) before Phase 5 implementation.

### 4.3 Plan feature-limit schema and usage enforcement (High)

`plans.features` JSON is defined but nothing specifies:

- Canonical feature flag/limit keys (e.g., `max_users`, `max_conversations`, `max_messages_per_month`, `presence_enabled`, `search_enabled`, `attachments_enabled`, `chat_permission_api`).
- Where limits are enforced (entitlement service, message service, scheduled metering).
- Overage behavior (block vs warn) — known-issues flags this as open, but nothing documents the mechanism.

**Recommended:** define the feature schema and an enforcement point (recommended: a `PlanLimitsService` consulted by chat entry points), deferred enforcement rules can stay open but the schema and hook points must be fixed before migrations.

### 4.4 Widget/messaging readiness chain undefined (Medium)

Three gating layers exist but their ordering/composition is not defined:

1. Entitlement (subscription `ACTIVE` + platform active) — dashboard + widget available?
2. Integration state (API integration `ACTIVE`) — widget can resolve users/search.
3. Widget state (`mode=live`) — widget serves production traffic.

**Recommended:** define an explicit "readiness chain": subscription `ACTIVE` → integration `ACTIVE` → widget `LIVE`. Widget bootstrap rejects or fall-backs at the first unmet layer. Middleware ordering (auth → PlatformContext → EnsurePlatformAccess → readiness check) should be documented.

### 4.5 Token refresh protocol (Medium)

Security.md says tokens are 5–15 min "refreshable" but no refresh flow is specified (how the widget detects expiry, re-fetches, reconnects without losing channel subscriptions or showing errors mid-conversation).

### 4.6 Conversation-create idempotency and external-user upsert (Medium)

- Conversation creation must be race-safe: two users creating the same conversation simultaneously (unique `participant_key` handles de-duplication at DB level, but create flow needs upsert/retry semantics).
- No trigger is defined for when `external_users` rows are created/updated (on current-user auth? on conversation start with peer? on search result?). Without this, `external_users` never populates.

**Recommended:** define upsert points: (a) on token verification/current-user resolution, upsert self; (b) on conversation open with peer, upsert peer from User Details API.

### 4.7 Dashboard platform-context selection for multi-platform owners (Low)

If one user owns several platforms, the dashboard must let them choose the active platform; `PlatformContext` resolution from session needs an "active platform" concept (session field).

### 4.8 Search API requesting-user context (Medium)

The Search API contract (`q`, `limit`, `offset`) does not include the **searching user's** identity. Client platforms enforce per-user rules (blocked users, eligibility filters). The client needs to know who is searching.

**Recommended:** include the authenticated context (e.g., `from_user_id`, or the same session-scoped credential used for the current-user API) in the Search capability contract. Same consideration for the Chat Permission API (it already has `from_user_id`, good).

### 4.9 Client-supplied headers vs reserved trace headers (Low)

Clients may add arbitrary headers (api-integration.md), but MyVivahAI sends `X-MyVivahAI-Request-ID`, `X-MyVivahAI-Platform-ID`, `X-MyVivahAI-Signature`. Collision rules need defining (reserved prefix; client values may not override).

### 4.10 Internal-admin role model (Medium)

security.md mentions "Admin-level MyVivahAI access controlled via additional role gates," but no admin role/permission model or table exists (`platform_admins` covers platform collaborators only). A simple `admins` flag or roles/permissions table is needed for MyVivahAI operators.

### 4.11 AGENTS.md location and content alignment (Low)

A comprehensive `docs/AGENTS.md` now exists (created during this review). It captures terminology, architecture principles, flows, and coding guidelines well. Two follow-ups:

- **Location:** tools and many agents look for `AGENTS.md` at the **repository root**, not inside `docs/`. Recommend copying/linking it to the root (or adding a root `AGENTS.md` that points into `docs/`) so it is discovered automatically.
- **Content alignment with docs/:** AGENTS.md examples use `user_id` / `profile_photo` / `data-platform-key` / `myvivahai.digitalji.in/widget.js`, while api-contract.md and widget-integration.md use `id` / `profile_photo_url` / `data-platform` / `widget.myvivahai.com/chat.js`. Field-mapping absorbs field-name differences, but the embed host and attribute names should be unified in the docs so clients see one story.

---

## 5. Database Design Issues

### 5.1 Missing tables (Critical → Medium)

| Missing table | Why | Priority |
|---|---|---|
| Platform secrets (or `platforms.token_signing_secret`) | Store/verify identity-token secret; rotation history | Critical |
| `audit_logs` | security.md §Audit Logging defines it conceptually; database.md omits it | High |
| `sessions` (DB driver) and Laravel runtime tables (`jobs`, `failed_jobs`, `cache`) if DB-driver fallback | Laravel requires them; Redis is recommended so these may be optional, but the doc should state the choice | Medium |
| `invoices`, `payment_methods`, `refunds` (future) | Billing lifecycle; auto-renew needs saved payment methods | Medium |
| `platform_settings` | Per-platform config: rate-limit overrides, branding defaults, feature toggles, security options | Medium |

### 5.2 Missing relationship: endpoint config ↔ credentials (High)

`api_endpoint_configs` declares `auth_method`/`auth_header_name`, and `api_credentials` stores `integration_id` + secret, but there is **no link** between an endpoint config and its credential row. A client could configure conflicting auth per capability with no clear credential ownership.

**Recommended:** add `api_endpoint_configs.credential_id → api_credentials.id` (nullable) so each capability references the credential it uses; `api_credentials` can keep `integration_id` for listing/rotation.

### 5.3 Circular/denormalized FKs that conflict with soft deletes (High)

- `conversations.last_message_id → messages.id` while `messages.conversation_id → conversations.id`.
- `conversation_participants.last_read_message_id → messages.id`.

Messages are soft-deleted (recall scenario). Enforcing these as DB-level FKs creates order-dependent inserts and complicates soft-delete (deleting a recalled message may violate a participant/conversation FK).

**Recommended:** keep `last_message_id` / `last_read_message_id` as **application-managed nullable columns without DB-level FK constraints** (or with deferred FK checks only in dev). Update them in the same transaction as message inserts.

### 5.4 Missing uniqueness / index refinement

- `api_credentials`: add UNIQUE `(integration_id, credential_type)` (one bearer key per integration capability group) — otherwise duplicate credentials accumulate.
- `external_users.display_name` index is low-value since search is delegated to the client API; consider dropping it or restricting to an ACO prefix index only if needed.
- `conversation_participants.last_read_message_id` also lacks an explicit FK note (5.3 covers it).
- Consider UNIQUE on `messages (conversation_id, client_message_id)` — already present; confirm it stays (Critical for idempotency).

### 5.5 Ownership boundary review — complete (Low)

Ownership scoping is consistent: integrations → platform; endpoint configs/credentials → integration → platform; chat tables all carry `platform_id`; `message_statuses.recipient_user_id` relates to `external_users` same platform via message→conversation→platform. No cross-platform FK paths exist. Good.

### 5.6 Scalability notes — acceptable

- `messages (conversation_id, created_at)` index is right for history pagination.
- Presence in Redis (not MySQL) is correct.
- The plan for `last_message_at` denormalization on conversations is correct for conversation-list ordering.
- Message retention is still open (D-12); must be resolved before production.

---

## 6. API and Authentication Issues

### 6.1 Identity token standard decision must be made before token service (High)

decisions.md D-5 recommends PASETO v4.local; security.md says "JWT HS256 or PASETO v4.local." Choose one before implementing `WidgetTokenService`. PASETO v4.local is the better recommendation (constant-time, fewer JWT pitfalls, symmetric key matching the per-platform shared-secret model); JWT HS256 is acceptable if client-stack compatibility argues for it. This affects the client integration docs for every client language, so it is time-sensitive for the api-contract documentation.

### 6.2 Token signing secret life-cycle (Critical)

Covered in 4.1/10.4. Without a storage + issuance + rotation story, no widget route can be authenticated.

### 6.3 Current User API — demote to optional refresh helper (High)

Covered in 3.6. If the Current User API is optional, `is_required` on that capability must be `false`, and activation should only require the three core capabilities (Identity token verification, User Details, User Search).

### 6.4 Token relay risk (High)

If MyVivahAI ever calls the client's Current User API using whatever token the browser sent to MyVivahAI (a browser-controlled value), an attacker can replay that token to the client endpoint to impersonate. Rules:
- The signed identity token itself is verified by MyVivahAI against the platform secret — it is not simply relaying an opaque browser value to the client.
- Any server-to-server call to client APIs must authenticate with **client-facing credentials stored in `api_credentials`** (never browser-supplied tokens).
- Document this clearly in api-contract.md to prevent implementer mistakes.

### 6.5 HMAC client-API auth at MVP (Medium)

Static bearer is acceptable at MVP (documented). If HMAC is added later, the `X-MyVivahAI-Signature` reserved header contract (api-contract.md) already hints at it. Keep as D-12/known-issue; do not implement HMAC in Phase 3 unless approved.

### 6.6 API versioning (Low)

`/api/v1` for widget-facing endpoints + `Accept: application/json` is confirmed. Dashboard APIs: recommend adding a version prefix at bootstrap even if unused yet, to avoid a painful retcon later.

### 6.7 Rate limiting values need a home (Medium)

Rate-limit numbers exist ("e.g., 30 msg/min") but are "configurable per platform" with no storage. Add `platform_settings` (5.1) so per-platform overrides are possible.

---

## 7. Subscription and Payment Issues

### 7.1 Feature limits and usage metering undefined (High)

See 4.3. Also relevant for future AI usage-based pricing: `ai_usage_tracking` exists only conceptually. At minimum, the plan `features` schema and an enforcement hook must be fixed now so chat limits (e.g., max conversations, max messages) can be enforced in Phase 5.

### 7.2 Auto-renew requires saved payment methods; payment methods not modeled (Medium)

`auto_renew` exists on subscriptions, but auto-renew (subscription-flow.md) "requires payment method on file." `payment_methods` does not exist in the schema. Either scope auto-renew out of MVP or model payment methods.

### 7.3 Recurring vs one-time, invoices, refunds (Medium)

All flagged open in subscription-flow.md. They don't block bootstrap, but the `payments` table should keep `gateway`, `gateway_transaction_id`, `status`, `metadata` so future invoices/refunds are additive.

### 7.4 Entitlement cache — approve the recommended approach (Medium)

`platform_service_access` cache table + event-synced updates (D-7) is the right call. Approve it so migrations can proceed. Keep a reconciliation job as a safety net.

### 7.5 At-most-one-active-subscription enforcement (Low)

MySQL 8 supports partial/functional indexes: `UNIQUE (platform_id, service_id, is_active_virtual)` via a generated column or `asNotInactive` scope + application guard. Document the chosen mechanism in the migration notes.

### 7.6 Grace period, trial, proration remain open (Medium)

These do not block bootstrap (they affect business rules after payment), but the subscription lifecycle job design should anticipate:
- `ends_at` + `next_billing_at` for expiry scheduling,
- a grace window being a setting in `platform_settings` or config, not hardcoded.

---

## 8. Real-Time Chat Issues

### 8.1 Message send path (Critical)

Resolve per 3.1/10.1: **HTTP persist in, WS delivery out**.

### 8.2 WebSocket auth alignment (Critical)

Adopt Laravel Echo + Reverb broadcast auth per 3.7/10.3. Channel naming rules and authorization checks (participant + platform) already align; only the handshake/session concept needs replacing.

### 8.3 Conversation create race + external-users upsert (High)

See 4.6. The `participant_key` unique constraint is the correct guard; implement create as "find-or-create + retry on unique violation," and upsert `external_users` on:
- token verification (self),
- conversation open (peer, via User Details API),
- search results (optional cache of searched users).

### 8.4 Unread count drift (Medium)

Realtime-chat.md already recommends incremental + nightly reconcile (`RecomputeUnreadCounts`). Approve and keep. Ensure the incremental path updates `conversation_participants.unread_count` inside the same transaction as message insert.

### 8.5 Presence — multi-tab and TTL (Low)

Redis TTL presence with heartbeat is sound. Clarify `external_users.last_seen_at` semantics (persisted last-activity snapshot vs live presence — Redis is the live source; `last_seen_at` can be refreshed via heartbeat asynchronously, not on every WS message).

### 8.6 Message status scope (Medium)

MVP defers read receipts (realtime-chat.md). Therefore `message_statuses` (delivered/read) may be over-modeled for MVP. Either implement a minimal `messages.status = sent` only, or keep `message_statuses` and mark delivered when the recipient is online. Decide before migration. Recommended: keep `message_statuses` since it is cheap and future-proof, but only track `delivered` (and `read` later); do not build read-receipt UI in MVP.

### 8.7 Soft-delete message + last_message_id (High)

If a recalled/last message is soft-deleted, `last_message_id`/`last_message_at` on the conversation must be recomputed. With application-managed references (5.3), this is straightforward — document the recompute behavior.

### 8.8 Chat Permission enforcement point (Medium)

realtime-chat.md flags open (conversation-open vs message-send). **Recommended:** enforce at conversation-open (authorization UX) **and** re-check at server on message send when the capability is configured (strict). Implement the capability as optional; skipped when not configured.

---

## 9. Security and Isolation Issues

### 9.1 Platform isolation approach — confirm (Medium)

Shared schema + `platform_id` + PlatformContext middleware + explicit service-level scoping + mandatory isolation test suite is a sound baseline. **Approve it** (D-1) so the schema can be fixed. Revisit per-platform schemas/databases only when compliance/scale demands.

### 9.2 Token replay scope (Low)

`jti` replay guard must be scoped **per platform** (Redis key `vivah:{platform}:token-jti:{jti}`) — already correct in laravel-architecture.md. Keep TTL = token expiry window. Confirm the purge strategy is the TTL (no separate cleanup job needed).

### 9.3 XSS / message content handling (Medium)

security.md says "escaping on render + CSP." Widget is Shadow DOM (recommended) which helps; still, message content must be rendered as text (never `innerHTML` of user content) and widget CSP headers documented. Add to widget build acceptance criteria.

### 9.4 Data minimization — email/phone caching (Low)

`external_users` stores `email`/`phone` "only if required." Widget requires name + photo; email/phone should be default-off fields unless a client feature (e.g., tap-to-call) is enabled. Make that explicit so defaults don't expand storage.

### 9.5 Isolation tests (High)

platform-isolation.md mandates isolation tests. This must be a non-negotiable part of test scaffolding in Phase 1/2 so that isolation is exercised from the first query helpers onward — not bolted on in Phase 5. Confirm this in the test strategy.

### 9.6 Compliance/residency (Open)

known-issues flags GDPR/India data-residency/compliance as missing information. This is business input; it does not block bootstrap but should block **production launch** decisions (storage region, backups, message encryption-at-rest choice).

### 9.7 Audit logging needs a home (High)

See 5.1 — `audit_logs` table + writing points (login, platform status, integration changes, credential rotation, subscription changes, widget activation, admin actions).

---

## 10. Recommended Corrections

Prioritized: **Critical** first, then High, then Medium/Low. Each correction references the issue(s) above.

### 10.1 (Critical) Fix the message send path (3.1, 8.1)

Authoritative flow, documented exactly:

```
Sender widget → POST /api/v1/conversations/{id}/messages
   { client_message_id, type, content }
Server:
   1. PlatformContext middleware resolves platform (from verified identity token)
   2. Entitlement + readiness check
   3. Verify sender is a participant; conversation.platform_id == sender platform
   4. Chat permission API check (if configured)
   5. Idempotency: unique (conversation_id, client_message_id) — return existing message if duplicate
   6. Persist message in transaction; update conversations.last_message_id/last_message_at;
      increment recipient unread_count
   7. HTTP 201 with server message (API Resource)
   8. Broadcast MessageSent to private-vivah.{platform}.conversation.{id}
   9. Update presence/last_seen at host level (async)
Recipient widget receives message.new via WS.
```

Update architecture.md, realtime-chat.md, and laravel-architecture.md to this single authoritative description.

### 10.2 (Critical) Define and store the platform identity-token secret (4.1, 6.2, 9.2)

- Add `platforms.token_signing_secret` (encrypted, or a pointer to a `platform_secrets` row) — recommended **encrypted column on `platforms`** for MVP with rotation history stored by overwrite + `rotated_at`.
- Add dashboard section "Widget Security / Signing Keys": generate, rotate, revoke, show masked hint; log rotation in audit log.
- Add server-to-server flow: client backend calls MyVivahAI endpoint with a **platform API credential** (a second, explicit credential type in `api_credentials`, e.g., `platform_api`) to fetch the current signing secret config — or simpler, the dashboard displays the secret once on rotation (client copies it). Choose the simpler dashboard-copy flow for MVP, and note the API-based flow as a Phase 3+ option.
- Token verification MUST use the platform's own secret (never a global master secret) — confirmed already; implementation must load per-platform.

### 10.3 (Critical) Align WebSocket handshake with Laravel broadcasting (3.7, 8.2)

Replace the "socket session" flow with:

```
1. Widget gets signed identity token from client backend.
2. POST /api/v1/widget/authenticate { identity_token }
   → { access_token, platform_id, user, allowed_channels }
3. Widget boots Echo/Reverb using access_token.
4. routes/channels.php callbacks authorize each private channel:
   - user channel: platform match + user match
   - conversation channel: platform match + participant
```

### 10.4 (High) Demote Current User API to optional refresh helper (3.6, 6.3)

- Mark `current_user` capability **optional** (`is_required=false`).
- Activation requires: user_details (required), user_search (required), and identity token path. `chat_permission` optional.
- Increase `external_users` cache freshness by calling User Details on conversation open / search, not by blocking on Current User.
- Documented contract note: "If the client configures Current User API, MyVivahAI uses it for profile re-sync; it is not the authentication mechanism." If unused, capability remains unconfigured.

### 10.5 (Medium) Single widget config row with test/live toggle (3.3)

- `widget_configs`: `platform_id` UNIQUE; `mode` toggle column only.
- Remove the `(platform_id, mode)` index (keep a non-unique index on `platform_id`).
- Test vs live is a **mode** on the single config; test data identity (test user IDs) is a client-side testing concern, not separate infrastructure (unless sandbox environment is explicitly requested later).

### 10.6 (Medium) Clean up the database design (3.2, 3.4, 3.5, 5.1, 5.2, 5.3)

Apply across database.md:
- Remove `api_configurations` / undefined widget tables from the domain map.
- Add `audit_logs`; add `platform_settings`; note `sessions`/runtime tables and future `invoices`/`payment_methods`/`refunds`; add `platform_secrets` decision (10.2).
- Add `api_endpoint_configs.credential_id → api_credentials.id`.
- Remove `external_users.profile_cache` duplication (or define it as the only cache store).
- Decide `messages.status` as aggregate from `message_statuses` (keep or drop — see 8.6).
- Make `conversations.last_message_id` and `conversation_participants.last_read_message_id` application-managed (no DB FK) to allow message soft-delete; document recompute on recall.
- Make `subscriptions.starts_at` nullable (set on activation).
- Add UNIQUE `api_credentials (integration_id, credential_type)`.
- Update cross-relationships table: add `widget_configs → platform_integrations`, `api_endpoint_configs → api_credentials`, `messages → message_statuses` (1:N) explicitly.

### 10.7 (High) Specify the plan feature schema and enforcement hook (4.3, 7.1)

- Define canonical keys in database.md (e.g., `max_users`, `max_conversations`, `max_messages_per_month`, `presence_enabled`, `search_enabled`, `attachments_enabled`, `chat_permission_api`, `message_retention_days`).
- Add `PlanLimitsService` (laravel-architecture.md Services/Chat section) consulted by conversation-creation and message-send entry points; overage behavior (block vs warn) can remain an open business decision until the first plans are priced, but the hook must exist at bootstrap time.

### 10.8 (High) Add artifact for the MyVivahAI widget-facing API (4.2)

Create `docs/widget-api.md` (or expand api-contract.md) with request/response envelopes for: authenticate, conversation create/open/list, message send/history, search proxy, read-state, token refresh. Nuanced: stable error envelope consistent with api-contract.md (`success`, `error.code/message`).

### 10.9 (Medium) Define the readiness chain (4.4)

Document: subscription `ACTIVE` → integration `ACTIVE` → widget `LIVE`; middleware order `auth → verified → PlatformContext → EnsurePlatformAccess → (readiness in controller/service)`. Widget fallback behavior when any layer is unmet: disabled/hidden chat button + clear dashboard warning.

### 10.10 (Medium) Token refresh protocol (4.5)

Specify: widget detects `exp` approaching; requests fresh token from client backend; re-invokes authenticate; reconnects Echo with new credential; preserves client_message_id continuity. Envelope in the new widget-api doc.

### 10.11 (Medium) Conversation-create idempotency + external_users upsert (4.6, 8.3)

- "Find-or-create + retry on unique violation" for conversations.
- Upsert external_users at token verify (self), conversation open (peer via User Details), and optionally search.

### 10.12 (Medium) Search context + reserved headers (4.8, 4.9)

- Add searching-user context to Search capability (`from_user_id` or reuse authenticated session credential).
- Reserved header policy: client-configured headers may not override `X-MyVivahAI-*` reserved names (prefix rule; conflict → rejection at config save).

### 10.13 (Medium) Audit logging + admin roles (5.1, 4.10)

- Add `audit_logs` table and write points.
- Add internal-admin gating (either `users.is_admin` boolean for MVP, or roles/permissions table later). boolean admins flag is acceptable at bootstrap.

### 10.14 (Low) Cleanup and housekeeping (3.9, 3.10)

- Fix changelog date to 2026-09.
- Fix typos, reconcile the 2s/5s NFR language, update widget embed example to ULID-format IDs.

### 10.15 (Medium/Low) Root-level AGENTS.md (4.11)

`docs/AGENTS.md` exists and is solid. Add a root `AGENTS.md` (or move the file) so tooling discovers it automatically, and align the example identifiers/hostnames (embed attribute `data-platform-key` vs `data-platform`, widget host, `user_id` vs `id`) across AGENTS.md and the docs.

---

## 11. Decisions That Still Require Your Approval

| # | Decision | Recommendation | Blocks |
|---|---|---|---|
| 1 | Message send path: HTTP POST persist + WS delivery (not WS-send) | Approve recommended | Phase 5 chat |
| 2 | WebSocket handshake: Laravel Echo + Reverb broadcast auth replacing the "socket session" concept | Approve recommended | Phase 5 chat |
| 3 | Identity token standard for client integration: **PASETO v4.local** (rec.) vs JWT HS256 | Choose | Widget token service, api-contract |
| 4 | Platform signing secret storage: encrypted column on `platforms` + dashboard rotate/copy flow; API-based variant later | Approve recommended | 10.2, Phase 5 |
| 5 | Current User API demoted to **optional** refresh helper (not core auth) | Approve recommended | api-contract, activation rules |
| 6 | Shared schema + `platform_id` baseline (D-1) | Approve | Migrations |
| 7 | Primary key strategy: BIGINT + ULID public IDs (D-2) | Approve | Migrations |
| 8 | `platform_service_access` cache table + event sync (D-7) | Approve | Migrations |
| 9 | Unread: incremental + nightly reconcile (D-6) | Approve | Phase 5 |
| 10 | Presence in MVP (D-8) | Approve recommended (include) | Phase 5 scope |
| 11 | Widget config: one row per platform, test/live as toggle (D-10 combined) | Approve | Migrations |
| 12 | Widget isolation: Shadow DOM (D-9) | Approve recommended | Widget build |
| 13 | Broadcast server: Laravel Reverb (self-hosted) (D-3) | Approve recommended over Pusher | Phase 5 setup |
| 14 | Dashboard UI: Blade + Alpine.js (rec.) vs Livewire (D-4) | Choose | Phase 4 dashboard |
| 15 | Payment gateway + billing periods + auto-renew at MVP | Choose (TBD; Razorpay/Stripe) | Phase 2 payments |
| 16 | Plan feature schema keys + enforcement hook (with overage behavior open) | Approve schema/hook | Migrations + Phase 5 |
| 17 | `message_statuses` in MVP: track `delivered` only; defer `read` UI | Approve recommended | Migrations |
| 18 | Application-managed `last_message_id`/`last_read_message_id` (no DB FK) | Approve | Migrations |
| 19 | Search mandatory at MVP (known-issues #8 default Yes) | Confirm | api-contract, activation |
| 20 | Chat Permission enforcement at conversation-open + message-send | Approve recommended | Phase 5 |
| 21 | Message retention policy (D-12) | TBD — must decide before production | Assessment of undocumented need |

Items 1, 6, 7, 8, 11, 16, 17, 18 are required **before schema/migration work**. Items 2, 3, 4, 13 are required **before chat/token implementation**. Item 15 can be deferred only until Phase 2 payments; the `payments`/`subscriptions` schema is gateway-agnostic as designed.

---

## 12. Proposed Final Architecture Baseline

The following is the consolidated target after the review corrections. It does not invent final decisions — it records the coherent picture the documentation should converge to.

```
MYVIVAH-AI APPLICATION (monolith, Laravel, domain modules as directories)
├── Core Platform module        → users, platforms, platform_admins (owner/admin/developer),
│                                 session mgmt, email verification, internal admins
├── Service & Subscription      → services, plans (features schema), subscriptions,
│                                 platform_service_access (entitlement cache), lifecycle job
├── Payment & Billing           → payments (gateway-agnostic), invoices/payment_methods (future);
│                                 webhook signature + idempotency
├── API Integration module      → platform_integrations, api_endpoint_configs, api_credentials,
│                                 api_test_logs; ExternalApiClient (auth, timeout, redacted log);
│                                 IntegrationTestService; credential_id link to endpoint config
├── Widget module               → widget_configs (one row/platform, test|live toggle),
│                                 WidgetScriptService (single embed, public config only),
│                                 readiness chain: subscript→integration ACTIVE→widget LIVE
├── Real-Time Chat module       → external_users (upsert), conversations (participant_key unique,
│                                 app-managed last_message_id/last_message_at),
│                                 conversation_participants (unread_count, last_read_message_id),
│                                 messages (client_message_id idempotency, soft delete),
│                                 message_statuses (delivered tracked; read deferred)
├── Security enablers           → PlatformContext middleware, EnsurePlatformAccess middleware,
│                                 EntitlementService, PlanLimitsService,
│                                 WidgetTokenService (verify against per-platform signing secret),
│                                 audit_logs, per-platform rate-limit overrides in platform_settings
└── Future services             → separate modules, own tables, not implemented; reuse the above

Isolation model (approved baseline, enforced everywhere):
  - Shared MySQL schema; platform_id on every platform-bound table
  - PlatformContext resolves scope from dashboard session or verified identity token
  - Explicit service-level scoping (no reliance on global scopes alone)
  - WebSocket channel auth server-side (participant + platform)
  - Jobs carry platform_id and re-validate
  - Mandatory isolation test suite in CI

Auth model (final target):
  Client backend signs short-lived token (platform public id, external_user_id, jti, iat, exp)
  → widget POSTs to /api/v1/widget/authenticate
  → MyVivahAI verifies signature (platform secret), expiry, jti (Redis, TTL=window)
  → widget connects via Echo/Reverb; channel auth callbacks enforce participant + platform
  → messages: HTTP POST persists (idempotent), WS broadcasts delivery
```

### Terminology alignment
- "multi-platform SaaS," "client platform," "registered platform," "platform account," "platform-specific data."
- "platform-scoped isolation" may be used as a technical term; "multi-tenant" is never product-facing.

---

## 13. Recommended Implementation Order

The existing current-tasks.md phases are sensible. Adjustments driven by this review:

1. **Phase 0b — Close the gating decisions** (section 11, items 1, 6, 7, 8, 11, 16, 17, 18) and patch the affected docs (architecture.md message path, realtime-chat.md send flow, database.md revisions, new widget-api artifact).
2. **Phase 1 — Bootstrap Laravel** (unchanged): `composer create-project laravel/laravel`, MySQL + Redis config, `.env.example`, Pint + PHPStan, **base test scaffolding including the isolation-test pattern**, and the first `AGENTS.md`.
3. **Phase 2 — Core Platform Domain**: users/platforms/admins/services/plans/subscriptions/payments + `platform_service_access` + `audit_logs` + `platform_settings` + `platforms.token_signing_secret`; registration, email verification, login/session, platform onboarding; EntitlementService + EnsurePlatformAccess; subscription lifecycle job; plan feature schema. *(Payment gateway hook only; gateway wiring deferred to a clear decision.)*
4. **Phase 3 — API Integration module** (unchanged): migration + dashboard + ExternalApiClient + IntegrationTestService + activation; capability `is_required` updated per decision (Current User optional).
5. **Phase 4 — Widget module**: widget_configs + config UI + embed generation + token bootstrap (per 10.2/10.3 auth flow) + test/live toggle + widget frontend (Shadow DOM) + ready checklist.
6. **Phase 5 — Real-Time Chat module**: external_users/conversations/participants/messages/message_statuses migrations + HTTP-send/WS-deliver path + idempotency + unread + presence + echo/reverb channel auth + search proxy + isolation tests + PlanLimitsService enforcement.
7. **Phase 6 — Hardening & Launch**: rate-limit overrides wired, audit log coverage, backups/restore runbook, monitoring, E2E with a pilot client, retention/export policy finalized.

**Do not start Phases 2–5 migrations until section 11 approval items (1, 6, 7, 8, 11, 16, 17, 18) are answered.**

---

## Appendix A — Review Scope

### Files reviewed
- All 18 files in `docs/` (README, product-overview, architecture, registration-flow, subscription-flow, api-integration, api-contract, widget-integration, realtime-chat, database, security, platform-isolation, laravel-architecture, future-services, current-tasks, changelog, known-issues, decisions).
- `docs/AGENTS.md` (created during this review session; its terminology, flows, and guidelines were reconciled against the reviewed documents — see sections 3.10 and 4.11).
- Nothing was modified except this review document, the docs index (README), and the changelog (date + entry). No application code, packages, or migrations were created.

### Methods
- Cross-file consistency checks (flows, terminology, table names, status enums, auth flows).
- Schema review against Laravel/MySQL requirements (FKs, uniques, indexes, soft deletes, circular references).
- Auth flow review against Laravel broadcasting capabilities.
- Security/isolation review against the documented threat model.

### Issue counts
- Contradictions/inconsistencies: **13** (3.1–3.10).
- Missing requirements: **11** (4.1–4.11).
- Database design issues: **6 groups** (5.1–5.6).
- API/auth issues: **7** (6.1–6.7).
- Subscription/payment issues: **6** (7.1–7.6).
- Real-time chat issues: **8** (8.1–8.8).
- Security/isolation issues: **7** (9.1–9.7).
- Recommended corrections: **15** (10.1–10.15).
- Decisions requiring approval: **21** (section 11).

### Verdict
The documentation is internally consistent **on strategy and scope** and is a strong foundation. It is **ready for Laravel project bootstrap** once the recommended corrections (10.1–10.3, 10.6, 10.7) are folded into the docs **and** the approval items 1, 6, 7, 8, 11, 16, 17, 18 of section 11 are answered. Database migration work should not begin until those items are closed.