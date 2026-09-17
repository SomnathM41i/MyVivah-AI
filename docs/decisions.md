# MyVivahAI — Architecture Decision Records (ADR)

> Each record: **Status** (Accepted / Recommended / Proposed / Superseded). Items marked "Recommended" or "Proposed" are **not** finalized — see known-issues.md for the open-decision list. Items marked "Accepted" apply to the current project as of this documentation phase.

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

- **Status:** Accepted (product requirement), with **Recommended** starter approach not yet finalized
- **Context:** Multiple independent external platforms subscribe to MyVivahAI services. Data belonging to one platform must never be accessible by another.
- **Decision:** Use product-facing terminology "multi-platform." Internally implement platform-scoped isolation.
- **Recommended starter approach (open):** Shared MySQL database + shared schema, with a mandatory `platform_id` on every platform-bound table, enforced via a `PlatformContext` middleware + service-layer scoping. Alternatives (per-platform schemas, per-platform databases) documented as options in platform-isolation.md.
- **Consequences:**
  - Every platform-bound query is filtered by platform.
  - Certified isolation test suite required.
  - Config/credentials are per-platform.

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

- **Status:** Recommended (option chosen subject to finalization)
- **Context:** The browser is attacker-controlled; a raw external user ID cannot be trusted. The client platform's backend authenticates its own session.
- **Decision:** The client platform's backend issues a short-lived signed token (claims: `platform` public id, `external_user_id`, `jti`, `iat`, `exp`; expiry 5–15 min) that the widget sends to MyVivahAI. MyVivahAI verifies the signature against the platform's registered secret, expiry, and `jti` replay guard, then resolves the external user within that platform.
- **Recommended standard (open decision):** PASETO v4.local or JWT (HS256). See known-issues.md #3.
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

## Recommended-but-not-finalized (tracked decisions)

| ID | Topic | Recommendation | Blocking? |
|---|---|---|---|
| D-1 | Shared schema vs per-schema/isolation | Shared schema + platform_id | Blocks migrations |
| D-2 | Key strategy | BIGINT PK + ULID public ids | Blocks migrations |
| D-3 | Broadcast server | Laravel Reverb (self-host) vs Pusher | Blocks chat implementation |
| D-4 | Dashboard UI | Blade+Alpine or Livewire | Blocks dashboard development |
| D-5 | Identity token standard | PASETO v4.local (recommended) | Blocks token service |
| D-6 | Unread strategy | Incremental + nightly recompute | Blocks message model |
| D-7 | Entitlement strategy | `platform_service_access` cache table + events | Blocks subscriptions |
| D-8 | Presence in MVP | Included (Redis TTL) | Blocks chat scope |
| D-9 | Widget isolation | Shadow DOM | Blocks widget build |
| D-10 | Test/live widget mode | Server-side resolution, one embed URL | Blocks widget config |
| D-11 | Payment gateway | TBD (India-focused: Razorpay/Stripe) | Blocks billing |
| D-12 | Message retention | TBD | Blocks data lifecycle |

---

## Decision Workflow

1. Any open decision above must be resolved (or superseded) in **known-issues.md**.
2. Finalized decisions are moved into this file with a full ADR entry or updated status.
3. Each ADR requiring code changes blocks its related Phase in current-tasks.md until resolved.