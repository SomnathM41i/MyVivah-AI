# Phase 5A — Public Website, Web Auth & App Shell (Implementation Report)

**Status:** EXIT VERIFIED 2026-09-24 · 166 tests / 1285 assertions green on sqlite AND MySQL · Pint PASS (187 files) · PHPStan level 5 = 0 errors · `vite build` OK · `npm run test:widget` 15/15

---

## 1. Scope & Stack Decision (DEV-01 — Blade, not Inertia/React/TS)

The outline for this phase mentions "Laravel/Inertia/React/TS." This project has never shipped frontend framework code, and `docs/AGENTS.md` forbids over-engineering ("Do not introduce a new package, framework, or infrastructure component without checking whether it is necessary").

**Decision:** Phase 5A is **server-rendered Blade** on the existing Laravel 12.69.2 + Tailwind v4 + Vite + vanilla-JS stack.

Rationale recorded for the maintainer:
- Blazing fast, zero client dependency, SEO-friendly out of the box (marketing pages are indexable).
- The existing widget client JS (`public/js/myvivah-widget.js`) is already the product's interactive surface; the account web UI needs only forms + session navigation, which Blade+Tailwind does cleanly.
- Vite/Tailwind v4 is already configured; introducing Inertia now means React+Vite multi-page config, a client bundle, and a new data layer for no functional gain.
- If a future phase wants Inertia, markdown-level incremental migration is possible — Blade views already live behind controllers and route names.

**Consequence documented in `docs/known-issues.md`:** dashboard frontend stack decision (#2) resolved **Blade** as of Phase 5A.

## 2. What Was Built

```
Public marketing site          →  /, /about, /services, /plans, /contact
Web authentication (session)   →  signup, login, logout, forgot/reset, email verification
Authenticated app shell        →  /dashboard + 5 sub-screens (read-only, honest states)
Contact inbox                  →  contact_messages table + mail + honeypot
Design system (Blade)          →  card, badge, alert, field, section-heading, flash,
                                  button, icon, logo + guest/auth/dashboard layouts
```

## 3. Public Marketing Pages

All pages render through `<x-layout.guest>`, which emits SEO head metadata:

- `<title>` = `"{title} — MyVivahAI"` (default brand title when no prop), `meta[name=description]`, `<link rel=canonical>` (current URL), Open Graph + Twitter cards (`og:site_name` MyVivahAI, `og:type` website), `meta[name=robots] content="index, follow"`, `csrf-token`, favicon, Bunny Fonts Instrument Sans, `@vite`.
- Each controller passes its own small `meta` array. There is no `app/meta.php` — per-page copy lives in the controller (single-use constants are fine here; nothing is platform-owned).

| Route | Controller | Content source |
|---|---|---|
| `/` | `Home::__invoke` | Brand hero, "live by value" feature grid, "how it works", final CTA. Claims are product facts only (REST API, tops 4000-char messages, React/Node/PHP SDK story from `api-contract.md`). No invented testimonials/stats. |
| `/about` | `About::__invoke` | Mission + 3 "principles" cards (platform-first isolation, API-first, privacy). Design copy, clearly principles. |
| `/services` | `Services::__invoke` | **Live services render from the `services` table** (`where is_active = true`, ordered by `name`) — the same flag that drives API entitlements, so the page cannot drift from reality. A "roadmap" group is controller copy (`services.features`/`restrictions` columns do not exist in this schema) badged **Coming soon** + dashed borders; a "Request a service" card points at `/contact`. |
| `/plans` | `Plans::__invoke` | **DB-driven plan catalog (ADR-012).** Only `is_active` plans whose parent service is active render (`where is_active → with service → filter service.is_active`). Pricing/limits always reflect what the entitlement engine grants. Badge highlight = `plan_key === 'growth'`. |
| `/contact` | `ContactController::index` | Contact + support page with the form. |

### Contact store (privacy-minimal)

`POST /contact` (`throttle:6,1`) via `ContactRequest`:
- Fields: `name` (required), `company` (nullable), `email` (rfc), `phone` (nullable), `message` (required, min 10 / max 5000), **honeypot `website`** (`sometimes, string, max:0` — real users never submit it; bots fail validation).
- `ip_hash` = `hash('sha256', $request->ip())` — **no raw IP is stored**; `source_url` = referer truncated to 2048.
- Stores a `ContactMessage` row then sends `ContactMessageMail` (markdown, `emails/contact-message.blade.md`) to `config('mail.to.address')` — guarded so a null mail target doesn't throw (`phpunit.xml` sets `MAIL_TO_ADDRESS` to a test address).
- `contact_messages` migration (20th on MySQL `myvivah`): `name`, `email`, `company`, `phone`, `message` TEXT, `ip_hash` CHAR(64) UNIQUE, `source_url`, `created_at` only (`ContactMessage::UPDATED_AT = null` — append-only inbox).

## 4. Web Authentication (Session Guard)

### Rules implemented (with `docs/registration-flow.md`)

1. **Signup creates the account ROOT atomically** — `PlatformAccountService::register()` opens a DB transaction that creates `users` (default `status=active`, `hashed` password cast, `UTC`/`en`) + `platforms` (unique human slug via `uniqueSlug()`, `status=Platform::STATUS_ACTIVE`, `created_by`) + `platform_admins` (`role='owner'`, `invited_at`/`accepted_at` = now). The verification email is sent **after** the transaction commits so a failed mail never rolls back a valid account.
2. **Email verification is required before first login.** `VerifyEmailNotification` is queued-by-object (sent synchronously in tests via `Mail`/`Notification`). The success path redirects to `verification.notice` (`->with('registration_email', $email)` + neutral `status`). **No pre-verification login.**
3. **Login gates (constant messages, no enumeration):**
   - No such account or wrong password → `__('auth.failed')`.
   - `status ∈ {suspended, deactivated}` → "This account is not active…" (support contact).
   - Not verified → redirect to `verification.notice` with resend.
   - Success → `Auth::login` + session regenerate + `last_login_at` update + `redirect()->intended(route('dashboard'))`.
4. **Email verification endpoint** — `GET /verify-email/{id}/{hash}` behind **`signed`** middleware (expiry + tamper signature) + `hash_equals(sha1(email), hash)`. The URL itself is the credential — verifying does NOT require login, then signs the user in and lands on the dashboard. `verification.notice` (GET) lives **outside** the `guest` group so the `verified` middleware can redirect an authenticated-but-unverified user there. `POST /verify-email/resend` throttled 3:1, neutral message (no enumeration).
5. **Password reset** — `ForgotPasswordController` uses `Password::broker()`; `ResetPasswordNotification` rendered via `User::sendPasswordResetNotification()` hook. Both forgot + reset store routes throttled 5:1; forgot returns a single neutral status text. `Password::Reset` via `ResetPasswordRequest` (token, email, password confirmed with defaults).
6. **Logout** — POST `/logout` (`auth`), session invalidate + regenerate token, redirect `/`.

### Security details

- `POST /signup` + `POST /login` throttled `6,1`; password routes `5,1`; verify-resend `3,1`; contact `6,1`.
- Passwords hashed via the model's `hashed` cast; `User` hides `password`/`remember_token`.
- **Critical fix (root-caused by tests):** `App\Models\User` now **implements `MustVerifyEmail`**. The framework's `Illuminate\Foundation\Auth\User` provides the `MustVerifyEmail` *trait* but does NOT declare the *contract* — so the `verified` middleware silently passed for unverified users, defeating dashboard gating. With the contract, `email_verified_at` is actually enforced. This is the single most important correctness fix in the phase.
- Input validation lives in `FormRequests` (`Web/{Register,Login,ForgotPassword,ResetPassword,Contact}Request`).

## 5. Authenticated App Shell (Read-Only, Honest)

All routes behind `['auth', 'verified']`. Guest visits → login; unverified → verification notice.

| Route | Content |
|---|---|
| `/dashboard` (name `dashboard`) | Overview: `platform-summary` partial (platform name/slug/status from DB), the validated `activeSubscription()` result, `serviceAccess` rows, latest 5 payments. |
| `/dashboard/services` | Real `platform_service_access` entitlements + catalog. |
| `/dashboard/integrations` | `PlatformIntegration` health + `primaryApiKey()->secret_fingerprint` (never secret material) + "how to embed the widget" instructions pointing to `docs/integration-api.md`. |
| `/dashboard/subscription` | Current plan/subscription from DB. |
| `/dashboard/payments` | Payment history; per ADR-011 this is an honest empty-state + expectation copy (manual admin-approved workflow; **no gateway exists yet**). |
| `/dashboard/settings` | Read-only profile summary (user + platform). |

**Honest empty-state design (ADR-011):** the shell never fakes activation. Statuses are `pending/active/expired/cancelled/suspended` (there is **no `trialing`** status in this schema — the dashboard's `activeSubscription()` filter is `status='active'` AND `ends_at IS NULL OR ends_at > now()`). Payments show only the columns that exist (`gateway_transaction_id`/`paid_at`/`amount`/`currency`). Checkout/gateway UI is explicitly a later phase and the screens say so.

## 6. Blade Design System

- **`x-layout.guest`** — public shell (head SEO block above + nav/footer/flash/slot).
- **`x-layout.auth`** — centered card for auth screens: props `title`/`subtitle`, optional `$footer` named slot, brand lockup.
- **`x-layout.dashboard`** — authenticated shell: topbar + `dashboard-user-card` partial + nav (`dashboard{,services,integrations,subscription,payments,settings}`, light "active" ring) + content slot with `breadcrumbs`.
- **`x-field`** — renders its own `<input>`; `value` prop + `old()` repopulation EXCEPT `type=password` (password values never repopulate); error state renders `id="{name}-error"` + `aria-invalid`; passes through `form*` attributes; optional `trailing` slot (e.g. password-eye toggle staring at `icon-eye-off`).
- **`x-alert`** (`tone: info|success|error|warning`, dismissible, `data-autohide`) + **`x-flash`** — renders session `status/success/error/warning` + all `$errors`.
- **`x-button`** — variants `primary/light/Light Outline/secondary/ghost/inline` (the two light variants were added for the gradient final-CTA on Home), `as=` link|button|submit, `size`, optional leading `icon`.
- **`x-card`**, **`x-badge`** (tones incl. success/neutral + dot), **`x-section-heading`**, **`x-logo`**, **`x-icon`** (inline SVG set; fixed a stray `]` that broke `icon-eye-off`).
- **`public/favicon.svg`** + `public/js/app.js` (auto-hiding native `<dialog>`, flash auto-hide, password eye toggle, mobile nav).

## 7. Tests (6 new files ≈ 177 assertions)

- `PublicPagesTest` — every public page 200 + expected `<h1>`s; `/plans` renders DB plans via `ServiceFactory::active()`/`PlanFactory::active()` (no seeded data in tests); `/services` shows live + roadmap groups; `x-layout.guest` emits canonical/OG + `meta[description]`.
- `ContactSubmissionTest` — valid submit stores row + sends `ContactMessageMail` (`Mail::fake`); honeypot `website` → 422; under-length message / bad email → 422; ip is stored as a hash, not raw.
- `AuthUserRegistrationTest` — signup creates user (unverified) + platform + owner `platform_admins` row in one transaction; dispatches `VerifyEmailNotification`; lands on `verification.notice`; `assertNotAuthenticated()`; duplicate email 422; terms unchecked 422.
- `AuthLoginTest` — unverified → redirect `verification.notice`; wrong creds → 422 `auth.failed`; verified+active → session, `intended()` to `dashboard`, `last_login_at` set; suspended → blocked.
- `PasswordResetTest` — forgot → neutral status (no user leak); reset token rotation via broker (`Password::Reset`), new password then logs in; bad token rejected.
- `DashboardAccessTest` — guest → redirect `/login`; unverified → `verification.notice`; verified → all six sections render + `platform-summary` reflects DB values.

## 8. Verification (ALL GREEN)

- `php -l` clean on every changed file.
- Pint (Laravel preset) `--test` **PASS — 187 files**.
- PHPStan **level 5 — No errors**.
- PHPUnit full suite **166 tests / 1285 assertions** on **sqlite AND real MySQL `myvivah`**.
- `migrate:fresh --force` **20/20** on MySQL (adds `contact_messages`).
- `npm run test:widget` **15/15**; `vite build` success → `public/build/manifest.json` + `app-*.css/js` assets.
- `php artisan route:list` confirms the full public/auth/dashboard route set; `/demo/chat` unchanged.

## 9. Follow-ups / Not Delivered (Documented, Non-Blocking)

- Checkout + payment gateway UI (dashboard/subscription shows honest pending state; ADR-011).
- Profile/settings editing (settings is read-only today).
- API-testing screen UI, plan purchase landing/checkout screen.
- Contact-inbox admin list (only the DB row + outbound mail exist today).
- `widget_configs` embed-code generator (from Phase 4).

## 10. Deviation Register

| # | Announced | Shipped | Consequence |
|---|---|---|---|
| DEV-01 | Inertia/React/TS frontend | Blade + Tailwind v4 + Vite | No SPA; marketing SEO direct; any future Inertia = incremental |
| DEV-02 | `app/meta.php` central SEO helper (draft plan) | per-controller `meta` array | Simpler; no shared file claimed |
| DEV-03 | — | `services.features`/`restrictions` columns (did NOT exist) | Services page uses `services.description` + controller roadmap copy; no schema change for marketing copy |