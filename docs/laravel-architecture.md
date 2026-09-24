# MyVivahAI — Laravel Application Architecture

## Overview

This document defines how the MyVivahAI Laravel application should be structured. It prescribes a practical, scalable organization without over-engineering. It covers directory layout, model organization, controllers, form requests, policies, services, events, jobs, queues, notifications, API resources, middleware, broadcasting, and configuration.

---

## Application Stack (Recommended)

| Layer | Choice | Status |
|---|---|---|
| Framework | Laravel (latest stable, 11.x/12.x) | Recommended |
| PHP | 8.2+ | Required |
| Database | MySQL 8.0+ | Recommended |
| Cache/Queue | Redis | Recommended |
| Broadcasting | Laravel Reverb (self-host) — **Resolved** (ADR-010) |
| Dashboard UI | Blade + Alpine.js or Livewire | Open decision (decisions.md) |

---

## Directory Organization

Standard Laravel structure, grouped by domain modules under `app/`. Domains mirror the modules defined in architecture.md.

```
app/
├── Models/                      # Eloquent models (see Model organization below)
│   ├── Platform.php
│   ├── Service.php
│   ├── Plan.php
│   ├── Subscription.php
│   ├── Payment.php
│   ├── ApiEndpointConfig.php
│   ├── ApiCredential.php
│   ├── ApiTestLog.php
│   ├── Conversation.php
│   ├── Message.php
│   ├── ExternalUser.php
│   └── WidgetConfig.php
│
├── Http/
│   ├── Controllers/
│   │   ├── Auth/                # Registration, login, verification, reset
│   │   ├── Dashboard/           # Overview pages, platform settings
│   │   ├── PlatformAdmin/       # Platform profile admin
│   │   ├── Services/            # Service catalog
│   │   ├── Subscription/        # Plans, subscriptions
│   │   ├── Payment/             # Checkout, webhooks
│   │   ├── Integration/         # API integration config + tests
│   │   ├── Widget/              # Widget config, code generation
│   │   └── Api/V1/              # Widget-facing APIs
│   │       ├── AuthController (token verification)
│   │       ├── ConversationController
│   │       ├── MessageController
│   │       ├── SearchController
│   │       └── PresenceController
│   ├── Requests/                # Form Requests (per domain)
│   │   ├── Auth/...
│   │   ├── Integration/...
│   │   ├── Subscription/...
│   │   └── Api/V1/...
│   ├── Middleware/
│   │   ├── PlatformContext.php  # Resolves+verifies platform scope
│   │   ├── EnsurePlatformAccess.php  # Entitlement middleware (per service)
│   │   └── ...
│   └── Resources/
│       ├── ConversationResource.php
│       ├── MessageResource.php
│       ├── ExternalUserResource.php
│       └── PlanResource.php
│
├── Services/                    # Application services (business logic)
│   ├── Platform/
│   ├── Entitlement/
│   │   └── EntitlementService.php
│   ├── Identity/
│   │   └── WidgetTokenService.php   # token verify/issue (platform-scoped)
│   ├── Integration/
│   │   ├── IntegrationTestService.php
│   │   └── ExternalApiClient.php    # calls client APIs (auth, timeout, logging)
│   ├── Chat/
│   │   ├── ConversationService.php
│   │   ├── MessageService.php
│   │   ├── PresenceService.php
│   │   └── UnreadService.php
│   └── Widget/
│       └── WidgetScriptService.php  # generates embed code
│
├── Policies/
│   ├── ConversationPolicy.php
│   ├── MessagePolicy.php
│   ├── WidgetConfigPolicy.php
│   ├── ApiEndpointConfigPolicy.php
│   └── SubscriptionPolicy.php
│
├── Events/  Listeners/
│   ├── SubscriptionActivated/Expired/Cancelled
│   ├── MessageSent
│   ├── ConversationStarted
│   ├── UserPresenceChanged
│   └── IntegrationActivated
│
├── Jobs/
│   ├── RunApiIntegrationTest.php
│   ├── PersistMessage.php        # if async persistence chosen
│   ├── PruneApiTestLogs.php
│   ├── ExpireSubscriptions.php
│   └── SendMessageNotifications.php
│
├── Notifications/
│   ├── EmailVerified.php
│   ├── SubscriptionActivated.php
│   ├── PaymentReceived.php
│   └── PlanExpiring.php
│
├── Broadcast/                   # (Laravel Reverb/Pusher channels)
│   ├── Channels/
│   │   ├── UserChannel.php
│   │   └── ConversationChannel.php
│   └── as needed
│
├── Providers/
│   ├── AppServiceProvider.php
│   ├── RouteServiceProvider.php
│   ├── ... (module providers, only if modularity demands)
│   └── BroadcastServiceProvider.php
│
└── Console/
    ├── Commands/
    │   ├── ExpireSubscriptions.php
    │   └── RecomputeUnreadCounts.php
    └── Kernel.php / bootstrap
```

---

## Model Organization

- One model per table; models live innamespaced in `app/Models` if desired (or keep flat per Laravel convention `app/Models`).
- Eloquent scopes used for domain queries.
- Casts for status enums, JSON columns, timestamps.
- `Fillable`/`guarded` with `$guarded = []` discouraged on sensitive models; explicit `$fillable`.
- Model events used sparingly (invariants). Prefer service/event layer.

### Key Model Examples (conceptual rules)

```
Platform:
  - belongsTo(User::class, 'created_by')
  - hasMany(Subscription, ExternalUser, Conversation, WidgetConfig, ...)

Subscription:
  - belongsTo(Platform, Service, Plan)
  - scopes: active()

ExternalUser:
  - belongsTo(Platform)
  - unique per (platform_id, external_user_id)
  - cached profile fields only

Conversation:
  - belongsTo(Platform)
  - belongsToMany(ExternalUser)->through(conversation_participants)
  - hasMany(Message)
  - unique participant pair per platform

Message:
  - belongsTo(Conversation, sender)
  - client_message_id idempotency (conversation_id + client_message_id unique)
```

---

## Controllers

- **Thin controllers:** validate via Form Requests, call Services, return responses.
- Dashboard controllers: Blade/Inertia views (open decision on view stack).
- API V1 controllers: JSON only, resource classes, consistent error envelope.
- No business logic inline in controllers.

## Form Requests

- One Form Request per action that requires validation.
- `authorize()` rules default to `true`; authorization handled by Policies/Middleware (keep decision layers separate).
- Rules reference each domain's `Validation` namespace when shared.

## Policies

- Define per-entity authorization: ownership + platform scoping.
- `viewAny`, `view`, `create`, `update`, `delete` canonical methods.
- Dashboard routes pair `authorizeResource` or explicit `$this->authorize(...)`.
- Policies resolve the caller's platform for ownership comparisons.

## Services

Contain the transactional/business logic. Rules:

- **One responsibility per service** (e.g., `MessageService::send`, `MessageService::markRead`).
- Services depend on other services via constructor injection (or `app()` resolution) — prefer constructor injection.
- Services never render HTML; they return data/domain results.
- Services may dispatch Events/Jobs.
- All multi-step operations run within DB transactions.

## Events & Listeners

| Event | Purpose |
|---|---|
| `UserRegistered` | Send verification email |
| `PlatformCreated` | Trigger onboarding notifications |
| `SubscriptionActivated` | Unlock entitlements, notify owner, log |
| `SubscriptionExpired` | Revoke entitlements, notify owner |
| `MessageSent` | Broadcast to conversation channel, update unread/presence |
| `IntegrationActivated` | Update status, optionally notify |
| `PaymentReceived` | Activate/update subscription, notify |

- Events are mostly **domain events** (past tense) recorded after commit where possible.
- Listeners are idempotent and queued where slow (emails, webhooks).

## Jobs & Queues

| Job | Queue | Notes |
|---|---|---|
| `SendEmailNotification` | `notifications` | Always queued |
| `RunApiIntegrationTest` | `integration` | Background API test execution |
| `ExpireSubscriptions` | `default` | Scheduled (`schedule`) |
| `RecomputeUnreadCounts` | `default` | Nightly reconciliation |
| `VerifyPaymentStatus` | `payments` | After gateway webhook |

- Queue connection: Redis.
- Failed-job handling via `queue:failed` monitoring.
- Everything non-interactive that could take >1s is queued.

## Notifications

- Use Laravel Notifications (Mail + database channels).
- Templates: transactional (activation, payment, expiry warnings).
- Do not send service-specific spam without consent.

## API Resources

- `ConversationResource`, `MessageResource`, `ExternalUserResource`, `PlanResource`, `PlatformResource`.
- Stable, documented JSON shapes for widget consumption.
- Include only necessary fields (data minimization).

## Middleware

| Middleware | Responsibility |
|---|---|
| `auth` (built-in) | Dashboard authenticated sessions |
| `verified` (built-in) | Email-verified accounts only |
| `PlatformContext` | Resolve and bind platform scope for the request |
| `EnsurePlatformAccess:chat` | Entitlement check: platform has active access to service |
| `throttle` (built-in) | Rate limiting on sensitive routes |
| Custom `ShareWidgetConfig` (or use config service) | Serve embed config server-side |

Middlewares are route-grouped:
- `web` + `auth` + `verified` → dashboard routes
- `api` + platform token verification → `/api/v1` widget routes
- `throttle` variants → authentication & token exchange endpoints

## Broadcasting / WebSockets

Channel definitions (in `routes/channels.php`):

```
private-vivah.{platformId}.user.{externalUserId}
   → authorize: authenticated session platform matches, user matches

private-vivah.{platformId}.conversation.{conversationId}
   → authorize: platform matches AND requesting user is participant
```

- On broadcast: unless the message targets any participant, broadcast events on those channels with per-user payloads.
- Broadcast driver choice (Reverb vs Pusher) is an open decision.

## Redis Utilization

| Key | Purpose | TTL |
|---|---|---|
| `vivah:{platform}:presence:{userId}` | Presence marker | ~60s with heartbeat |
| `vivah:{platform}:token-jti:{jti}` | Token replay guard | = token exp window |
| `cache:...` | Cache reads (plans, config) | varying |

Redis is also the queue driver and cache driver.

---

## Configuration & Environment

Referenced `.env` keys (planned):

```
APP_NAME=MyVivahAI
APP_ENV / APP_DEBUG
APP_KEY                     # encryption key (secrets)
DB_*                        # MySQL
REDIS_*                     # Redis
BROADCAST_DRIVER=(reverb|pusher)
REVERB_* / PUSHER_*         # WebSocket config
QUEUE_CONNECTION=redis
ASSET_URL                   # widget CDN host
```

Never commit real `.env`; use `.env.example`.

---

## Routing Structure

```
web.php                    # Landing, auth, dashboard routes
api.php                    # /api/v1 widget-facing routes (token-authed)
channels.php               # Private channel auth callbacks
console.php                # Scheduler closures/commands
```

Route groups:
- Dashboard: `web`, `auth`, `verified`, `PlatformContext`
- Service-specific dashboard sections: `EnsurePlatformAccess:chat`
- Widget API: `api`, platform-verified, throttled

---

## Schema Naming Conventions

- Tables: snake_case plural (`conversation_participants`).
- Columns: snake_case (`external_user_id`).
- Booleans: `is_`, `has_`, `can_` prefixes, `DEFAULT false`.
- Timestamps: `created_at`/`updated_at`; domain-specific (paid_at, ended_at).
- Soft delete: `deleted_at`.
- Public IDs: `{entity}_id` as ULID columns (`public_id`) on entities shared with browsers.

---

## Testing Strategy (Recommended)

| Suite | Coverage |
|---|---|
| Unit | Services (message send, entitlement resolution, token verification) |
| Feature | API endpoints, dashboard flows, integration test runner |
| Isolation | Cross-platform access attempts (see platform-isolation.md) |
| WebSocket | Public channel auth + prevent unauthorized subscribes |

- `phpunit.xml` default Laravel test setup; use `RefreshDatabase`.
- Factories for users, platforms, subscriptions, conversations, messages, configs.

---

## Open Decisions

1. View stack: Blade+Alpine vs Livewire vs Inertia+Vue for the dashboard.
2. Broadcast driver — **Resolved**: Laravel Reverb (ADR-010).
3. Whether Eloquent global scopes are used (recommended: explicit scoping) for platform.
4. Module boundaries as plain directories vs Composer packages (start simple: directories).
5. Whether to adopt Laravel 12 conventions or stick with 11 defaults once initialized.