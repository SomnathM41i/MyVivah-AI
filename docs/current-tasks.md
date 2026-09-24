# MyVivahAI — Current Tasks

> Status legend: `[x]` done · `[ ]` pending · `[~]` in progress

This list is a living document. Update it as work proceeds. Do **not** mark tasks as completed unless they are actually completed (code written, tests passing, PR merged).

---

## Phase 0 — Documentation Foundation

- [x] Create documentation directory and file set (`docs/`)
- [x] Document product overview and business model
- [x] Document system architecture and module boundaries
- [x] Document registration flow
- [x] Document subscription flow and entitlements
- [x] Document API integration dashboard flow
- [x] Document external API contract
- [x] Document widget integration flow
- [x] Document real-time chat internals
- [x] Document conceptual database design
- [x] Document security requirements
- [x] Document platform isolation strategy
- [x] Document recommended Laravel architecture
- [x] Document future services extension model
- [ ] Finalize open decisions listed in `decisions.md` and `known-issues.md`

---

## Phase 1 — Project Bootstrap (complete — docs/bootstrap.md)

- [x] Initialize Laravel project (`composer create-project laravel/laravel`) — v12.69.2
- [x] Set up environment config (.env, .env.example)
- [x] Configure MySQL connection (`myvivah`, 127.0.0.1)
- [~] Configure Redis (cache, queue, session) — **blocked: no Redis server in this environment**; keys configured, drivers stay on `database` until Redis is provisioned (ADR-010)
- [x] Configure `.env.example` (maintenance: `database`) and local Docker (skipped — no Docker installed)
- [x] Set up base test suite (2 tests passing) and CI config (documented as pending)
- [x] Establish code style tooling (Pint) and static analysis (PHPStan) config (dev-only)

---

## Phase 2 — Core Platform Domain

### Phase 2A — Schema Plan (complete, pending review)

- [x] Author `docs/phase-2a-schema-plan.md` — authoritative Core Platform Domain schema contract (T1–T8)
- [x] Document conventions (ULID public_id, InnoDB, utf8mb4/utf8mb4_unicode_ci, booleans, soft-delete matrix, indexes, platform isolation)
- [x] Document the mysql_native_password note (legacy escape hatch only; not switched by default)
- [x] Update `docs/README.md` index + project status
- [x] **Approval gate:** schema plan approved by maintainer → Phase 2B migrations authorized
- [x] Phase 2B: Author + run the T1–T8 business migrations (1:1 with `docs/phase-2a-schema-plan.md`)
- [x] Phase 2B: Verify all 8 business tables exist on `myvivah` (InnoDB, utf8mb4, plan columns + indexes) — `platforms`, `platform_admins`, `services`, `plans`, `subscriptions`, `payments`, `platform_service_access`, and the `users` Phase 2A column additions
- [x] **Phase 2C — Eloquent models (approved, done):** 8 models authored 1:1 with migrations + plan — `User`, `Platform`, `PlatformAdmin`, `Service`, `Plan`, `Subscription`, `Payment`, `PlatformServiceAccess`
  - [x] ULID `public_id` via `HasUlids`/`uniqueIds()` on `User`, `Platform`, `Plan`, `Subscription` (ADR-002; plan §6 — Service/Payment/PlatformAdmin/PlatformServiceAccess are internal-only, no ULID)
  - [x] Soft-delete matrix honored per database.md: soft on User/Platform/Service/Plan; none on PlatformAdmin/Subscription/Payment/PlatformServiceAccess
  - [x] Casts (decimal:2, boolean, datetime, array/JSON), fillable lists, and platform-scoped relationships per plan
  - [x] Verified: 8/8 autoload (composer PSR-4 probe), `php -l` clean, Pint (Laravel preset) clean, test suite 2/2 passing
- [x] **Approval gate (still gated):** factories + seeders — NOT created in Phase 2C (AGENTS.md phase gating); require separate Phase 2D approval
- [x] Implement account registration + email verification
- [x] Implement login/logout + session management
- [x] Implement platform creation/onboarding
- [x] Seed `services` catalog (Real-Time Chat)
- [x] Seed initial `plans` for Real-Time Chat as **demo data** (Free Trial, Starter, Professional, Business — placeholder pricing, ADR-012)
- [x] Implement EntitlementService + `EnsurePlatformAccess` middleware
- [x] Implement subscription lifecycle (activate/expire/cancel/suspend)
- [x] Implement manual admin-approved purchase + payment-verification workflow (no online gateway at MVP — ADR-011)

### Phase 2D — Eloquent Factories & Seeders (EXIT VERIFIED 2026-09-18)

- [x] Author 8 Eloquent factories (`database/factories/`) 1:1 with Phase 2B migrations + 2C models — User, Platform, PlatformAdmin, Service, Plan, Subscription, Payment, PlatformServiceAccess
- [x] Enforce Phase 2A plan conventions in factories: ULID-public_id matrices (ULID on User/Platform/Plan/Subscription per plan §6; none on Service/Payment/PlatformAdmin/PlatformServiceAccess), soft/soft-populated isolation rules, INR decimal:2, enum/hash/bool casts consistent with models
- [x] Author 5 seeders (`database/seeders/`) — DatabaseSeeder orchestrator + DemoUserSeeder + ServiceSeeder + PlanSeeder + DemoPlatformSeeder
- [x] Idempotency: every catalog/demo seeder uses `updateOrCreate` on natural unique keys → `db:seed` re-runs never duplicate
- [x] Demo data policy honored (docs/database.md): example.test emails, fake Indian +91 numbers, INR demo pricing, no real PII/payments/credentials
- [x] **Verified (all green):** `php -l` 21/21 files clean · Pint (Laravel preset) `--test` PASS 21 files · PHPUnit 2/2 · `db:seed` twice → row counts identical (users 3, services 1, plans 3, platforms 1, platform_admins 1), zero duplicates / FK orphans · `migrate:fresh --seed` repeatable
- [x] **Honest limitation:** PHPStan **not run** — requires Larastan via Composer, which is unavailable in this sandbox (reported, not fabricated)
- [x] Docs: `docs/phase-2d-factories-seeders.md` authored; `docs/README.md` index + status, `docs/changelog.md`, this file updated

---

### Phase 3A — API Integration Module Plan (complete, gated)

- [x] Author `docs/phase-3a-api-integration-plan.md` — authoritative API Integration Module plan (PASETO v4.local, token lifecycle, platform-scoped authz + entitlements, external user mapping/sync, v1 endpoint contracts, versioning, validation/errors, rate limiting, audit logging, isolation, tests, required artifacts, implementation order + exit criteria, open decisions)
- [x] Update `docs/README.md` index
- [ ] **Approval gate:** plan approved by maintainer → Phase 3B implementation authorized (per AGENTS.md phase gating)

## Phase 3 — API Integration Module

### Phase 3B-1 — PASETO Authentication Foundation (EXIT VERIFIED 2026-09-22)

- [x] Install/verify `paragonie/paseto` v4.local (Composer present + runtime unit green)
- [x] Models + factories for the auth skeleton: `PlatformIntegration`, `PlatformApiKey`, `PlatformServiceAccess` service typing (+ 3 factories)
- [x] `config/paseto.php` (TTL cap, authentication scope, `service_scopes.realtime_chat`)
- [x] `PasetoTokenService` (issue/validate/revoke, scopes from T8 entitlements, `jti` blacklist, TTL cap)
- [x] `ValidatePlatformToken` + `EnsurePlatformAccess` middleware (scope params, dotted convention)
- [x] v1 routes: `auth/token` (throttled), `auth/revoke`, `platform/me` (+ `IssueTokenRequest`, `ApiException` envelope renderer)
- [x] Tests: `tests/Unit/PasetoTokenServiceTest.php` + `tests/Feature/AuthTokenLifecycleTest.php` (26 new) — issue/validate/revoke, 401/403 envelopes, scope binding, expired/tampered/mismatched, `request_id`
- [x] **Verified (all green):** `php -l` clean · Pint `--test` PASS (71 files) · PHPUnit 31/102 via phpunit and `artisan test` · **PHPStan level 5 = 0 errors** (first run in project; fixed legacy `cache:` config + `parseModelCastsMethod`) · feature suite re-run vs real MySQL `myvivah` (18/73 OK) · `migrate:fresh` 13/13 on MySQL, `primary_token` char(7) verified
- [x] Docs: `docs/decisions.md` ADR-013, `docs/security.md`, `docs/changelog.md`, `docs/phase-3a-api-integration-plan.md` status (3B-1 completed)

### Phase 3B-2 — API Integration Foundation (EXIT VERIFIED 2026-09-22)

- [x] **A — Key lifecycle:** `platform_api_keys.status` (`active`/`rotated`/`revoked`) + index; `ApiKeyService` rotate/revokeAll/generateSecret (one-time secret, never stored/logged); grace-aware `assertKeyUsable` + hard-revoke on expiry; `primaryApiKey()` filters `status='active'`
- [x] **B — Audit trail:** `api_audit_logs` migration + append-only `ApiAuditLog` + fail-open `ApiAuditService` + `LogApiAudit` middleware; events `token_issued|token_rejected|token_expired|invalid_token|wrong_platform|insufficient_scope|key_rotation|request`; `ValidatePlatformToken`/`EnsurePlatformAccess`/`AuthController` audit hooks (status-preserving)
- [x] **C+D — External user identity:** `platform_external_user_map` migration (UNIQUE `(platform_id, external_user_id)`, optional `local_public_id` FK) + `ExternalUserService` (strictly platform-scoped, retry-once upsert) + `POST /api/v1/users/verify` + `GET /api/v1/users/{external_user_id}` (scoped `realtime_chat.write/read`, no profile mirroring)
- [x] **E — Conversation identity reference:** satisfied by the external-user map / `local_public_id`; **no** conversations/participants/messages tables created (realtime chat still gated)
- [x] **F — Tests (23 new):** `ApiKeyServiceTest`, `ExternalUserMappingTest`, `SecurityAuditLogTest`
- [x] **Verified (all green):** `php -l` clean · Pint `--test` PASS (86 files) · PHPUnit + `artisan test` **60 tests / 262 assertions** · PHPStan level 5 = 0 errors · full suite on real MySQL `myvivah` (60/60) · `migrate:fresh` 15/15 on MySQL
- [x] Docs: `docs/security.md`, `docs/api-contract.md`, `docs/api-integration.md`, `docs/changelog.md`, this file, `docs/known-issues.md`
- [x] 3B-6: chat permission + conversation/message endpoint set (REST foundation — **delivered as Phase 3D**; dashboard-config test-slice pending)
- [ ] 3B-7: full §15 contract/dashboard config tests + doc finalization
- [ ] Implement `api_endpoint_configs`, `api_credentials`, `api_test_logs` migrations (dashboard capability config)
- [ ] Implement API Integration dashboard (per-capability configuration)
- [ ] Implement field mapping UI
- [ ] Implement external API client (auth, timeout, redacted logging)
- [ ] Implement integration test runner (queued job)
- [ ] Implement validation of responses against contract
- [ ] Implement integration activation flow

### Phase 3C — Integration API & Per-Platform Rate Limiting (EXIT VERIFIED 2026-09-23)

- [x] `PlatformOnboardingService` + `integration:onboard` artisan command (idempotent; system owner `system@myvivah.local` for `platforms.created_by`; one-time secret)
- [x] `config/api.php` (`rate_limit_per_minute` 60) + `GET/PATCH /api/v1/integration/config` (`authentication` scope) + `IntegrationConfigResource`
- [x] Key management API: `GET /api/v1/integration/keys` · `POST …/keys/rotate` (one-time secret) · `POST …/keys/revoke` (fingerprint, isolation-scoped) + `ApiKeyService::revokeKey()`
- [x] Users API refinement: `GET /api/v1/users` (paginated, `meta.pagination`) · `GET /api/v1/users/{external_user_id}` bumps `last_seen_at` (segment `[^/]+`)
- [x] **Per-platform rate limit** via `EnforcePlatformRateLimit` middleware (keyed by verified platform id; per-row ceiling override; `Retry-After` + envelope `retry_after_seconds`; 429 still audited). Replaced `throttle:integration` alias — Laravel priority-sorts `ThrottleRequests` ahead of custom middleware so the alias ran before the token context existed (root-caused by middleware-order + request-attribute debugging)
- [x] Standardized errors in `bootstrap/app.php`: `RATE_LIMITED` 429 / `NOT_FOUND` 404 / `METHOD_NOT_ALLOWED` 405 / `REQUEST_FAILED` / `INTERNAL_ERROR` 500 / `HttpResponseException` pass-through; `LogApiAudit` try/finally + `statusFor()` audits 4xx/429; fixed `$platform->setRelation('integration', …)` eager-null-relation bug in `onboard()`
- [x] Tests: `IntegrationOnboardingTest` (5) + `IntegrationApiEndToEndTest` (16) — **21 tests / 214 assertions**; full suite **81 tests / 476 assertions** green on sqlite AND MySQL `myvivah`
- [x] **Verified (all green):** `php -l` clean · Pint `--test` PASS (99 files) · PHPUnit 81/81 (sqlite) · **PHPStan level 5 = 0 errors** · full suite on real MySQL `myvivah` (81/81) · `migrate:fresh` 15/15 on MySQL
- [x] Docs: `docs/integration-api.md` (new), `docs/security.md` (rate limiting), `docs/api-integration.md`, `docs/api-contract.md`, `docs/README.md`, `docs/changelog.md`, this file, `docs/known-issues.md`
- [x] (Next item) 3B-6 chat endpoint set — **delivered as Phase 3D** (below); remaining 3B-6/3B-7 dashboard-config tests untouched by 3D

### Phase 3D — Chat API Foundation (EXIT VERIFIED 2026-09-23)

- [x] Schema (3 migrations `2026_09_23_000001`–`000003`): `conversations` (deterministic `participant_key` pair UNIQUE per platform, status enum, denormalized `last_message_*` without FK on `last_message_id` — circular dependency, documented in migration), `messages` (UNIQUE `(platform_id, conversation_id, client_message_id)`, nullable SET-NULL sender FK, cursor/created/sender indexes, SoftDeletes reserved), `conversation_participants` (seat UNIQUE, read state `last_read_message_id`+`last_read_at`+`unread_count`, platform index)
- [x] Models + factories: `Conversation` (ULID), `Message` (ULID + SoftDeletes), `ConversationParticipant`; `Platform::conversations()`; tenant-aligned factories
- [x] Services: `ExternalUserContext` (`X-External-User-Id` platform-scoped actor; 422 blank / 404 unmapped), `ChatConversationService` (deterministic resolve-or-create + advance-only markRead), `ChatMessageService` (idempotent send + cursor history)
- [x] HTTP surface `/api/v1/chat` (scoped stack; create/send/read=write, list/detail/history=read): `POST|GET /conversations`, `GET /conversations/{id}`, `POST /conversations/{id}/read`, `GET|POST /conversations/{id}/messages` + 5 FormRequests + 3 resources + `config/chat.php`
- [x] Tests: `tests/Feature/ChatApiEndToEndTest.php` (12 tests / 224 assertions) — deterministic resolve, validation, idempotent send + unread-only-for-others, participant + cross-platform authz (404), gapless cursor pagination, advance-only unread reset, read-scope 403 + audit, missing/unknown actor header
- [x] **Verified (all green):** `php -l` clean · Pint `--test` PASS (123 files) · PHPUnit **93/93 · 700 assertions** (sqlite AND MySQL `myvivah`) · PHPStan level 5 = 0 errors · `migrate:fresh` 18/18 · `SHOW CREATE TABLE` UNIQUEs/FKs/indexes verified
- [x] Docs: `docs/database.md`, `docs/realtime-chat.md`, `docs/integration-api.md`, `docs/changelog.md`, this file, `docs/known-issues.md`, `docs/README.md`
- [x] **Stop honored:** no Reverb/WebSockets/Redis/queues/broadcasting/widget/push/subscriptions/payments/admin/AI started

### Phase 3E — Realtime Messaging: Broadcast + Presence + Channel Authorization (EXIT VERIFIED 2026-09-23)

- [x] `config/broadcasting.php` (default `null` no-op; `reverb`/`soketi`/`pusher`/`ably`/`redis`/`log` lanes config-only, pusher-protocol defaults from `REVERB_*`) + `config/chat.php` `realtime` block + env (`BROADCAST_CONNECTION=null`, `CHAT_REALTIME_ENABLED=false`, `CHAT_PRESENCE_OFFLINE_AFTER=90`)
- [x] Schema: migration `2026_09_23_000004` — `platform_external_user_map` `presence_status` (online/offline) + `presence_seen_at` + index `(platform_id, presence_status)`; `ExternalUserMap` constants/casts
- [x] Channel naming + auth: `App\Chat\ChatChannels` (client names + bare transport names to avoid `private-` double-prefix) + `ChatChannelAuthorizer` (participant/platform-member, `403 CHANNEL_DENIED`, Pusher-protocol HMAC signature, presence `channel_data`)
- [x] Events: `RealtimeEvent` base (`ShouldBroadcastNow` + `ShouldRescue` + `broadcastWhen`; **no** after-commit hooks — call sites broadcast post-commit) + `MessageCreated` / `MessageRead` / `ConversationUpdated` / `UserOnline` / `UserOffline` (minimal payloads, no secrets)
- [x] Services: `RealtimeBroadcaster` (single gated dispatch point) + `PresenceService` (DB-backed, no Redis: heartbeat/state/sweep `lockForUpdate`, transitions broadcast only); wiring in `ChatMessageService::send` (non-duplicate) + `ChatConversationService` (`conversation.updated('created')`, `message.read`)
- [x] HTTP: `POST /api/v1/chat/presence` (write heartbeat) · `GET /api/v1/chat/presence/{external_user_id}` (read/polling fallback) · `POST /api/v1/chat/socket/auth` (read, signed auth) + `PresenceController`/`SocketAuthController` + 2 requests
- [x] Command `chat:presence-sweep` (cron-every-minute, optional `--platform=`, bounded batch)
- [x] Tests: `RecordingBroadcaster` (process-memory + fault injection) + `RealtimeChatTest` (15 tests / 208 assertions) — persist-before-broadcast, secret-free payloads, dedup no-rebroadcast, created/updated events, message.read, transition-only presence + sweep/reconnect, private/presence channel auth allow/deny, invalid inputs, scope enforcement, realtime-disabled quiet, dead-transport REST resilience, polling fallback + 404s
- [x] **Verified (all green):** `php -l` clean · Pint `--test` PASS (22 files fixed) · PHPStan level 5 = 0 errors · full suite **108 tests / 908 assertions** on sqlite AND MySQL `myvivah` · `migrate:fresh --force` 19/19 on MySQL
- [x] Docs: `docs/realtime-chat.md`, `docs/database.md`, `docs/integration-api.md`, `docs/security.md`, **`docs/deployment.md` (new)**, `docs/changelog.md`, this file, `docs/known-issues.md`, `docs/README.md`

---

## Phase 4 — Widget Module (EXIT VERIFIED 2026-09-23)

- [x] **Widget identity-token bootstrap** — `POST /api/v1/widget/session` (platform PASETO, `realtime_chat:write`) mints a **short-lived v4.local widget session** bound to ONE external user (`aud = widget:{slug}`, `sub = external_user_id`); token can never be replayed on platform routes and vice versa
- [x] **Widget API surface** — `api/v1/widget/**` (conversations CRUD/read, messages + idempotent sends, read/markRead, presence heartbeat/show/me, `socket/auth`, `integration/users` + `?q=` search, `session/revoke`); `ValidateWidgetToken` sets `widget_session` + adapted `paseto` so scope/entitlement/audit/rate-limit run unchanged
- [x] **Identity trust** — widget acting-user ALWAYS token-bound (`ExternalUserContext`); spoofed `X-External-User-Id` ignored; widget callers may only open threads they are a participant of
- [x] **Revocation + expiry** — jti blacklist (`session/revoke`) + `exp` enforcement on every request (TTL `widget.session.ttl_seconds`, capped `max_ttl_seconds`)
- [x] **CORS** — `config/cors.php` serves `api/v1/widget/*` preflights; per-platform `integration.allowed_origins` stripped via global `RestrictWidgetOrigins` (exact or `https://*.sub.example`)
- [x] **Widget frontend** — `public/js/myvivah-widget.js` (zero-dependency IIFE: `init/open/close/toggle/destroy/get/version`; floating + inline; REST-first + optional realtime; optimistic sends + `client_message_id` dedupe; presence via polling; session-expired handling)
- [x] **Local demo** — `GET /demo/chat` (local + APP_DEBUG only, 404 otherwise), `ChatDemoController` + `demo/*` views; idempotent `DemoPlatformSeeder` (demo integration + primary key + 5 demo users)
- [x] **Tests** — `tests/Feature/WidgetApiTest.php` + `tests/Feature/WidgetIntegrationTest.php` (18 tests / 199 assertions) + `tests/js/widget-core.test.js` (15 tests) — all green on sqlite AND MySQL
- [ ] **Not delivered (documented):** `widget_configs` table/UI, embed-script generator UI, test/live toggle, dashboard test checklist — the browser-side `window.MyVivahAIWidget.init({...})` covers embedding today; dashboard/UI tooling remains follow-up (no schema was needed for the API surface shipped)

---

## Phase 5 — Real-Time Chat Module

- [x] REST + DB foundation **delivered in Phase 3D** (EXIT VERIFIED 2026-09-23): `conversations`/`messages`/`conversation_participants` migrations (identity via `platform_external_user_map` — no `external_users` table), conversation create/open/list, message send/persist with idempotency, history cursor pagination, unread counts + read state, cross-platform isolation tests
- [x] Private/presence channel authorization + signed Pusher-protocol auth — **delivered in Phase 3E** (`POST /api/v1/chat/socket/auth`, `ChatChannelAuthorizer`, `403 CHANNEL_DENIED`)
- [x] Presence (DB-backed, no Redis) — **delivered in Phase 3E** (heartbeat / read / `chat:presence-sweep`; transitions broadcast when realtime on + polling fallback)
- [x] WebSocket event broadcasting (Reverb — ADR-010) — **implemented, driver-agnostic, Phase 3E**: events + `RealtimeBroadcaster` + pusher-protocol lanes pre-wired config-only in `config/broadcasting.php`; the actual Reverb server package install (`composer require laravel/reverb pusher/pusher-php-server`) + `reverb:start` remains a runtime deployment step (`docs/deployment.md`) — no long-lived infra assumed
- [x] Widget identity-token bootstrap endpoint — **delivered in Phase 4** (`POST /api/v1/widget/session`; widget sessions are short-lived browser tokens, distinct from platform tokens)
- [ ] Implement search (via client's search API) — `integration/users?q=` search over MyVivahAI-held references is DONE (Phase 4); delegating to the client's external search API remains a future integration hook
- [ ] Message recall/delete (SoftDeletes already reserved on `messages`; service layer pending)

---

## Phase 5A — Public Website + Web Auth + App Shell (EXIT VERIFIED 2026-09-24)

See `docs/phase-5a-public-website-auth.md` for the full implementation report.

- [x] **Public marketing site** (Blade + Tailwind v4 + Vite — see deviation note in the phase doc): Home, About, Services, Plans, Contact + SEO meta emitted by `x-layout.guest` (title/description → `<title>`/`meta.description`/canonical/OG/Twitter) fed from each controller's `meta` array + `marketing-nav`/`marketing-footer` partials. **No invented content:** live services render from `services` (single source of truth — `is_active` drives both the page AND API entitlements); plans are DB-driven (ADR-012); the Services "roadmap" card group is controller copy explicitly badged *Coming soon*
- [x] **Design system components:** `card`, `badge`, `alert`, `field`, `section-heading`, `flash`, `button` (`light`/`light-outline` variants), `logo`, `icon` (eye-off bug fixed); layouts `x-layout.guest` / `x-layout.auth` / `x-layout.dashboard`; `public/favicon.svg`
- [x] **Web auth (all Blade, server-side session):** signup (creates user + platform + `platform_admins` owner row transactionally via `PlatformAccountService`, then `VerifyEmailNotification`), login (blocked for suspended/deactivated/unverified with no enumeration), logout, forgot/reset password (token via `ResetPasswordNotification`), email verification (signed URL + `hash(sha1)`; **no pre-verification login**; `verification.notice` lives outside the guest group so `verified` middleware redirects work)
- [x] **Critical fix:** `App\Models\User` now `implements MustVerifyEmail` — the framework base `User` uses the trait but does NOT implement the contract, so the `verified` middleware previously passed everyone; this is what makes dashboard gating real
- [x] **Auth hardening:** POST flow throttles (`signup`/`login` 6:1, password routes 5:1, verify-resend 3:1, contact 6:1); `ForgotPassword`/`VerifyEmail` return neutral status messages (no enumeration); password reset uses `Password::Reset`, hashed cast
- [x] **Contact inbox:** `contact_messages` migration + `ContactMessage` model (append-only, no updated_at) + contact form with honeypot `website` field (max:0 rejection) + `ip_hash = sha256(raw ip)` (no raw IPs stored) + `ContactMessageMail` + `emails/contact-message.blade.md`
- [x] **Dashboard app shell (honest empty-states):** index (overview), services (entitlements from DB), integrations (API keys + widget embed instructions), subscription, payments, settings; per ADR-011 no fake activation — checkout/gateway UI arrives in a later phase, screens state that plainly; statuses `pending/active/expired/cancelled/suspended` (no `trialing`), payments tracked via `gateway_transaction_id`/`paid_at`/`amount`/`currency`
- [x] **Routes:** public pages, auth group (`auth.web`), dashboard group (auth+verified), guest `RedirectIfAuthenticated` → `intended()` on route **`dashboard`** (renamed from `dashboard.index`); `/demo/chat` unchanged
- [x] **Tests (6 new files):** `PublicPagesTest`, `ContactSubmissionTest`, `AuthUserRegistrationTest`, `AuthLoginTest`, `PasswordResetTest`, `DashboardAccessTest` — **166 tests / 1285 assertions** on sqlite AND MySQL
- [x] **Verified (all green):** `php -l` clean on every changed file · Pint `--test` **PASS (187 files)** · PHPStan level 5 **No errors** · full PHPUnit suite **166/166 · 1285 assertions** on sqlite AND real MySQL `myvivah` · `migrate:fresh` 20/20 on MySQL (adds `contact_messages`) · `npm run test:widget` 15/15 · `vite build` success (`public/build/manifest.json` + `app-*.css/js`) · `php artisan route:list` shows the full 5A web-route set
- [~] **Follow-ups (documented, not blocking):** dashboard profile/settings editing, subscription checkout/payment gateway UI, API-testing screen UI, plan purchase landing, contact inbox admin list — all deferred to later phases
- [x] Docs: `docs/phase-5a-public-website-auth.md` (new), `docs/changelog.md`, this file, `docs/known-issues.md`, `docs/README.md`

---

## Phase 6 — Hardening & Launch

- [x] Rate limiting on all sensitive endpoints (auth/token throttled 5/min; **per-platform API ceilings enforced — Phase 3C**) — remaining: login/registration/IP limits, dashboard test-run limits
- [~] Audit logging — integration/API audit trail implemented (Phase 3B-2); dashboard-level auth/admin audit pending
- [ ] Backup procedures and recovery runbook
- [ ] Monitoring/alerting
- [ ] Production deployment pipeline
- [ ] End-to-end test with a sample client platform
- [ ] Live pilot with a pilot matrimony platform

---

## Backlog (Not Scheduled)

- [ ] Matrimony AI Agent (defined in future-services.md)
- [ ] Data Entry Agent
- [ ] WhatsApp integration
- [ ] Message attachments
- [ ] Typing indicators / realtime read receipts (per-user read/unread state IS implemented REST-side — Phase 3D)
- [ ] Group conversations (decision pending)
- [ ] Widget analytics dashboard

> **Phase 2D exit (verified 2026-09-18):** 8 factories + 5 seeders authored, Pint-clean (21 files), `php -l` 21/21, PHPUnit 2/2, `migrate:fresh --seed` green, `db:seed` twice = same row counts (no dupes — idempotent via updateOrCreate). Composer unavailable → PHPStan honestly not run (documented in docs/phase-2d-factories-seeders.md).
