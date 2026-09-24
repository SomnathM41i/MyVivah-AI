# MyVivahAI — Architecture Decision Records (ADR)

> Each record: **Status** (Accepted / Recommended / Proposed / Superseded). Items marked "Recommended" or "Proposed" are **not** finalized — see known-issues.md for the open-decision list. Items marked "Accepted" apply to the current project as of this documentation phase.

> **Approved MVP decisions (2026-09-17):** database isolation (shared MySQL + `platform_id`), primary keys (BIGINT internal + ULID public), integration identity tokens (PASETO v4.local, ~5 min), payment workflow (manual admin-approved; no online gateway in MVP; Razorpay/Stripe integrated later), plans (dynamic, admin-managed, demo data, deactivate-not-delete), and real-time broadcasting (Laravel Reverb). Items listed below supersede any earlier "Recommended/Open" status for the same topic.

---

## ADR-001: Laravel as the Backend Framework

- **Status:** Accepted (product requirement)
- **Context:** MyVivahAI is specified as a Laravel-based SaaS.
- **Decision:** Use Laravel (latest stable) on PHP 8.2+ as the backend framework for all dashboard, API, and service logic.
- **Consequences:**
  - Use Laravel ecosystem: Eloquent, Queue, Broadcasting, Policies, Form Requests, Notifications.
  - Eloquent models with MySQL (ADR-002).
  - Feature modules remain standard Laravel directories (see laravel-architecture.md).

## ADR-002: MySQL as the Primary Database

- **Status:** Accepted (product requirement)
- **Context:** The product brief specifies MySQL.
- **Decision:** MySQL 8.0+ as the primary relational database.
- **Consequences:**
  - JSON columns available where appropriate (MySQL JSON type).
  - Redis used for cache, queues, presence, replay-guard (not as primary store).
  - Indexing strategy defined in database.md.

## ADR-003: Multi-Platform Architecture (Isolation per Registered Platform)

- **Status:** Accepted (approved MVP decision)
- **Context:** Multiple independent external platforms subscribe to MyVivahAI services. Data belonging to one platform must never be accessible by another.
- **Decision:** Use product-facing terminology "multi-platform." Internally implement platform-scoped isolation in a **shared MySQL database** with platform-scoped rows.
- **Approved implementation (MVP):**
  - Shared MySQL database for all platforms.
  - A mandatory `platform_id` on every platform-owned record.
  - Platform context resolution, authorization, explicit query scoping, and an isolation test suite.
  - Architecture must remain extensible for dedicated databases per platform in the future (no hard-coded single-DB assumptions that would prevent it).
- **Consequences:**
  - Every platform-bound query is filtered by platform.
  - Certified isolation test suite required.
  - Config/credentials are per-platform.
  - See platform-isolation.md for the approved approach and future-extension notes.

## ADR-004: External Platform Is the Source of Truth for User Profiles

- **Status:** Accepted (product requirement)
- **Context:** Client platforms maintain complete user profiles, sessions, and matrimony data. MyVivahAI must not copy the external user database.
- **Decision:** MyVivahAI stores only external-user references (`external_users` with platform_id + external_user_id) plus the minimal cached fields required by the widget. All profile data remains with the client platform.
- **Consequences:**
  - Search is delegated to the client platform's API (not local search over full user sets).
  - User Details API is called at runtime to render peer profiles.
  - Data minimization rules in security.md.

## ADR-005: MyVivahAI Stores Only Service-Required Chat Data

- **Status:** Accepted (product requirement)
- **Context:** MyVivahAI provides chat; messages/history must live on MyVivahAI for the widget to function across sessions.
- **Decision:** MyVivahAI stores conversations, messages, message statuses, participants (references), and chat-related configuration. It does not store the external platform's complete user data or business data.
- **Consequences:**
  - `external_users` holds minimal cached display fields, not full profiles.
  - External platform can request exports/retention decisions (open decision).

## ADR-006: Secure External User Identification via Short-Lived Signed Tokens

- **Status:** Accepted (approved MVP decision)
- **Context:** The browser is attacker-controlled; a raw external user ID cannot be trusted. The client platform's backend authenticates its own session.
- **Decision:** The client platform's backend issues a short-lived **PASETO v4.local** token for the logged-in user. The widget sends it to MyVivahAI; MyVivahAI verifies the platform signature, expiry, identity, and permissions before resolving the external user in platform context.
- **Approved token contract:**
  - **Format:** PASETO v4.local (symmetric, encrypted; per-platform shared key).
  - **Lifetime:** Short-lived, approximately **5 minutes**.
  - **Payload:** platform identity (public platform id), external user identity (external user id), issued-at (`iat`), expiration (`exp`), and a unique token identifier (`jti`) where required for replay protection.
  - **Issuance:** Must be generated by the **client platform backend** only.
  - **Transport:** HTTPS is mandatory for all token-related traffic.
  - **Validation:** expiration, platform, identity, and permissions are all validated.
  - **Browser safety:** Must never expose client secrets in browser code.
  - **Scope limit:** PASETO is required for client integration identity tokens only. It is **not** required for Laravel dashboard sessions or every internal authentication flow; Laravel's native session/cookie auth remains for dashboard users.
- **Consequences:**
  - Different client stacks (PHP/Laravel/Node/React/custom) can implement the same contract.
  - Never trust browser-passed user IDs.
  - Platform-specific secrets — not one global secret.

## ADR-007: API-Driven Integration (Client Provides Capability APIs)

- **Status:** Accepted (product requirement)
- **Context:** MyVivahAI cannot assume client technology or schema. Integration is achieved by the client exposing capability APIs.
- **Decision:** Each registered platform configures, tests, and activates its own APIs for Identity (current user), User Details, and User Search (plus optional Chat Permission). Config/credentials/field mappings are managed through the API Integration dashboard.
- **Consequences:**
  - Contract defined in api-contract.md.
  - Integration must pass validation before activation.
  - Credentials encrypted at rest (security.md).

## ADR-008: Widget-Based Integration for End Users

- **Status:** Accepted (product requirement)
- **Context:** End users must not leave the external website to chat.
- **Decision:** A single embed script installs a floating chat widget on the client's site. It is configurable, brandable, placeable, and isolated from host styles.
- **Consequences:**
  - Embed code contains only public config; identity via signed token.
  - Shadow DOM / scoped CSS recommended (open decision).
  - Widget communicates with MyVivahAI (backed by client APIs server-side).

## ADR-009: Modular Future Services

- **Status:** Accepted (product requirement)
- **Context:** Matrimony AI Agent, Data Entry Agent, WhatsApp, and more are planned later without restructuring the chat module.
- **Decision:** Architecture keeps services as independent modules with their own `services` rows, `plans`, entitlements, integrations, and tables, reusing the platform-isolation and entitlement cores.
- **Consequences:**
  - No hardcoded service keys.
  - Future services must not alter chat module code.
  - Defined in future-services.md.

---

## ADR-010: Real-Time Broadcasting with Laravel Reverb

- **Status:** Accepted (approved MVP decision)
- **Context:** The chat widget requires real-time message delivery, presence, and unread updates over WebSockets.
- **Decision:** Use **Laravel Broadcasting + Laravel Reverb** as the initial WebSocket server, with **Laravel Echo** on the client, **Redis** for broadcasting/cache/queues, **Laravel Queue**, and **MySQL** as the source of truth.
- **Approved rules:**
  - The database remains the source of truth; **messages must be persisted before broadcasting**.
  - Use **private or presence channels**.
  - Every channel authorization must verify **platform ownership and conversation membership**.
  - Do not use Pusher, Soketi, or Ably unless a specific future requirement justifies changing the choice.
- **Consequences:**
  - Reverb is self-hosted (part of the Laravel app / infra); Pusher/Soketi/Ably are excluded from MVP choices.
  - The auth handshake follows Laravel Echo/broadcast conventions (see realtime-chat.md).

## ADR-011: Manual Admin-Approved Purchase Workflow (MVP)

- **Status:** Accepted (approved MVP decision)
- **Context:** no online payment gateway is integrated during the MVP.
- **Decision:** Implement a **manual admin-approved purchase workflow**:
  1. Client submits a purchase request.
  2. Admin reviews the request.
  3. Admin approves or rejects the request.
  4. Payment is recorded manually.
  5. Admin verifies payment.
  6. Subscription becomes active **only after payment verification**.
- **Approved rules:**
  - Purchase approval and payment verification are kept as **separate states**.
  - Suggested statuses: `pending`, `under_review`, `approved`, `payment_pending`, `payment_submitted`, `payment_verified`, `rejected`, `cancelled`, `expired`.
  - The database must be designed so **Razorpay or Stripe can be integrated later** without restructuring (gateway-agnostic payment table).
- **Consequences:**
  - Subscription activation is gated on a verified payment record.
  - A `purchase_requests` model separates the purchase/review workflow from subscription access state and payment records.
  - No gateway SDKs are required at MVP.

## ADR-012: Dynamic Plans Managed in the Admin Panel

- **Status:** Accepted (approved MVP decision)
- **Context:** plan tiers and pricing should be changeable without code deploys.
- **Decision:** Plans are **managed dynamically through the Admin Panel**:
  - Plans belong to services.
  - Admin can create, edit, activate, deactivate, and configure plans.
  - Plan pricing, billing interval, trial duration, usage limits, and features are **database-driven**.
  - Demo plans are seeded for Real-Time Chat: **Free Trial, Starter, Professional, Business** — with **placeholder pricing clearly marked as demo data** that can be changed later.
  - Limits and feature flags use a flexible **`plan_features`** system supporting at least: `max_platforms`, `max_external_users`, `max_monthly_messages`, `max_storage_mb`, `chat_enabled`, `user_search_enabled`, `presence_enabled`, `analytics_enabled`, `priority_support`.
  - Plans with historical subscriptions must **not be deleted**; they are **deactivated** instead.
- **Consequences:**
  - Adds `plan_features` (and future-code niceties) to the database design.
  - `plans.is_active` controls visibility; deletion is prohibited where subscriptions reference the plan.

## ADR-013: Integration API Authentication with PASETO v4.local (Token Service + Lifecycle)

- **Status:** Accepted (approved — resolves Phase 3A §20 item 1; supersedes the "recommended" line in D-5 for platform-to-MyVivahAI API auth)
- **Context:** external matrimony platforms call MyVivahAI APIs server-to-server. The API auth layer needs a cryptographically sound, symmetric, short-lived token with built-in revocation — and a version shared lock so every integration issues/validates the same token format.
- **Decision:** Implement integration API authentication with **PASETO `v4.local`** (AEAD XChaCha20-Poly1305, symmetric) as implemented by `paragonie/paseto`, per `docs/phase-3a-api-integration-plan.md` §6:
  - A **single locked version** `v4.local` for all integrations (`PlatformIntegration::PASETO_V4_LOCAL`); no per-platform algorithm negotiation.
  - Claims: `aud` (platform slug), `platform_id` (platform ULID), `sub` (integration public_id), `kid` (API-key fingerprint), `iat`, `exp` (exp−iat ≤ 1h), `jti` (unique), `scope` (list).
  - **Symmetric key at rest:** each API key's raw material is stored **encrypted** via Laravel `Crypt` (AES-256-CBC under `APP_KEY`); a SHA-256 **fingerprint** identifies the key non-secretly in the token `kid`.
  - **Issue:** `POST /api/v1/auth/token` exchanges `client_id` (platform `public_id`) + `client_secret` (base64url API key) for a PASETO. Scopes are **always derived from T8 `platform_service_access` entitlements**, never client-supplied; the `authentication` scope is always included.
  - **Validate:** `ValidatePlatformToken` middleware decodes with the key resolved by `kid`, re-checks expiry, key `revoked_at`, integration `revoked_at`/status, platform status, JTI blacklist (cache), and that the token fingerprint matches the presented key (`hash_equals`).
  - **Revoke:** `POST /api/v1/auth/revoke` blacklists the token `jti`; key/integration revocation is immediate via DB state.
  - **Rotation forwards to Phase 3B-3/ApiKeyService** (primary/backup + grace window), not built in 3B-1.
- **Consequences:**
  - No JWT algorithm-confusion surface; constant-time parsing.
  - `config/paseto.php` is the single source of truth for TTL caps, the authentication scope name, and `service_scopes` per service.
  - Widget/user **identity** tokens remain a separate concern (ADR-006) — this ADR covers integration/client-secret auth only.

| ID | Topic | Status | Blocking? |
|---|---|---|---|
| D-1 | Shared schema vs per-schema/isolation | **Resolved** — shared MySQL + `platform_id` (ADR-003) | Blocks migrations (resolved) |
| D-2 | Key strategy | **Resolved** — BIGINT PK + ULID public ids | Blocks migrations (resolved) |
| D-3 | Broadcast server | **Resolved** — Laravel Reverb (ADR-010) | Blocks chat implementation (resolved) |
| D-4 | Dashboard UI | Open — Blade+Alpine vs Livewire | Blocks dashboard development |
| D-5 | Identity token standard | **Resolved** — PASETO v4.local, ~5 min (ADR-006, widget/identity); **integration API auth also PASETO v4.local + key lifecycle (ADR-013, 3B-1)** | Blocks token service (resolved) |
| D-6 | Unread strategy | Open — Incremental + nightly recompute | Blocks message model |
| D-7 | Entitlement strategy | Open — `platform_service_access` cache + events | Blocks subscriptions |
| D-8 | Presence in MVP | Open — Included (Redis TTL) | Blocks chat scope |
| D-9 | Widget isolation | Open — Shadow DOM | Blocks widget build |
| D-10 | Test/live widget mode | Open — Server-side resolution, one embed URL | Blocks widget config |
| D-11 | Purchase workflow | **Resolved** — Manual admin-approved (ADR-011); gateway TBD/deferred | Blocks billing (partially resolved) |
| D-12 | Message retention | Open — TBD | Blocks data lifecycle |
| D-13 | Dynamic plans | **Resolved** — Admin-managed, demo data, deactivate-not-delete (ADR-012) | Blocks plans (resolved) |
| D-14 | Payment/Billing interval | Open — manual gateway-free MVP; interval/currency per plan | Blocks subscriptions (partial) |

---

## Decision Workflow

1. Any open decision above must be resolved (or superseded) in **known-issues.md**.
2. Finalized decisions are moved into this file with a full ADR entry or updated status.
3. Each ADR requiring code changes blocks its related Phase in current-tasks.md until resolved.