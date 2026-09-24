# Phase 2D — Factories & Seeders (Demo Data)

**Status:** ✅ Complete + exit-verified (2026-09-18)
**Scope gate:** Phase 2D only (factories + seeders for the 8 Core Platform tables T1–T8). No auth, no registration, no admin UI, no subscription workflows, no API integration — Phase 3 blocked pending separate approval (AGENTS.md phase gating).

---

## 1. Overview

Phase 2D authored the **development-only** seed data layer for MyVivahAI's Core Platform domain, 1:1 with the authoritative Phase 2A schema plan (`docs/phase-2a-schema-plan.md`), the Phase 2B migrations, and the Phase 2C Eloquent models.

Two deliverable groups:

| Group | Count | Location |
|---|---|---|
| Eloquent factories | 8 | `database/factories/` |
| Seeders (incl. root orchestrator) | 5 | `database/seeders/` |

---

## 2. Factories Created (8)

| Factory | Model | Target Table | Notes |
|---|---|---|---|
| `UserFactory` | `App\Models\User` | `users` (T1) | Phase 2A column set; ULID `public_id` auto (HasUlids); `status`/`timezone`/`locale`, verified, soft-delete aware |
| `PlatformFactory` | `App\Models\Platform` | `platforms` (T2) | `created_by` → resolved demo owner; unique slug; `status`; `active()` state |
| `PlatformAdminFactory` | `App\Models\PlatformAdmin` | `platform_admins` (T3) | `platform_id`/`user_id` pair (UNIQUE); role enum; `owner()` state |
| `ServiceFactory` | `App\Models\Service` | `services` (T4) | Global catalog (ADR-012); unique `key`; internal, no ULID |
| `PlanFactory` | `App\Models\Plan` | `plans` (T5) | ULID `public_id`; `service_id` FK; price/currency/billing_period; features JSON; UNIQUE(service_id, plan_key); `active()` state |
| `SubscriptionFactory` | `App\Models\Subscription` | `subscriptions` (T6) | ULID `public_id`; platform/service/plan FKs; status enum; no soft delete |
| `PaymentFactory` | `App\Models\Payment` | `payments` (T7) | subscription/platform FKs; amount/currency; status enum; unique gateway txn; no soft delete (financial) |
| `PlatformServiceAccessFactory` | `App\Models\PlatformServiceAccess` | `platform_service_access` (T8) | platform/service pair (UNIQUE); `has_access` bool; no soft delete |

All 8 implement `HasFactory` and are PSR-4 autoloadable (`App\Models\*::factory()`).

---

## 3. Seeders Created (5)

| Seeder | Purpose | Idempotency key |
|---|---|---|
| `DatabaseSeeder` | Root orchestrator — calls children in FK dependency order | — |
| `DemoUserSeeder` | Demo users (T1): demo owner + 2 demo users, `example.test` only | unique `email` |
| `ServiceSeeder` | Global service catalog (T4): seeds the `realtime_chat` service (ADR-012) | unique `key` |
| `PlanSeeder` | Demo plans (T5) for the seeded service | UNIQUE(service_id, plan_key) |
| `DemoPlatformSeeder` | Demo platform (T2) owned by demo owner + T3 platform_admin row | unique `slug` + UNIQUE(platform_id, user_id) |

### Seed Order (FK dependency)

```text
1. DemoUserSeeder      (T1 users — no upstream FK)
2. ServiceSeeder       (T4 services — global catalog)
3. PlanSeeder          (T5 plans — depends on services)
4. DemoPlatformSeeder  (T2 platforms + T3 platform_admins — depends on users)
```

`DatabaseSeeder::run()` wires exactly this order.

---

## 4. Idempotency Strategy

- **Factories** are never run directly during `db:seed`; they exist for `App\Models\*::factory()` (tests + future dev use).
- **Seeders** use `updateOrCreate` on natural unique keys, so re-running `php artisan db:seed` never creates duplicates:
  - DemoUserSeeder → `updateOrCreate` on `email`
  - ServiceSeeder → `updateOrCreate` on `key`
  - PlanSeeder → `updateOrCreate` on `['service_id', 'plan_key']`
  - DemoPlatformSeeder → `updateOrCreate` on `slug`; platform_admin on `['platform_id', 'user_id']`

**Proof (two consecutive runs, same local `myvivah` DB):**

| Table | After run 1 | After run 2 | Delta |
|---|---|---|---|
| `users` | 3 | 3 | 0 |
| `services` | 1 | 1 | 0 |
| `plans` | 3 | 3 | 0 |
| `platforms` | 1 | 1 | 0 |
| `platform_admins` | 1 | 1 | 0 |

**`migrate:fresh --seed` proof (clean slate → full repro):** after `migrate:fresh --seed` the same 5 tables hold exactly 3/1/3/1/1 rows with zero FK orphans (all cross-table FK probes returned 0).

---

## 5. Demo Data Policy

- **Fake only.** All emails use the `example.test` reserved TLD (e.g. `owner@example.test`, `alpha@example.test`, `beta@example.test`) per `docs/database.md` demo rules.
- **No real PII.** No real names, no real phone numbers, no real payment credentials.
- **Passwords:** seeded via the shared demo hash against `password` convention (dev-only, never production).
- **No sensitive data.** No gateway keys, no production tokens. `payments`/`subscriptions` rows are intentionally **not** seeded with real gateway data (ADR-011 manual billing at MVP).
- **Scoped by demo platform** per `docs/platform-isolation.md` — platform-owned rows reference the single demo platform only.

---

## 6. Validation & Exit Commands (actual results)

| Check | Command | Result |
|---|---|---|
| Syntax (all factories/seeders) | `php -l` sweep | ✅ 8/8 + 5/5 clean, "No syntax errors detected" |
| Style | `vendor/bin/pint --test` | ✅ 21 files PASS (after `pint` fix, 0 remaining) |
| Test suite | `php artisan test` / `vendor/bin/phpunit` | ✅ 2 tests, 2 assertions, OK |
| Seed run 1 | `php artisan db:seed` | ✅ ALL run to DONE |
| Seed run 2 (idempotency) | `php artisan db:seed` | ✅ no duplicates (counts unchanged) |
| Clean rebuild | `migrate:fresh --seed` | ✅ fresh schema + same seed result |
| Static analysis | `vendor/bin/phpstan` | ⚠️ **Not runnable — see §7** |

---

## 7. Limitations (honest report)

- **Composer is unavailable in this environment** (`composer.phar`/`composer` not resolvable through the reliable php.exe probe channel). Consequences:
  - `composer dump-autoload` **cannot be run**. PSR-4 correctness was verified by direct `class_exists`/autoload probes instead.
  - **PHPStan cannot complete analysis** — the configured `phpstan.neon` requires the Larastan extension, which has not been installed (vendor requires Composer). Attempting to run it fails at bootstrap with:
    `Missing parameter 'cache.resolvedPhpDocBlockCacheCountMax'` (Larastan/Mockery bootstrap not satisfiable without Composer install).
  - **No fabricated results.** Where a tool could not run, that is stated explicitly. Nothing is marked verified unless the command actually succeeded.
- **MySQL flavor note:** the local server responds as MariaDB 10.4.32 (XAMPP); the project targets MySQL semantics — no migration/seed incompatibility observed.

---

## 8. Deliverable Audit

- ✅ 8 factories authored, 1:1 with Phase 2B migrations & Phase 2C models
- ✅ 5 seeders authored, idempotent, ordered by FK dependency
- ✅ `DatabaseSeeder` wires all children correctly
- ✅ Two-run + fresh-seed idempotency proven on local dev DB
- ✅ All seeded values are fake/`example.test` demo data only
- ✅ Pint clean; test suite green; `php -l` clean
- ⚠️ PHPStan not runnable (Composer/Larastan unavailable) — documented, not faked

---

## 9. Files Changed (this phase)

- `database/factories/` — 8 files (User, Platform, PlatformAdmin, Service, Plan, Subscription, Payment, PlatformServiceAccess)
- `database/seeders/` — 5 files (DatabaseSeeder, DemoUserSeeder, ServiceSeeder, PlanSeeder, DemoPlatformSeeder)
- `docs/phase-2d-factories-seeders.md` — this document
- `docs/README.md`, `docs/current-tasks.md`, `docs/changelog.md` — Phase 2D status/index/changelog updates

**Phase 2D exit: PASS.** Stopping here per AGENTS.md phase gating — Phase 3 (auth/registration/admin UI/subscription flows/API integration) requires separate approval.
