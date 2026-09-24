# Phase 2A — Core Platform Domain Schema Plan

> **Status:** PLAN ONLY — documentation deliverable. No migrations, models, factories, or seeders are created by this document.
> **Gate:** Migrations must **not** be written until this plan is reviewed and approved (AGENTS.md phase-gating; task rule "ask for approval before any migrations/models").
> **Sources (1:1):** `docs/database.md`, `docs/decisions.md`, `docs/security.md`, `docs/platform-isolation.md`, `docs/architecture.md`, `docs/laravel-architecture.md`, `docs/AGENTS.md`, `docs/current-tasks.md`, `docs/known-issues.md`, `docs/changelog.md`.

---

## 1. Purpose & Scope

Phase 2A produces the authoritative database schema for the **Core Platform Domain** — the tables that Phase 2B migrations will implement. It is the contract that Phase 2B implements 1:1 with no deviation.

### In scope (Phase 2B migrations)

| # | Table | Module |
|---|---|---|
| T1 | `users` (+ Phase 2A columns) | Core Platform / Auth |
| T2 | `platforms` | Core Platform |
| T3 | `platform_admins` | Core Platform |
| T4 | `services` | Service & Subscription |
| T5 | `plans` | Service & Subscription |
| T6 | `subscriptions` | Service & Subscription |
| T7 | `payments` | Payment & Billing |
| T8 | `platform_service_access` | Service & Subscription (entitlement cache) |

### Documented but staged to later phases (NOT Phase 2B migrations)

- **Integration Domain** (Phase 3): `platform_integrations`, `api_endpoint_configs`, `api_credentials`, `api_test_logs`, `webhook_configs`.
- **Real-Time Chat Domain** (Phase 5): `external_users`, `conversations`, `conversation_participants`, `messages`, `message_statuses`, `message_attachments`.
- **Widget** (Phase 4): `widget_configs`.

These are described conceptually in `docs/database.md`. They are listed here only as a boundary statement so the Core Platform schema is not over-built or prematurely coupled.

### Explicitly NOT in scope (future domains — do not create, AGENTS.md §17/§23)

`whatsapp_integrations`, `ai_agents`, `ai_conversations`, `ai_usage_tracking`, `data_entry_jobs`, `message_attachments` (until chat domain), `widget_configs` (until Phase 4). No future-domain table is created now.

---

## 2. Schema Conventions (binding for every table)

| Rule | Value | Source |
|---|---|---|
| Primary key | `id` BIGINT UNSIGNED AUTO_INCREMENT | database.md §Conventions, AGENTS.md §20 |
| Public identifier | `public_id` CHAR(26) (ULID), separate from internal PK | ADR-002/D-2, decisions.md, laravel-architecture.md |
| Engine | InnoDB | database.md, ADR-002 |
| Charset | `utf8mb4` | bootstrap.md, database.md |
| Collation | `utf8mb4_unicode_ci` | bootstrap.md |
| DRIVER DB | `utf8mb4_unicode_ci` set explicitly per migration | Phase-1 bootstrap (myvivah DB created utf8mb4/utf8mb4_unicode_ci) |
| Foreign key column | `{singular_table}_id` (snake_case) | AGENTS.md §20, database.md |
| Timestamps | `created_at`/`updated_at` TIMESTAMP NULL on all tables | database.md |
| Soft delete | `deleted_at` TIMESTAMP NULL where noted | database.md §Soft-delete matrix |
| Booleans | `is_`/`has_`/`can_` prefix, `DEFAULT false` | AGENTS.md §20, database.md |
| ENUM over VARCHAR | Use string ENUM only where database.md specifies; prefer VARCHAR+application validation otherwise | database.md |
| Platform scoping | Every platform-owned row carries `platform_id` FK; no cross-platform FK paths | platform-isolation.md |

---

## 3. Canonical Schema

### T1. `users` — MyVivahAI internal accounts (staff + platform owners/admins)

Base import from Laravel scaffold `0001_01_01_000000_create_users_table` + Phase 2A additions.

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT PK | |
| public_id | CHAR(26) | NO | — | ULID. Public account ID (public_id coverage, §6) |
| name | VARCHAR(255) | NO | — | |
| email | VARCHAR(255) | NO | — | UNIQUE |
| email_verified_at | TIMESTAMP | YES | NULL | |
| phone | VARCHAR(32) | YES | NULL | UNIQUE nullable |
| password | VARCHAR(255) | NO | — | Bcrypt via `Hash::make` |
| status | ENUM('active','suspended','deactivated') | NO | `active` | |
| timezone | VARCHAR(64) | NO | `'UTC'` | |
| locale | VARCHAR(16) | NO | `'en'` | |
| last_login_at | TIMESTAMP | YES | NULL | |
| remember_token | VARCHAR(100) | YES | NULL | Scaffold |
| created_at / updated_at | TIMESTAMP | YES | NULL | |
| deleted_at | TIMESTAMP | YES | NULL | Soft delete |

**Indexes:** UNIQUE `email`; UNIQUE `phone`; INDEX `status`.
**Soft delete:** yes (admin-managed accounts, database.md).

---

### T2. `platforms` — external client business (the tenant)

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT PK | |
| public_id | CHAR(26) | NO | — | ULID; the platform key used in widget config & client URLs |
| name | VARCHAR(255) | NO | — | |
| slug | VARCHAR(255) | NO | — | UNIQUE, URL-safe |
| website_url | VARCHAR(2048) | YES | NULL | |
| description | TEXT | YES | NULL | |
| status | ENUM('pending','active','suspended','deactivated') | NO | `pending` | |
| created_by | BIGINT UNSIGNED | NO | — | FK → users.id (owner) |
| created_at / updated_at | TIMESTAMP | YES | NULL | |
| deleted_at | TIMESTAMP | YES | NULL | Soft delete |

**Indexes:** UNIQUE `public_id`; UNIQUE `slug`; INDEX `created_by`; INDEX `status`.
**Soft delete:** yes.
**Isolation:** root of all platform-scoped rows; every business table below carries `platform_id`.

---

### T3. `platform_admins` — platform ↔ user role assignments (multi-owner + admin)

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT PK | |
| platform_id | BIGINT UNSIGNED | NO | — | FK → platforms.id |
| user_id | BIGINT UNSIGNED | NO | — | FK → users.id |
| role | ENUM('owner','admin','developer') | NO | `admin` | |
| invited_at | TIMESTAMP | YES | NULL | |
| accepted_at | TIMESTAMP | YES | NULL | |
| created_at / updated_at | TIMESTAMP | YES | NULL | |

**Indexes:** UNIQUE (platform_id, user_id); INDEX (user_id); INDEX (platform_id).
**Soft delete:** no.
**Note:** exactly one `owner` per platform. MySQL 8.0.22 does **not** support partial unique indexes via migration API, so single-owner enforcement is in the service layer (`PlatformAdminService`) with a `is_owner` check + transaction; documented in `docs/database.md` as enforced at app level (open decisions).

---

### T4. `services` — MyVivahAI product catalog (first: Real-Time Chat)

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT PK | |
| key | VARCHAR(50) | NO | — | UNIQUE e.g. `realtime_chat` |
| name | VARCHAR(255) | NO | — | |
| description | TEXT | YES | NULL | |
| is_active | BOOLEAN | NO | `false` | |
| sort_order | INT | NO | `0` | |
| created_at / updated_at | TIMESTAMP | YES | NULL | |
| deleted_at | TIMESTAMP | YES | NULL | Soft delete |

**Indexes:** UNIQUE `key`; INDEX `is_active`.
**Soft delete:** yes (deactivate-not-delete, ADR-012).
**Seed (Phase 2B):** `realtime_chat` — Real-Time Chat (only active service).

---

### T5. `plans` — service-specific plans (dynamic catalog, ADR-012)

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT PK | |
| public_id | CHAR(26) | NO | — | ULID (public_id coverage §6) |
| service_id | BIGINT UNSIGNED | NO | — | FK → services.id |
| plan_key | VARCHAR(50) | NO | — | e.g. `starter` |
| name | VARCHAR(255) | NO | — | |
| price | DECIMAL(12,2) | NO | `0.00` | |
| currency | CHAR(3) | NO | `'INR'` | |
| billing_period | ENUM('monthly','yearly','custom') | NO | `monthly` | |
| billing_interval | INT | YES | NULL | for custom periods |
| is_active | BOOLEAN | NO | `false` | |
| features | JSON | YES | NULL | feature flags/limits map |
| sort_order | INT | NO | `0` | |
| created_at / updated_at | TIMESTAMP | YES | NULL | |
| deleted_at | TIMESTAMP | YES | NULL | Soft delete (deactivate, ADR-012) |

**Indexes:** UNIQUE (service_id, plan_key); INDEX (service_id, is_active); INDEX `is_active`.
**Soft delete:** yes (deactivate-not-delete).
**Seed (Phase 2B, demo data — ADR-012):** Free Trial, Starter, Professional, Business plans for `realtime_chat`, placeholder pricing & INR.

---

### T6. `subscriptions` — platform ↔ service ↔ plan lifecycle

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT PK | |
| public_id | CHAR(26) | NO | — | ULID (public_id coverage §6) |
| platform_id | BIGINT UNSIGNED | NO | — | FK → platforms.id |
| service_id | BIGINT UNSIGNED | NO | — | FK → services.id |
| plan_id | BIGINT UNSIGNED | NO | — | FK → plans.id |
| status | ENUM('pending','active','expired','cancelled','suspended') | NO | `pending` | |
| starts_at | TIMESTAMP | NO | — | |
| ends_at | TIMESTAMP | YES | NULL | |
| cancelled_at | TIMESTAMP | YES | NULL | |
| next_billing_at | TIMESTAMP | YES | NULL | |
| auto_renew | BOOLEAN | NO | `false` | |
| created_at / updated_at | TIMESTAMP | YES | NULL | |

**Indexes:** UNIQUE (platform_id, service_id, status — at most one `active` per pair enforced in service layer, ADR/database.md §Open Decisions); INDEX (platform_id, status); INDEX (platform_id, service_id); INDEX `ends_at`; INDEX `status`.
**Soft delete:** no (status transitions instead, database.md).
**Isolation:** platform-scoped.

---

### T7. `payments` — payment records (manual MVP flow, ADR-011)

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT PK | |
| subscription_id | BIGINT UNSIGNED | NO | — | FK → subscriptions.id |
| platform_id | BIGINT UNSIGNED | NO | — | FK → platforms.id |
| gateway | VARCHAR(50) | YES | NULL | e.g. `manual`; no online gateway at MVP (ADR-011) |
| gateway_transaction_id | VARCHAR(255) | YES | NULL | UNIQUE |
| amount | DECIMAL(12,2) | NO | — | |
| currency | CHAR(3) | NO | `'INR'` | |
| status | ENUM('pending','completed','failed','refunded') | NO | `pending` | |
| paid_at | TIMESTAMP | YES | NULL | |
| metadata | JSON | YES | NULL | gateway payload / admin note |
| created_at / updated_at | TIMESTAMP | YES | NULL | |

**Indexes:** UNIQUE `gateway_transaction_id`; INDEX (subscription_id); INDEX (platform_id, status); INDEX (platform_id, paid_at).
**Soft delete:** no (financial records retained; no soft delete per database.md matrix).

---

### T8. `platform_service_access` — precomputed entitlement cache

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| id | BIGINT UNSIGNED | NO | AUTO_INCREMENT PK | |
| platform_id | BIGINT UNSIGNED | NO | — | FK → platforms.id |
| service_id | BIGINT UNSIGNED | NO | — | FK → services.id |
| has_access | BOOLEAN | NO | `false` | |
| effective_until | TIMESTAMP | YES | NULL | |
| synced_at | TIMESTAMP | NO | — | |
| created_at / updated_at | TIMESTAMP | YES | NULL | |

**Indexes:** UNIQUE (platform_id, service_id); INDEX (platform_id).
**Soft delete:** no.
**Purpose:** fast entitlement checks for `EntitlementService` / `EnsurePlatformAccess:chat` — not hardcoded checks (AGENTS.md §21). Maintained by event listeners on subscription status changes; open decision: cache table (recommended here) vs compute-on-demand (database.md §Open Decisions).

---

## 4. Laravel Scaffold Tables (framework-owned — NOT business migrations)

Created by the framework's default migrations in Phase 1 bootstrap; present in `myvivah` DB but outside Phase 2A business scope:

`password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`.

These are not part of the Phase 2A plan; do not modify or duplicate them.

---

## 5. Module → Table Mapping (Core Platform)

| Module | Tables |
|---|---|
| Core Platform / Auth | `users`, `platforms`, `platform_admins` |
| Service & Subscription | `services`, `plans`, `subscriptions`, `platform_service_access` |
| Payment & Billing | `payments` |

No chat, integration, widget, or future-AI tables are created by the Core Platform Domain.

---

## 6. Public Identifier (ULID) Coverage

Rules (database.md, decisions.md ADR-002): internal PKs are BIGINT auto-increment; public IDs are ULID `CHAR(26)` used wherever the identifier crosses a trust boundary (browser, widget, client URLs, WebSocket channels, client HTTP payloads).

| Table | Public column | Required because |
|---|---|---|
| `users` | `public_id` | Account-level public reference |
| `platforms` | `public_id` | **Platform key** used in widget embed + client URLs (never raw auto-increment) |
| `plans` | `public_id` | Public plan reference (plans are selectable by clients) |
| `subscriptions` | `public_id` | Public subscription reference |

`payments`, `platform_admins`, `platform_service_access`, `services` remain internal-only (not client-addressable primitives in MVP); ULID coverage for chat/integration tables will be defined in their phase plans, but the pattern (ULID for every browser/widget-facing entity) is fixed from Phase 2A onward. ULID generation: `\Illuminate\Support\Str::ulid()` / `HasUlids` trait (decisions.md ADR-002).

---

## 7. Default Booleans

Every boolean uses `is_`/`has_`/`can_` prefix with `DEFAULT false` (never `true`, never nullable):

| Table.Column | Default | Source |
|---|---|---|
| `platforms.status` | ENUM (not boolean) | — |
| `services.is_active` | `false` | |
| `plans.is_active` | `false` | |
| `subscriptions.auto_renew` | `false` | |
| `platform_service_access.has_access` | `false` | |

---

## 8. MySQL Edition, Driver, Charset, Collation, Engine

| Decision | Value | Source |
|---|---|---|
| Edition | MySQL 8.0.22 (XAMPP local) | bootstrap.md, `artisan about` (Server 8.0.22) |
| Connection driver | `mysql` (PDO) | `.env` DB_CONNECTION=mysql |
| Charset | `utf8mb4` | myvivah DB created with utf8mb4 |
| Collation | `utf8mb4_unicode_ci` | CRUD on columns |
| Engine | InnoDB | database.md / ADR-002 |

**Migration convention:** every business migration sets explicit engine `InnoDB`; charset/collation come from the MySQL connection default (`utf8mb4`/`utf8mb4_unicode_ci` on `myvivah`). Do not infer or override per-table unless a specific table requires otherwise; any exception must be documented here first.

---

## 9. Platform Isolation

- Every platform-owned table (`platforms`, `platform_admins`, `subscriptions`, `payments`, `platform_service_access`) is queried through a resolved platform context (middleware `PlatformContext` / `EnsurePlatformAccess`) — no table except `platforms`/`users` is ever queried without a platform scope (platform-isolation.md; laravel-architecture.md §Middleware).
- No cross-platform FK path exists between tenant tables.
- ULID public IDs prevent ID enumeration (security.md).
- Authorization is service-layer + middleware enforced, never a hardcoded `if (auth()->user()->platform_id === ...)` scatter.

---

## 10. Soft-Delete Matrix (Core Platform tables)

| Table | Soft delete | Rationale (database.md) |
|---|---|---|
| `users` | Yes | admin-managed accounts |
| `platforms` | Yes | |
| `platform_admins` | No | role history retained |
| `services` | Yes | deactivate-not-delete (ADR-012) |
| `plans` | Yes | deactivate-not-delete (ADR-012) |
| `subscriptions` | No | status transitions only |
| `payments` | No | financial records retained |

---

## 11. Phase 2B Readiness Gate — approval checklist

Schema plan approved means the following are confirmed and will be implemented 1:1 in Phase 2B:

- [ ] All 8 business tables (T1–T8) as specified, no additions/deviations
- [ ] `platform_id` isolation present on every platform-owned table; no cross-platform FKs
- [ ] ULID `public_id` on `users`, `platforms`, `plans`, `subscriptions`
- [ ] utf8mb4 / utf8mb4_unicode_ci / InnoDB explicit
- [ ] Booleans all `DEFAULT false`
- [ ] Soft-delete matrix honored
- [ ] `services` seeded with `realtime_chat`; `plans` seeded with demo data (ADR-012)
- [ ] No future-domain / chat / integration / widget tables created (staged per AGENTS.md)
- [ ] Single-owner-per-platform enforced in service layer (documented open decision)
- [ ] `platform_service_access` cache maintained via subscription events (open decision: cache over compute)

Open items carried forward (not blockers): Redis/Reverb activation (ADR-010), payment gateway choice (ADR-011, manual for MVP), plan feature JSON contract definition (Phase 2B EntitlementService).

> **Approval required before Phase 2B migration work begins.**
