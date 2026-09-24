# MyVivahAI — Known Issues, Uncertainties & Open Decisions

This file tracks everything that is currently uncertain, missing, or needs confirmation before implementation work begins. Keep it updated as decisions are made (move finalized items to decisions.md).

---

## Project State

### P0 — Empty Workspace

- The project directory contains **no Laravel application yet** (no `composer.json`, no `artisan`).
- The Laravel project must be initialized before any code work (recommended next phase).
- PHP/MySQL/Redis availability on the host machine: **unconfirmed**.

### Phase 5A — Public Website + Web Auth + App Shell (2026-09-24, EXIT VERIFIED)

- **Frontend stack deviation (decision made):** the phase outline suggested Laravel/Inertia/React/TS, but the project ships **Blade + Tailwind v4 + Vite + vanilla JS** (per AGENTS.md "no unnecessary dependencies"). Public pages, auth and the dashboard shell are server-rendered Blade. Any future Inertia migration would be incremental; not required.
- The dashboard is a **read-only app shell with honest empty-states** per ADR-011 (manual admin-approved payments — no gateway, no fake activation). Concrete follow-ups (blocked/future, see `docs/phase-5a-public-website-auth.md` §Follow-ups): subscription checkout + payment gateway UI, profile/settings editing, API-testing screen UI, plan purchase flow, contact-inbox admin list, `widget_configs` embed-code generator (from Phase 4).
- Demo content is minimal and honest: only `Service`/`Plan` demo rows (`platform-spot` demo platform) appear on Services/Plans; no invented testimonials, stats, or logos.

### Phase 4 — Widget (2026-09-23, EXIT VERIFIED)

- Widget identity-token bootstrap resolved + implemented (`/api/v1/widget/session` → short-lived PASETO v4.local widget session; token-bound identity, audience isolation, jti revocation, expiry). See `docs/widget-integration.md` §Phase 4.
- Widget **user search** is implemented against MyVivahAI-held identity references (`integration/users?q=`); delegation to the client's external **search API** is future work.
- The dashboard "configure widget / generate embed code" UI (widget_configs) is still open — embedding today is via `window.MyVivahAIWidget.init({session:{...}})`. Shadow-DOM CSS isolation decision remains open (current build uses scoped wrapper).

---

## Open Product Decisions

| # | Question | Impact | Suggested Default |
|---|---|---|---|
| 1 | Does phone/SMS verification exist for accounts? | Registration UX & infra (SMS provider cost) | Email-only at MVP |
| 2 | Is there a free or trial tier? | Subscription design | TBD |
| 3 | Are multiple user roles per platform (owner/admin/developer) supported? | Users table, platform_admins, policy design | owner+developer |
| 4 | Can a MyVivahAI account own multiple platforms? | users↔platforms relation | Yes |
| 5 | Are there per-platform admin approval flows? | Platform lifecycle | None at MVP |
| 6 | Billing: monthly/yearly? auto-renew? currencies? | Payments table, gateway choice | Monthly+yearly, auto-renew later |
| 7 | Proration / plan change / refund policies | Subscription logic | TBD |
| 8 | Is search a required widget feature at MVP? | api-contract, widget scope | Yes |
| 9 | Is the Chat Permission API required or strictly optional at MVP? | Contract, conversation-open flow | Optional |
| 10 | Group chats ever in scope? | Data model, messaging | No |

---

## Open Technical Decisions

> **2026-09 state:** the following MVP decisions are now **resolved** in decisions.md and are removed from the open list below: WebSocket/broadcast server (Laravel Reverb — ADR-010), identity token standard (PASETO v4.local ~5 min — ADR-006), payment workflow (manual admin-approved, no gateway at MVP — ADR-011), dynamic plans (admin-managed, demo data — ADR-012). **API auth (2026-09-22):** platform→MyVivahAI PASETO v4.local + token/key lifecycle resolved and implemented as 3B-1 (ADR-013); **Phase 3B-2 (2026-09-22)** additionally resolved the API key state machine (`active→rotated→revoked` with grace + hard-revoke, one-time secret), the platform-scoped external-user identity map (`platform_external_user_map`), and the append-only integration audit trail (`api_audit_logs`). **Phase 3C (2026-09-23)** resolved the per-platform rate-limit policy (`integration:<platform_id>` key on the verified platform id, per-row ceiling override default 60/min, `Retry-After`/`retry_after_seconds`, 429 still audited) and the client-facing integration API surface (config read/update, keys list/rotate/revoke, users list/show). **Phase 3D (2026-09-23)** resolved the chat unread-count strategy (#5 — incremental within the send/mark-read transaction, never recomputed from a full scan), the history pagination cursor (#14 — keyset on `messages.id`, opaque `next_cursor`/`before`, no offset), the actor-identity protocol for server-to-server chat calls (`X-External-User-Id` header resolved strictly inside the platform's map), and the chat REST surface (`/api/v1/chat` — see `docs/integration-api.md` §Chat API). **Phase 3E (2026-09-23)** resolved presence in the MVP (#6 — **DB-backed, Redis-optional**: `presence_status`/`presence_seen_at` on the identity map + computed staleness + `chat:presence-sweep` cron; transitions broadcast `user.online`/`user.offline` on `presence-chat.{platform}` when realtime is on; `GET …/chat/presence/{id}` doubles as the polling fallback), the realtime channel model (`private-chat.{conversation public_id}` / `presence-chat.{platform public_id}`, public ULIDs only) with server-side subscription authorization (`POST /api/v1/chat/socket/auth` — participant/platform-member + Pusher-protocol HMAC signature, denials `403 CHANNEL_DENIED`), and the broadcast delivery model (**inline `ShouldBroadcastNow` + `ShouldRescue` after commit — no queue worker, no Redis; driver lanes config-only, default `null`, Reverb/Soketi/Pusher package install deferred to deployment** — see `docs/deployment.md`). Remaining open items are listed below.

| # | Question | Options | Status |
|---|---|---|---|
| 1 | ~~WebSocket/broadcast server~~ | ~~Reverb / Pusher / Soketi~~ | **Resolved — Reverb (ADR-010)** |
| 2 | Dashboard frontend stack | Blade+Alpine / Livewire / Inertia+Vue | **Resolved — Phase 5A: Blade (server-rendered) + Tailwind v4 + Vite; interactive bits with small inline JS** — see `docs/phase-5a-public-website-auth.md` |
| 3 | ~~Identity token standard~~ | ~~JWT / PASETO / HMAC~~ | **Resolved — PASETO v4.local (ADR-006)** |
| 4 | Primary key strategy | BIGINT + ULID public ids | Recommended — finalized in decisions.md (D-2) |
| 5 | ~~Unread count strategy~~ | ~~Incremental vs periodic recompute~~ | **Resolved — Phase 3D: incremental, transactional (send/mark-read)** — see `docs/realtime-chat.md` §Unread Counts |
| 6 | ~~Presence in MVP~~ | ~~Included or deferred~~ | **Resolved — Phase 3E: included, DB-backed (no Redis), REST-first; transitions broadcast when realtime on + polling fallback** — see `docs/realtime-chat.md` §Presence |
| 7 | Message content encryption at rest | Plaintext indexable vs encrypted | Open (privacy tradeoff) |
| 8 | Widget CSS isolation | Shadow DOM vs scoped CSS | Open (recommended Shadow DOM) |
| 9 | Message retention policy | Indefinite vs prune | Open |
| 10 | API test log retention | Keep vs prune duration | Open |
| 11 | Security audit log retention | `api_audit_logs` grows unbounded (append-only) | Open — recommend a later prune/archival policy |
| 12 | Key rotation grace window default | 24 h (`paseto.rotation_grace_seconds`) | Configurable; default open for review pre-launch |
| 13 | Entitlement cache table vs compute-on-demand | Cache table recommended | Open |
| 14 | ~~History pagination cursor~~ | ~~Offset vs keyset~~ | **Resolved — Phase 3D: keyset on `messages.id`, opaque `next_cursor`/`before`** — see `docs/realtime-chat.md` §History & Pagination |
| 15 | Test/live widget mode | One embed URL, server-side resolution | Open (recommended) |
| 16 | File storage | Local vs S3 | Open |
| 17 | Deployment target | Docker/Forge/Envoyer/cloud | Open |
| 18 | PHP version | 8.2 / 8.3 / 8.4 | Open |
| 19 | Widget profile photo proxy | Direct CDN vs MyVivahAI proxy | Open |
| 20 | Server-to-server chat actor identity | `X-External-User-Id` (platform-asserted) vs per-user tokens | **Resolved — Phase 3D: `X-External-User-Id` header, platform-scoped** — see `docs/integration-api.md` §Chat API |
| 21 | Widget browser identity | Raw ID vs signed session token | **Resolved — Phase 4: short-lived widget session token minted by `/api/v1/widget/session`; browser identity always token-bound `aud = widget:{slug}`, spoofed header ignored** — see `docs/integration-api.md` §Widget API |
| 22 | Widget search source | MyVivahAI-held refs vs client search API | **Phase 4: partial — `integration/users?q=` against MyVivahAI references; full client-API delegation open** |

---

## Missing Information to Obtain from Business

- Concrete plan/price tiers for Real-Time Chat.
- Payment gateway preference/provider (India-focused? Razorpay/Stripe?).
- Legal/compliance requirements (data residency, GDPR-like rights, consent, age verification for matrimony data).
- Sample of a real matrimony platform's user database schema (to validate User Details fields).
- Whether client platforms will call MyVivahAI's hosted search results endpoint or the widget will need proxying.
- Branding requirements (custom domain for widget script, white-label).
- Expected initial platform scale (users/conversations per platform) — informs performance decisions.

---

## Confirmed Requirements (from product brief) — Quick Reference

- Multi-platform SaaS (never use "multi-tenant" in product-facing text).
- Laravel backend + MySQL database.
- External platforms are the source of truth for user profiles.
- MyVivahAI stores only references + chat/configuration data.
- First service: Real-Time Chat platform (widget). Others deferred.
- Platform data fully isolated.
- Client platform provides Identity, User Details, User Search (and optional Chat Permission) APIs.
- No raw user IDs trusted from the browser; use signed short-lived tokens.
- Payment verification activates subscription; dashboard sections unlock per service.

---

## Risks

| Risk | Description | Mitigation |
|---|---|---|
| Cross-platform leak | Missing platform filter exposes another platform's data | Isolation-first design + mandatory isolation tests |
| Credential leak | Client API secrets stored in DB | Encrypt at rest, mask in UI, no browser exposure |
| Widget conflict | Widget CSS/JS clashes with host site | Shadow DOM / scoped CSS, namespacing |
| Widget impersonation | Browser spoofs another user's id or opens others' threads | Phase 4: token-bound identity (`ExternalUserContext` ignores `X-External-User-Id`), participant guard on thread open, audience isolation platform↔widget |
| Token replay/impersonation | Reused or forged identity tokens | Short expiry + jti + signature verification |
| Rotated-key reuse in grace window | A compromised rotated key stays usable up to grace expiry | Short default grace, hard-revoke on sight after expiry, rotation audit events |
| Scope creep | Implementing chat/AI features before foundation | Stick to phases in current-tasks.md |
| Payment failure | Unverified payment enabling access | Strict status gating + webhook verification |
| Data duplication creep | MyVivahAI starts mirroring profiles | Data minimization rules documented |