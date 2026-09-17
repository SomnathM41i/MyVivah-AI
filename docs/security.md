# MyVivahAI — Security Documentation

## Overview

This document defines the security requirements and threat considerations for the MyVivahAI platform. It covers authentication, API security, widget security, webhook security, and data security.

**Threat model:** The platform must protect:

1. MyVivahAI accounts (platform owners)
2. The external platforms' data (via cross-platform isolation)
3. End users' identities and messages
4. Stored credentials (API keys, secrets)
5. Financial/payment and subscription data

---

## Authentication

### Account Registration & Verification

- Password hashing: **Bcrypt or Argon2id** (Laravel default bcrypt is acceptable; Argon2id recommended if hosted PHP supports it).
- Email verification enforced: account cannot be used for service access until email is verified.
- Phone verification: open decision; if implemented, OTP with expiry + attempt limits.
- All verification flows rate-limited.

### Password Handling

- Minimum length 8 (recommended 12+), no forced composition rules (NIST guidance).
- Never log passwords or verification tokens.
- Hash comparisons via `Hash::check` (constant-time).
- Password reset via signed, expiring link + optional challenge.

### Session Security

- Session ID in HttpOnly, Secure, SameSite=Lax cookie (`config/session.php`).
- Session driver: Redis or database (recommended over file for multi-server).
- Session rotation/regeneration on login and privilege change.
- Inactivity timeout: configurable (recommended default 30 min).
- Dashboard shows active sessions; user can revoke.

### Token Expiration

- Remember-me tokens rotated on use (via `remember_token` rotation).
- All short-lived integration tokens (widget identity) expire (see Widget Security).

---

## API Security

### MyVivahAI's Own APIs

- All client-dashboard APIs require authenticated platform-owner session.
- All widget-facing APIs require platform context + identity token verification.
- CSRF protection via Laravel middleware on web routes.
- `Accept: application/json`-driven JSON responses for API routes.

### Client Platform API Credentials (the client's own APIs)

MyVivahAI stores credentials used to call the client's external APIs:

| Requirement | Detail |
|---|---|
| Encryption at rest | Credentials encrypted with Laravel `Crypt` (AES-256-CBC / AES-256-GCM via key in `APP_KEY`) |
| Not in plaintext DB | Stored in dedicated `api_credentials` table as `secret_encrypted` |
| Masking in UI | Only masked hint (e.g., last 4 chars) shown in dashboard |
| Rotation | Admin/owner-triggered rotation; credential revision tracked |
| Access scope | Only the owning platform's dashboard can read/write; widget never receives them |

### Rate Limiting

- Login attempts: e.g., 5/min per IP+email.
- Registration: e.g., 10/hr per IP.
- API test runs: e.g., 30/min per platform.
- Chat message send: per-user limit (see realtime-chat.md).
- Global throttling per platform across its endpoints.

### Request Validation

- Every write request validated through Laravel Form Requests.
- Input sanitization for message content (strip/escape on render).
- Max sizes enforced (message length, file sizes when attachments come).
- JSON body size limits.

### API Versioning

- All external-facing widget APIs prefixed `/api/v1`.
- Dashboard APIs can be unversioned initially but a version prefix is recommended.
- Breaking changes introduced via new major version only.

---

## Widget Security

### Secure User Identification (Core Concern)

Raw external user IDs must **never** be trusted from the browser. The widget authenticates via a short-lived signed token issued by the client's backend.

Token requirements:

| Property | Requirement |
|---|---|
| Issuer | Client platform backend only (server-side) |
| Signing | Shared secret (registered platform secret) |
| Claims | `platform` (public_id), `external_user_id`, `jti` (unique), `iat`, `exp` |
| Expiry | 5–15 minutes (recommended); refresh flow required |
| Replay protection | Unique `jti` checked/revoked server-side within validity window |
| Algorithm | JWT HS256 or PASETO v4.local (see decisions.md) |

Verification flow on MyVivahAI:

1. Parse token.
2. Verify signature — must match the **platform's** registered secret (not a global master secret).
3. Verify `platform` claim matches the platform context.
4. Verify `exp` not passed.
5. Check `jti` not previously used (short-lived cache).
6. Resolve external user within that platform scope.

**Important: MyVivahAI verifies the client's identity token against the platform-specific secret.** This is the mechanism preventing cross-platform impersonation.

### Platform Identification

- Public platform ID (`platforms.public_id`) is used in widget config.
- It is **not** secret — it's a public identifier.
- Identity tokens must still be verified per-platform as above.
- No global-next tokens; authorization always resolves against the requesting platform.

### Conversation Authorization

- WebSocket subscription to `private-...conversation.{id}` is authorized server-side: only participants.
- Message send: validated that sender is a participant and conversation platform == sender platform.
- Message reads: only participants.
- Conversation listing: only conversations where the user is a participant in the user's platform.

### Preventing Unauthorized Access

| Attack | Control |
|---|---|
| User ID spoofing | Signed token (never raw ID) |
| Token replay | `jti` reuse check + short expiry |
| Cross-platform data | platform_id enforced on every DB query and channel |
| Forged WebSocket subscribe | Private-channel auth server-side |
| Tampered widget config | Config resolved server-side; public fields only in page |
| ID enumeration | ULID public IDs |

### Avoiding Exposure of Secret Credentials

- Widget embed script contains **only** public platform id and public config.
- No client API credentials embedded.
- Token endpoint (client-side call) means the secret stays on the client backend.
- MyVivahAI server-side calls client APIs with stored credentials; the browser never sees those credentials.

---

## Webhook Security

(Applies to future webhook features: payment gateways, client platform webhooks, etc.)

| Requirement | Detail |
|---|---|
| Signature verification | Every webhook verified with HMAC signature using a shared secret; reject mismatches |
| Replay protection | Timestamp window + nonce/idempotency key tracking |
| Idempotency | Store processed event IDs; skip duplicates |
| Rate limiting | Per-source throttling |
| Logging | All webhook deliveries logged (headers redacted) and auditable |
| Secrets | Webhook signing keys encrypted at rest |

---

## Data Security

### Data Minimization

- MyVivahAI stores only what is required for a service.
- External platform remains source of truth for user profiles.
- External user records store minimal cached fields.
- Do not pull entire overseas user databases.

### Encryption

| Data | Protection |
|---|---|
| Passwords | Hashing (never reversible) |
| API credentials | Encrypted at rest (`Crypt`) |
| Payment information | Never stored — handled by gateway; store only gateway references |
| Message content in transit | TLS 1.2+ required |
| Message content at rest | Open decision (DB-encryption vs application-level; weigh indexability vs privacy) |
| Backups | Encrypted backups |

### Sensitive Data Handling

- Emails/phones stored only when required by a service.
- Profile photos cached with referrer-hidden URLs on request.
- Logging must never include: passwords, tokens, full API secrets, payment PAN data.
- Log redaction helpers applied to request/response logs.

### Access Control

- Role-based access within a platform (owner/admin/developer) — open decision on final roles.
- Admin-level MyVivahAI access controlled via additional role gates.
- Every service-layer operation verifies platform ownership.

### Audit Logging

Recommended audit events:

- Login/logout, failed logins
- Platform status changes
- Integration config changes
- API credential rotation
- Subscription changes
- Widget live activation
- Admin actions

Audit log table design (future): `audit_logs` with `actor`, `subject_type`, `subject_id`, `action`, `changes (json)`, `ip`, `occurred_at`.

### Backup & Recovery

- Database backups scheduled (RPO target: open decision; recommended daily).
- Restore test run periodically (documented procedure).
- File storage backups/replication.
- Recovery point documented per environment.
- Secrets/failing keys (APP_KEY) backed up securely; loss prevents credential decryption.

---

## Threat Scenarios Checklist

| # | Scenario | Primary Defense |
|---|---|---|
| 1 | Attacker edits widget to claim another user's ID | Signed identity token |
| 2 | Reuse stolen token later | short exp + jti check |
| 3 | User from Platform A reads Platform B conversations | platform_id scoping everywhere |
| 4 | Attacker subscribes to someone else's WebSocket channel | private channel authorization |
| 5 | Credential leak via page source | no secrets in frontend |
| 6 | Brute-force login | rate limiting + lockout |
| 7 | Replay of payment webhook | signature + idempotency |
| 8 | Massive batch of fake external user references | rate limits + per-platform caps |
| 9 | Message content injection (XSS in chat UI) | escaping on render + CSP |
| 10 | Data breach exposing API secrets | encryption at rest + rotation |

---

## Open Decisions

1. Message content encryption-at-rest approach (openness vs privacy tradeoff).
2. Role model for platform-level collaboration (owner/admin/developer).
3. Whether phone verification is required.
4. HMAC-signed client API auth at MVP (vs static bearer).
5. Audit logging framework (Laravel Audit log package vs custom events).
6. Whether uploaded attachments (when added) are scanned for malware.
7. Compliance requirements (GDPR, local data residency for India) — confirm scope.