# MyVivahAI — Platform Isolation

## Overview

Every registered external platform must have fully isolated data: its external users, conversations, messages, API integrations, credentials, and widget configuration.

**Product-facing terminology:** "multi-platform" (not "multi-tenant").

```
MyVivahAI
│
├── Platform A
│   ├── External Users
│   ├── Conversations
│   ├── Messages
│   ├── API Integrations
│   └── Widget Configuration
│
├── Platform B
│   ├── External Users
│   ├── Conversations
│   ├── Messages
│   ├── API Integrations
│   └── Widget Configuration
│
└── Platform C
    ├── External Users
    ├── Conversations
    ├── Messages
    ├── API Integrations
    └── Widget Configuration
```

---

## Isolation Requirements

| Requirement | Enforcement |
|---|---|
| Platform A must never access Platform B's data | `platform_id` scoping on all queries + ownership checks |
| Conversations platform-scoped | `conversations.platform_id`; connection between users of different platforms impossible |
| External user references platform-scoped | `external_users.platform_id` + UNIQUE (platform_id, external_user_id) |
| API credentials platform-scoped | Credentials reached only via `platform_integrations` belonging to the platform |
| Widget configurations platform-scoped | `widget_configs.platform_id` unique per platform |
| API requests resolve correct platform context | Platform resolved from identity token / public platform id; validated in middleware |
| Authorization verifies platform ownership | Policy/service checks on every entity access |
| WebSocket subscriptions authorized | Private channel auth checks participant+platform |
| Background jobs preserve platform context | Jobs carry platform_id and re-validate ownership at execution |

---

## Recommended Database Approach (Laravel + MySQL)

### Recommended Baseline: Shared Database, Shared Schema, Platform-Scoped Rows

All platform data lives in one MySQL database. Every resource that is platform-bound carries a `platform_id` column. SQL queries always narrow by the caller's platform.

**Pros:**
- Simplest migration story
- Single schema version for all platforms
- Easy to index and tune one database
- Analytics/aggregates across platforms are possible
- Cost-efficient (one DB server)

**Cons:**
- Requires discipline: every query must be platform-filtered
- A bug that misses the filter can leak cross-platform data
- Noisy-neighbor risk (one heavy platform affects others)
- One database to scale

**Mitigations for the recommended baseline:**
- Global scope / middleware that forces platform context
- Repository/service layer that always passes platform_id
- Automated tests that assert cross-platform isolation
- Read replicas later for scale

### Alternative A: Shared Database, Separate Schemas (per-platform schemas)

Each platform gets its own MySQL schema; tables exist per schema.

**Pros:** Strong physical isolation; simpler to reason about blasting radius.

**Cons:** Complex migrations (run per schema); connection management complexity in Laravel; cross-platform admin analytics harder; schema drift risk.

### Alternative B: Separate Databases per Platform

**Pros:** Maximum isolation; independent scaling; per-platform backup.

**Cons:** Heavy operations overhead; complex connection routing; costly; overkill at MVP.

### Recommendation

Start with **shared database, shared schema, platform-scoped rows**, enforced by a `PlatformContext` middleware and consistently applied `platform_id` filters. Revisit separate-schema or separate-database only if compliance/scale demands it. See [decisions.md](decisions.md) for the ADR.

---

## Resolving Platform Context

Platform context must be derived once per request and reused everywhere.

### Sources of Platform Context

| Context Source | When Used |
|---|---|
| Authenticated dashboard session (platform owner) | Dashboard routes |
| Identity token `platform` claim (signed by client) | Widget/API routes |
| Public platform ID from widget config | Bootstrap of widget session |
| Direct parameter (never accept untrusted platform param alone) | Avoid |

### PlatformContext Middleware (Recommended)

A middleware that:

1. Determines platform scope from the request (session or token).
2. Binds the platform into the container: `resolve(PlatformContext::class)->set($platform)`.
3. Rejects requests for suspended/deactivated platforms.
4. Ensures routes requiring platform context reject requests without it.

All service-layer repository calls accept or resolve the platform and append `platform_id` filters.

---

## Ownership Verification in Practice

### Eloquent Scoping

Recommended pattern:

```php
Conversation::query()
    ->where('platform_id', $platform->getKey())
    ->whereHas('participants', fn ($q) => $q->where('external_user_id', $user->id))
    ->findOrFail($publicId);
```

A **global scope** alone is insufficient and dangerous (one request assumption); explicit scoping in repositories is preferred so isolation intent is visible.

### Service Layer

- Services take `Platform $platform` (or resolve from context) as first argument.
- WebSocket handlers receive platform from the authenticated socket session.
- Jobs carry `platform_id` attribute; on execution the platform is re-resolved and re-validated (`platform->isActive()`).

### Policies

Laravel Policies used for dashboard-level resource checks (e.g., "this platform owner may edit this widget config"). Policy resolves the platform from the session and compares with the resource.

---

## WebSocket Isolation

WebSocket flow with isolation:

```
1. Widget POST /api/v1/widget/authenticate  {identity_token}
   → server verifies signature/expiry → returns {socket_session, platform_id, user}
2. Widget connects to WebSocket with socket_session
3. Socket channel name includes platform:  private-vivah.{platformId}.user.{userId}
4. On subscribe, server authorizes:
   - socket_session maps to platform_id + user
   - channel platform_id must equal socket session platform_id
   - channel user must equal socket session user (for user channel)
   - for conversation channel: user must be participant; conversation.platform_id == session platform_id
5. Any mismatch → subscribe rejected
```

**Never** let a client subscribe to a channel by declaring an arbitrary platform ID; authorization always derives platform from the authenticated session.

---

## Background Jobs

- Every job carrying platform data includes `platform_id`.
- On execution: resolve platform; if suspended/expired → abort job gracefully.
- Jobs that publish WebSocket events reuse the same channel auth path.

---

## Testing Cross-Platform Isolation

Mandatory test suite section: **Isolation Tests**

Examples:

- Create platform A and platform B with users/conversations.
- Assert platform A's user cannot fetch platform B's conversation by public ID.
- Assert platform A's credentials cannot be read by platform B owner.
- Assert WebSocket subscription to platform B conversation is rejected for platform A user.
- Assert search is scoped per platform.
- Assert a background job for platform B cannot touch platform A messages.

These tests run in CI for every change.

---

## Open Decisions

1. Whether to introduce per-platform read replicas before sharding.
2. Whether cross-platform analytics/aggregation is needed (affects whether aggregation queries are allowed).
3. Whether platform-specific custom branding implies any shared global config safety concerns.
4. Whether any domain data is **not** platform-scoped (e.g., global service catalog, admin-only data). Confirm that `services`, `plans`, `users` are global; platform data is everything under a platform.