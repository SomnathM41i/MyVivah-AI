# Phase 3A — API Integration Module Plan (API Contract + Platform Integration)

> **Status (2026-09-22):** PLAN APPROVED + **Phase 3B-1 auth foundation implemented** (models/`PasetoTokenService`/middleware/routes for `auth/token`, `auth/revoke`, `platform/me`; config, tests, PHPStan/Pint/MySQL verified — see `docs/current-tasks.md` and `docs/changelog.md`). Remaining §17 3B items (external-user map, API-key rotation, audit trail, identity/conversation endpoints) are NOT authored yet.
>
> **Canonical project path:** `/mnt/c/Users/Somnath Mali/Desktop/MyVivah-AI/` (real on-disk casing, AGENTS.md).
> **Stack truth:** Laravel 12.69.2 · PHP 8.2.12 (XAMPP `php.exe`) · MySQL/MariaDB `myvivah` · Pint Laravel preset · PHPUnit 31/102 green · **PHPStan level 5 green** (Composer + Larastan now installed).

---

## 1. Purpose

Define — at the **planning layer, 1:1 with the verified Phase 2B/2C/2D reality (T1–T8)** — how an external matrimony platform registers with MyVivahAI, configures an integration, and securely calls MyVivahAI APIs to power chat (and future services). This is the **Phase 3A contract**, the counterpart of `docs/phase-2a-schema-plan.md` for the API/Integration domain, mirroring the approved Phase 2A/2B/2C/2D conventions exactly (ULID `public_id` matrix, InnoDB, utf8mb4, platform isolation, soft-delete matrix, booleans, indexes).

The 3A plan is **approved and partially implemented** (Phase 3B-1). Sections that describe live code are annotated; the rest remains the implementation contract for later 3B work.

---

## 2. Terminology (from AGENTS.md — product-facing)

- **MyVivahAI** — the main SaaS platform.
- **Platform** — an external matrimony website/application that uses MyVivahAI (use "platform", **never** "tenant" in user-facing language).
- **Service** — a product MyVivahAI offers (first: Real-Time Chat).
- **Integration** — the configured connection between a Platform and MyVivahAI (API credentials, endpoints, field mapping).
- **External User** — a user of the external platform's own system.
- **Widget** — the embeddable chat interface the external platform installs.
- **API Credentials** — platform-scoped keys used to authenticate MyVivahAI API calls.

**Isolation language:** product docs and user-facing text say **multi-platform** / **platform-scoped** — never "multi-tenant".

---

## 3. Scope

### In scope (Phase 3A plan)

- Platform registration + integration configuration contract (data model + config surface).
- API authentication with **PASETO v4.local**.
- Token generation, validation, expiration, rotation, revocation.
- Platform-scoped authorization (entitlements via `platform_service_access`, middleware).
- External user ID mapping + synchronization contract.
- Proposed API endpoints + request/response contracts.
- API versioning.
- Validation + standardized error responses.
- Rate limiting.
- Audit logging.
- Security + cross-platform data isolation.
- Automated tests plan.
- Required models, migrations, services, middleware, requests, resources, routes (Phase 3B scope list, not yet authored).
- Documentation changes.
- Implementation order + phase exit criteria.
- **Open decisions requiring maintainer approval.**

### Out of scope (explicitly gated)

- ❌ No code, no controllers, no routes, no migrations, no models, no middleware, no requests, no resources are authored in Phase 3A.
- ❌ No modification of the admin panel or external client APIs yet.
- ❌ No registration-flow UI, no auth UI, no subscription/payment workflow UI (Phase 4+).
- ❌ No widget frontend code (Phase 5+).
- ❌ No per-platform separate databases at this stage (isolation via `platform_id` scoping + authorization — platform-isolation.md).
- ❌ No WhatsApp/AI-agent integrations (ADR-012 catalog is extensible; only the contract is planned).

---

## 4. Baseline Reality It Must Integrate With

Verified in prior phases (T1–T8, `docs/phase-2a-schema-plan.md` + migrations + models + factories/seeders):

| Table | ULID public_id | Soft-delete | Notes |
|---|---|---|---|
| `users` | ✅ | ✅ | T1; HasUlids, SoftDeletes, status/timezone/locale |
| `platforms` | ✅ | ✅ | T2; isolation root, slug unique, created_by |
| `platform_admins` | — | — | T3; UNIQUE(platform_id,user_id), role enum |
| `services` | — | ✅ | T4; global catalog (realtime_chat seeded, ADR-012) |
| `plans` | ✅ | ✅ | T5; UNIQUE(service_id,plan_key), ULID, INR, billing enum |
| `subscriptions` | ✅ | — | T6; status enum(pending,active,expired,cancelled,suspended), auto_renew |
| `payments` | — | — | T7; gateway manual (ADR-011), status enum, unique txn id |
| `platform_service_access` | — | — | T8; UNIQUE(platform_id,service_id), has_access, effective_until; **entitlement cache** |

The `platform_service_access` table (T8) is the **authorization root** for service APIs: a platform may only call a service API if `platform_service_access.has_access = true` for `(platform_id, service_id)`. This must be enforced at the middleware + service layer, never trusted from the client.

---

## 5. Platform Registration & Integration Configuration

### 5.1 Existing primitives (Phase 2, already real)

- `Platform` (T2) with unique `slug` = the platform identity.
- `PlatformAdmin` (T3) with `role` enum(owner,admin,developer) = who manages the integration.
- `platforms.status` enum(pending,active,suspended,deactivated) gates whether APIs may serve the platform.

### 5.2 New config surface (Phase 3B data model contract — NOT authored in 3A)

Planned tables (naming + shape proposed; final names to be confirmed by maintainer):

**`platform_integrations`** — one per platform (1:1), the integration root.
- `platform_id` FK → platforms (unique)
- `status` enum(pending,active,test_disabled) default `pending`
- `widget_enabled` bool default false
- `auth_mode` enum(paseto_v4local, paseto_v4public) — MVP: `paseto_v4local`
- `base_domain` string (platform's own origin for widget/CORS)
- `cors_allowed_origins` json (default `[]`)
- `rate_limit_per_minute` smallint unsigned default 60
- soft delete **no** (integration root is hard-scoped; disable via status)

**`platform_api_keys`** — PASETO local symmetric keys, per platform.
- `platform_id` FK, `public_key_id` (short, non-secret identifier used in token `kid`)
- `secret_encrypted` text (encrypted at rest; never plaintext in DB)
- `is_primary` bool default false (only one primary at a time)
- `created_at`, `revoked_at` datetime nullable
- `key_type` enum(paseto_v4local) 
- **Never expose raw secret via API — rotation returns a one-time value.**

**`platform_external_user_map`** — external→local user reference.
- `platform_id` FK, `external_user_id` string (external platform's ID), `local_public_id` nullable (MyVivahAI internal user ULID if we create one)
- `synced_at` datetime, `last_seen_at` datetime
- UNIQUE(platform_id, external_user_id)
- Holds as little as possible: mapping + timestamps only (external platform is source of truth; docs/realtime-chat.md data-minimization)

**`api_audit_logs`** — request/response audit trail (append-only).
- `platform_id` FK, `api_key_id` FK nullable, `endpoint` string, `method` string, `status_code`, `ip_hash` (hashed), `duration_ms`, `request_checksum` (redacted-body hash), `response_checksum`, `created_at`
- No soft delete; never store raw secrets/bodies with PII — store checksums + minimal metadata (security.md)

### 5.3 Configuration UX (Phase 4, planned — not built)

Dashboard → Platform Detail → Integration tab:
1. Integration status (Pending → Active)
2. API key generation + rotation (primary/backup)
3. PASETO token generation helper (dev)
4. Base domain + CORS allowlist
5. Rate-limit policy display
6. Audit log viewer (read-only)
7. Test-runner (Phase 4B) entry point

---

## 6. API Authentication — PASETO v4.local

### 6.1 Choice rationale (ADR to document in decisions.md)

- PASETO v4 is the current cryptographically-sound token spec; `.local` = **symmetric** AEAD (XChaCha20-Poly1305) with a shared secret — ideal for server-to-server API between a known platform and MyVivahAI.
- No algorithm confusion / `alg:none` attacks unlike naive JWT setups; no external signing-key lookup needed for symmetric use.
- PHP implementation: `paragonie/paseto` v4.local (Composer package). ⚠️ Composer is unavailable in this sandbox → **package install + runtime proof is a Phase 3B first-step and must be verified with a real test, not assumed.**

### 6.2 Claims (PASETO payload)

```json
{
  "aud": "platform:{platform_slug}",
  "sub": "{platform_id_ulid}",
  "kid": "{api_key_id}",
  "iat": 1720000000,
  "exp": 1720003600,
  "jti": "{uuid}",
  "scope": ["realtime_chat:read", "realtime_chat:write"],
  "platform_id": "{platform_id_ulid}"
}
```

- `iat`/`exp`: exp − iat ≤ 3600s (1h, MVP).
- `jti`: unique per token; used for revocation ledger.
- `scope`: service-level scopes derived from `platform_service_access` at issue time.
- `platform_id` claim must **match** the platform of the presenting integration + key (never trust a client-supplied platform).

### 6.3 Lifecycle

- **Issue (generation):** on integration activation or key rotation → PASETO signed with the platform's primary symmetric key. Secret only readable by platform admin during key creation.
- **Validation (per request):** middleware — decode+verify signature with `kid` lookup, check `exp`, check `aud`/`sub` match route platform, check `scope` against endpoint requirement, check `jti` not revoked.
- **Expiration:** `exp` enforced server-side; short TTL (≤1h). No long-lived tokens at MVP.
- **Rotation:** generate new key → mark `is_primary` → keep previous as secondary (grace) → revoke old after grace (configurable, default 24h) — zero-downtime.
- **Revocation:** `jti` blacklist (cache/table) + disable integration (`status`) + revoke all keys (`revoked_at`). Revocation takes effect immediately.

### 6.4 Transport

- Header `Authorization: Bearer <paseto>` only. **Never** API keys in query strings.
- TLS required in production (no HTTP).
- CORS restricted to `base_domain` allowlist.

---

## 7. Platform-Scoped Authorization

- **Middleware chain (Phase 3B, planned):** `EnsurePlatformAccess` (exists pattern from Phase 2C entitlement design) + new `ValidateApiToken` + `RateLimitApi` + `LogApiAudit`.
- Every service API route must resolve the platform **from the verified token**, then check `platform_service_access` for `(platform_id, service_id)` → `has_access = true` AND `effective_until ≥ now` AND `platforms.status = active`.
- Entitlement failures return 403 with a stable error code (see §11).
- **Never trust request-body `platform_id`/`user_id`** from the client for authorization — always derive from the token (security.md rule "Never trust raw user IDs from the browser").

---

## 8. External User ID Mapping & Synchronization

- External platform is the **source of truth** for its user data (AGENTS.md / realtime-chat.md).
- MyVivahAI stores only: `platform_external_user_map` (platform_id, external_user_id, optional local public_id, sync timestamps).
- Identity for widget/chat: external platform passes a **signed PASETO identity token** (not a raw user ID). MyVivahAI validates signature, extracts `external_user_id`, stores/updates the map, and returns a short-lived MyVivahAI chat identity. (Flow in widget-integration.md §4.)
- Sync: on-demand (on first contact / identity verification) — **no bulk copy** of the external user database. Periodic reconciliation (unchanged records skipped) is future.

---

## 9. Proposed API Endpoints (Versioned `v1`)

All under `/api/v1` prefix. All require `Authorization: Bearer <paseto>` unless noted. Envelope + errors per §10/§11.

### Platform-scoped (external matrimony platform → MyVivahAI)

| Method | Path | Purpose | Auth scope |
|---|---|---|---|
| POST | `/api/v1/auth/token` | Exchange integration → PASETO (dev/refresh) | client_id+secret (short-lived) |
| POST | `/api/v1/auth/revoke` | Revoke current token (`jti`) | any |
| GET | `/api/v1/platform/me` | Integration + entitlements self-describe | authentication |
| GET | `/api/v1/users/{external_user_id}` | Fetch MyVivahAI-held identity data for external user | realtime_chat:read |
| POST | `/api/v1/users/verify` | Validate signed external-user identity token → local ref | realtime_chat:write |
| POST | `/api/v1/chat/permission` | Check two external users may chat (per realtime-chat.md §) | realtime_chat:read |
| GET | `/api/v1/chat/conversations` | List conversations (platform-scoped) | realtime_chat:read |
| GET | `/api/v1/chat/conversations/{conversation_public_id}` | Single conversation + participants | realtime_chat:read |
| POST | `/api/v1/chat/conversations` | Create conversation | realtime_chat:write |
| GET | `/api/v1/chat/conversations/{id}/messages` | Message history (paginated, platform-scoped) | realtime_chat:read |
| POST | `/api/v1/chat/conversations/{id}/messages` | Send message | realtime_chat:write |
| GET | `/api/v1/health` | Liveness + service availability (public, no auth) | — |

> These are **proposed contracts for approval**; exact fields are finalized with the maintainer during Phase 3B. No routes are registered in 3A.

### Example contract — POST /api/v1/chat/permission (conceptual, per realtime-chat.md §7)

Request:
```json
{
  "conversation": {
    "participant_external_ids": ["4582", "9012"]
  }
}
```
Response 200:
```json
{
  "success": true,
  "data": { "allowed": true, "reason": "both_active_subscribers" }
}
```

---

## 10. API Versioning

- Path prefix `/api/v1/...` (clean, cacheable, discoverable).
- Keep `v1` additive-only; breaking changes → new version (`v2`), never inline mutation of a live version.
- Rate-limit + audit scopes per version.
- Document version support policy in `docs/api-contract.md` during Phase 3B.

---

## 11. Validation & Standardized Error Responses

Consistent envelope:

```json
{
  "success": false,
  "error": {
    "code": "RATE_LIMITED",
    "status": 429,
    "message": "Too many requests.",
    "request_id": "{uuid}",
    "retry_after_seconds": 45
  }
}
```

Stable error codes (candidate set, to be finalized):

| HTTP | code | meaning |
|---|---|---|
| 400 | `VALIDATION_FAILED` | field errors (list in `errors[]`) |
| 401 | `INVALID_TOKEN` / `TOKEN_EXPIRED` / `TOKEN_REVOKED` | auth failure |
| 403 | `PLATFORM_SUSPENDED` / `SERVICE_NO_ACCESS` / `PLATFORM_MISMATCH` | authorization |
| 404 | `NOT_FOUND` | resource missing (platform-scoped) |
| 422 | `UNPROCESSABLE_ENTITY` | semantic business-rule failure |
| 429 | `RATE_LIMITED` | throttle hit |
| 500 | `INTERNAL_ERROR` | unexpected (no internals leaked) |

- Every validation uses **Form Requests** (Laravel convention, AGENTS.md §20).
- `request_id` correlated with audit log for support; never leak exception internals.

---

## 12. Rate Limiting

- Per-platform limiter keyed by `platform:{id}` (from token, not client-provided).
- Default `rate_limit_per_minute` (config surface §5.2) = 60/min MVP; configurable per platform.
- Burst penalty + per-endpoint sensitivity (chat send stricter than reads).
- 429 with `retry_after` + `Request-ID` header (standardized §11).
- Uses Laravel `RateLimiter` (cache-backed; Redis absent → database/cache driver per ADR-010, no Redis assumption).

---

## 13. Audit Logging

- Every authenticated API call → `api_audit_logs` row (append-only, no soft delete).
- Redaction: request/response **checksums only** + metadata; raw bodies/PII/secrets never stored (security.md).
- Server-side request hash lets support reproduce without storing secrets.
- Audit log UI (Phase 4) read-only with platform filter.

---

## 14. Security & Cross-Platform Data Isolation

- **Isolation:** every query is `platform_id`-scoped from the verified token; platform isolation enforced at model/middleware layer (platform-isolation.md). No cross-platform data access, ever.
- **Keys:** PASETO secrets encrypted at rest; only the primary key active; rotation + revocation immediate (§6).
- **Identity:** never trust client PID; validate signed identity tokens (§8).
- **Widget:** tokenized bootstrap, no credentials in browser, CORS allowlisted (widget docs).
- **No secrets in logs/UI after creation** (one-time display only).
- **Rate limiting + throttling on auth endpoints** to slow brute force.
- Threat-model section in `docs/security.md` updated during Phase 3B.

---

## 15. Automated Tests (Phase 3B — partially run)

- ✅ **Auth foundation (3B-1) landed:** `tests/Unit/PasetoTokenServiceTest.php` (claims/round-trip/expired/malformed/tampered/unprovisioned-key/TTL cap/revoked-key/integration/platform/scopes/jti-blacklist) + `tests/Feature/AuthTokenLifecycleTest.php` (issue ok, unknown client, wrong secret, missing creds 422, suspended, entitlement scopes, `platform/me`, expired/tampered/wrong-key/mismatched-platform, revoked jti, revoke endpoint, revoked key, scope binding, ungranted scope 403, `request_id` envelope) — **31 tests / 102 assertions, ran both phpunit and `artisan test`**.
- Pending (later 3B): platform-scoped isolation across services, CORS, rate-limit 429 on `paseto.issue`, audit rows w/ redaction, external-user map idempotency.
- Baseline preserved: PHPUnit 31/102 · Pint Laravel preset clean (71 files) · `php -l` clean · **PHPStan level 5 = 0 errors**.

---

## 16. Required Phase 3B Artifacts (list — ✓ = authored in 3B-1)

- **Models:** ✓ `PlatformIntegration`, ✓ `PlatformApiKey`; pending `PlatformExternalUser`, `ApiAuditLog` (+ HasFactory).
- **Migrations:** ✓`platform_integrations` + ✓`platform_api_keys` (schema finalized in place, pre-release); pending `platform_external_user_map`, `api_audit_logs`, plus the §5.2 dashboard-domain tables.
- **Services:** ✓ `PasetoTokenService`; pending `ApiKeyService` (rotation), `ApiEntitlementService` (wraps T8 — currently inline in `PasetoTokenService::scopesFor` + middleware), `ApiAuditService`, `RateLimitApi`.
- **Middleware:** ✓ `ValidatePlatformToken`, ✓ `EnsurePlatformAccess`; pending `LogApiAudit`, `RateLimitApi`.
- **Requests:** ✓ `IssueTokenRequest`; pending per-endpoint Form Requests.
- **Resources:** pending `PlatformResource`, `UserIdentityResource`, `ConversationResource`, `MessageResource` (controllers currently return arrays; error envelope via `ApiException` renderer).
- **Routes:** ✓ `routes/api.php` v1 group (`auth/token`, `auth/revoke`, `platform/me`) with `throttle:paseto.issue`; pending remaining §9 endpoints.
- **Tests:** ✓ `tests/Unit/PasetoTokenServiceTest.php` + `tests/Feature/AuthTokenLifecycleTest.php`.
- **Config:** ✓ `config/paseto.php`; pending `config/api.php` (rate/TTL defaults).

---

## 17. Implementation Order — Phase 3B ONLY

```text
3B-0  Install/verify paragonie/paseto (Composer) — gate: runtime unit green        ✅ DONE (3B-1)
3B-1  platform_integrations + platform_api_keys migrations, models, factories;
      PasetoTokenService/core; middleware; auth/token, auth/revoke, platform/me;   ✅ DONE (3B-1)
      config, tests, PHPStan/Pint/MySQL verified
3B-2  platform_external_user_map + api_audit_logs migrations + models              ⏳ NEXT
3B-3  ApiKeyService rotation (primary/backup + grace) + ApiEntitlementService
3B-4  audit-log middleware + per-platform rate-limit policy
3B-5  v1 routes: users/verify, users/{id}, health (external-user map)
3B-6  Chat permission + conversations + messages endpoints (thinnest slices)
3B-7  Error envelope + Form Requests + remaining §15 tests (contract asserts)
3B-8  docs: api-contract.md, realtime-chat.md, platform-isolation.md, security.md,
      README, changelog, current-tasks (security.md/ADR/changelog/tasks updated in
      3B-1; api-contract + realtime-chat + platform-isolation remain)
```

**Gate: Phase 3B is a SEPARATE approval.** 3B-1 (auth foundation) completed 2026-09-22; each later 3B sub-phase is separately approved before work.

---

## 18. Documentation Changes (Phase 3B)

- `docs/api-contract.md` — final request/response contracts.
- `docs/api-integration.md` — integration dashboard flow (Phase 4 notes).
- `docs/platform-isolation.md` — API-scoping additions.
- `docs/security.md` — threat model + key management.
- `docs/decisions.md` — ADR: PASETO v4.local; key rotation policy.
- `docs/README.md` (index) + `docs/changelog.md` + `docs/current-tasks.md` — Phase 3 tracking.

---

## 19. Phase 3A Exit Criteria (fulfilled by THIS doc)

- [x] Covers: platform registration/config, PASETO v4.local auth, token lifecycle, platform-scoped authz, external user mapping/sync, endpoint contracts, versioning, validation+standardized errors, rate limiting, audit logging, security+isolation, tests, required 3B artifacts, doc changes, implementation order.
- [x] Uses **multi-platform** language (never "multi-tenant" product-facing).
- [x] External platform remains source of truth for user data.
- [x] Stores only integration-required data (mapping + timestamps).
- [x] No per-platform databases at this stage.
- [x] Strict `platform_id` scoping + entitlement enforcement (T8).
- [x] No admin panel / client API / widget changes yet.
- [x] **No code written** — plan only.
- [x] Open decisions below clearly gated.

---

## 20. Open Decisions Requiring Approval

1. **PASETO package:** ~~`paragonie/paseto` v4.local approved?~~ ✅ **Approved** — installed in 3B-0/3B-1 and runtime-proven (unit + feature tests; ADR-013).
2. **Table naming:** `platform_integrations` / `platform_api_keys` / `platform_external_user_map` / `api_audit_logs` — confirm or rename.
3. **Token TTL:** ≤1h default OK? Secondary-key grace window (24h default)?
4. **Scope model:** coarse per-service scopes (`realtime_chat:read/write`) vs finer per-endpoint — decide now to avoid churn.
5. **Rate limit default 60/min** + per-platform configurability.
6. **Auth flow for `auth/token`:** client_id+secret exchange (short-lived) approved for MVP, or direct long-lived token? Recommend exchange.
7. **Conversation/message endpoint set in §9** — confirm scope for Phase 3B vs later.
8. **Error-code set (§11)** — confirm stable codes.
9. **Idempotency for chat/message POSTs** — required at MVP (recommend: yes, client `idempotency_key`).

---

## 21. Authoritative References

- `docs/phase-2a-schema-plan.md` — T1–T8 contract (all 8 tables verified on `myvivah`).
- `docs/realtime-chat.md`, `docs/widget-integration.md`, `docs/api-contract.md` — chat/identity/API surface.
- `docs/platform-isolation.md`, `docs/security.md` — isolation + security rules.
- `docs/decisions.md` — ADR-002 (ULID), ADR-011 (manual payments), ADR-012 (dynamic services catalog).
- `docs/AGENTS.md` — phase gating, terminology, "never multi-tenant."
- `docs/phase-2d-factories-seeders.md` — Phase 2D exit verification.

---

> **Phase 3A plan approved; Phase 3B-1 (PASETO auth foundation) implementation completed and verified 2026-09-22.** Remaining §17 3B sub-phases (external-user map, key rotation, audit trail, identity/conversation endpoints) await separate approval per the gate. Open §20 items 2, 4, 5 partially settled (tables `platform_integrations`/`platform_api_keys` + scopes `realtime_chat:read/write` + `throttle:paseto.issue` implemented); items 7–9 remain open.