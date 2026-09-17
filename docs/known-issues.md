# MyVivahAI — Known Issues, Uncertainties & Open Decisions

This file tracks everything that is currently uncertain, missing, or needs confirmation before implementation work begins. Keep it updated as decisions are made (move finalized items to decisions.md).

---

## Project State

### P0 — Empty Workspace

- The project directory contains **no Laravel application yet** (no `composer.json`, no `artisan`).
- The Laravel project must be initialized before any code work (recommended next phase).
- PHP/MySQL/Redis availability on the host machine: **unconfirmed**.

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

| # | Question | Options | Status |
|---|---|---|---|
| 1 | WebSocket/broadcast server | Laravel Reverb / Pusher / Soketi | Open — see decisions.md |
| 2 | Dashboard frontend stack | Blade+Alpine / Livewire / Inertia+Vue | Open — see decisions.md |
| 3 | Identity token standard | JWT HS256 / PASETO v4.local / custom HMAC | Open — see decisions.md |
| 4 | Primary key strategy | BIGINT + ULID public ids (recommended) vs ULID-only | Recommended, not finalized |
| 5 | Unread count strategy | Incremental vs periodic recompute cache | Open |
| 6 | Presence in MVP | Included or deferred | Recommended included, open |
| 7 | Message content encryption at rest | Plaintext indexable vs encrypted | Open (privacy tradeoff) |
| 8 | Widget CSS isolation | Shadow DOM vs scoped CSS | Recommended Shadow DOM, open |
| 9 | Message retention policy | Indefinite vs prune | Open |
| 10 | API test log retention | Keep vs prune duration | Open |
| 11 | Entitlement cache table vs compute-on-demand | Cache table (platform_service_access) recommended | Open |
| 12 | HMAC-signed client API calls at MVP vs static bearer | Decide contract auth | Open |
| 13 | Test/live widget mode mechanism | Single embed URL w/ server resolution vs separate URLs | Open |
| 14 | File storage for future attachments/widget assets | Local vs S3 | Open |
| 15 | Deployment target | Docker/Forge/Envoyer/cloud | Open |
| 16 | PHP version pinned | 8.2 / 8.3 / 8.4 | Open |
| 17 | Widget profile photo proxy/caching | Direct CDN vs MyVivahAI proxy | Open |

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
| Token replay/impersonation | Reused or forged identity tokens | Short expiry + jti + signature verification |
| Scope creep | Implementing chat/AI features before foundation | Stick to phases in current-tasks.md |
| Payment failure | Unverified payment enabling access | Strict status gating + webhook verification |
| Data duplication creep | MyVivahAI starts mirroring profiles | Data minimization rules documented |