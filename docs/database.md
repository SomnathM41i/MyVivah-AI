# MyVivahAI — Database Design

## Overview

This document describes the conceptual database design for the complete MyVivahAI platform. It covers the core platform domain, integration domain, real-time chat domain, and future domains.

**Important:** No migrations are created yet. This document is the specification that will guide migration creation in the implementation phase.

---

## Convention Notes

Unless stated otherwise:

- `id` is the primary key on every table (auto-increment `BIGINT UNSIGNED` or `ULID` — see open decisions).
- Laravel `created_at` / `updated_at` timestamps on all tables (nullable).
- `deleted_at` timestamp (nullable) enables soft deletes where noted.
- Foreign keys named `{table}_id` referencing `id`.
- Public identifiers (shared with external systems/browser) are separate from internal primary keys for security.

---

## Domain Map

```
Core Platform Domain        Integration Domain          Real-Time Chat Domain
├── users                   ├── platform_integrations   ├── external_users
├── platforms               ├── api_configurations      ├── conversations
├── platform_admins         ├── api_credentials         ├── conversation_participants
├── services                ├── api_endpoint_configs     ├── messages
├── plans                   ├── api_test_logs            ├── message_statuses
├── subscriptions           ├── webhook_configs          ├── message_attachments
├── payments                                             └── widget_configs
└── platform_service_access                                 ├── widget_contents (future)
                                                           └── widget_sessions (future)

Future Domains                 (not implemented yet)
├── whatsapp_integrations
├── ai_agents
├── ai_conversations
├── ai_usage_tracking
└── data_entry_jobs
```

---

## Core Platform Domain

### `users` (MyVivahAI accounts)

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| name | VARCHAR(255) | |
| email | VARCHAR(255) UNIQUE | |
| email_verified_at | TIMESTAMP NULL | |
| phone | VARCHAR(32) NULL UNIQUE | Open decision |
| phone_verified_at | TIMESTAMP NULL | Open decision |
| password | VARCHAR(255) | Bcrypt/Argon2id hash |
| avatar_url | VARCHAR(2048) NULL | |
| timezone | VARCHAR(64) | Default UTC |
| locale | VARCHAR(16) | |
| status | ENUM(active, suspended, deactivated) | |
| last_login_at | TIMESTAMP NULL | |
| timestamps | — | |

Indices: `email`, `phone`, `status`.
Soft delete: yes (admin-managed accounts).

### `platforms`

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| public_id | CHAR(26) UNIQUE | Globally unique public identifier (ULID) used in widget config and client-facing URLs; **not** the auto-increment |
| name | VARCHAR(255) | |
| slug | VARCHAR(255) UNIQUE | URL-safe name (open decision) |
| website_url | VARCHAR(2048) | |
| description | TEXT NULL | |
| status | ENUM(pending, active, suspended, deactivated) | |
| created_by | FK → users.id | Owner of the platform |
| timestamps | — | |

Indices: `public_id`, `slug`, `created_by`.

### `platform_admins` (or `platform_user_roles`)

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| platform_id | FK → platforms.id | |
| user_id | FK → users.id | |
| role | ENUM(owner, admin, developer) | Open decision on roles |
| invited_at, accepted_at | TIMESTAMP NULL | |

Unique: (platform_id, user_id).

### `services`

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| key | VARCHAR(50) UNIQUE | e.g., `realtime_chat` |
| name | VARCHAR(255) | |
| description | TEXT | |
| is_active | BOOLEAN | |
| sort_order | INT | |

Pre-seed: `realtime_chat`.

### `plans`

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| service_id | FK → services.id | |
| plan_key | VARCHAR(50) | e.g., `starter` |
| name | VARCHAR(255) | |
| price | DECIMAL(12,2) | |
| currency | CHAR(3) | |
| billing_period | ENUM(monthly, yearly, custom) | |
| billing_interval | INT NULL | For custom periods |
| is_active | BOOLEAN | |
| features | JSON NULL | Feature flags/limits JSON |
| sort_order | INT | |

Unique: (service_id, plan_key).
Indices: `service_id`, `is_active`.

### `subscriptions` (Platform ↔ Service ↔ Plan)

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| platform_id | FK → platforms.id | |
| service_id | FK → services.id | |
| plan_id | FK → plans.id | |
| status | ENUM(pending, active, expired, cancelled, suspended) | |
| starts_at | TIMESTAMP | |
| ends_at | TIMESTAMP NULL | |
| cancelled_at | TIMESTAMP NULL | |
| next_billing_at | TIMESTAMP NULL | |
| auto_renew | BOOLEAN DEFAULT false | |

Indices: (platform_id, service_id, status), `ends_at`.
Unique (partial/application-enforced): at most one active subscription per (platform_id, service_id).
Soft delete: no.

### `payments`

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| subscription_id | FK → subscriptions.id | |
| platform_id | FK → platforms.id | |
| gateway | VARCHAR(50) | e.g., `stripe` |
| gateway_transaction_id | VARCHAR(255) UNIQUE NULL | |
| amount | DECIMAL(12,2) | |
| currency | CHAR(3) | |
| status | ENUM(pending, completed, failed, refunded) | |
| paid_at | TIMESTAMP NULL | |
| metadata | JSON NULL | Gateway payload |

Indices: `subscription_id`, `platform_id`, `status`, `gateway_transaction_id`.

### `platform_service_access` (Entitlement cache)

**Purpose:** Pre-computed entitlement for fast access checks without resolving plan/subscription algebra every request.

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| platform_id | FK → platforms.id | |
| service_id | FK → services.id | |
| has_access | BOOLEAN | |
| effective_until | TIMESTAMP NULL | |
| synced_at | TIMESTAMP | |

Unique: (platform_id, service_id).
This may be maintained by an event listener on subscription status changes rather than derived on each request. **Open decision** on cache-over-compute.

---

## Integration Domain

### `platform_integrations`

One row per external platform (the current migration has no `service_id`). This row currently owns API lifecycle, widget-origin, and external user-search configuration. General endpoint capability, credential, and test-log tables described below are conceptual and are not present in the current schema.

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| platform_id | FK → platforms.id | |
| public_id | CHAR(26) UNIQUE | ULID |
| status | ENUM(pending, active, suspended, deactivated) | |
| paseto_version, token_ttl_seconds, rate_limit_per_minute | config | Platform API authentication |
| base_domain, allowed_origins | VARCHAR / JSON | Browser widget origin policy |
| user_search_endpoint | VARCHAR(2048) NULL | Configured client API URL |
| user_search_auth_type, user_search_auth_header | VARCHAR NULL | Bearer or custom-header mode |
| user_search_auth_secret | TEXT NULL | Laravel-encrypted client API credential |
| user_search_allowed_hosts | JSON NULL | Exact endpoint host allowlist; separate from widget origins |
| last_active_at, revoked_at | TIMESTAMP NULL | Lifecycle metadata |

Unique: (platform_id).

Migration `2026_09_25_000001` adds the search API fields to the existing row; no duplicate integration or credential table was introduced.

### `api_endpoint_configs` (planned; not migrated)

Per-capability endpoint configuration.

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| integration_id | FK → platform_integrations.id | |
| capability | ENUM(current_user, user_details, user_search, chat_permission) | |
| method | VARCHAR(10) | GET/POST |
| url | VARCHAR(2048) | |
| auth_method | ENUM(none, bearer, api_key, custom_header) | |
| auth_header_name | VARCHAR(255) NULL | For custom headers |
| timeout_ms | INT DEFAULT 5000 | |
| request_config | JSON NULL | Headers, query params, body template |
| field_mapping | JSON NULL | External→MyVivahAI field mapping |
| is_required | BOOLEAN | |
| is_configured, is_validated | BOOLEAN | |

Unique: (integration_id, capability).
Indices: `integration_id`.

### `api_credentials` (planned; not migrated)

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| integration_id | FK → platform_integrations.id | |
| credential_type | ENUM(bearer, api_key, basic, custom) | |
| reference | VARCHAR(255) NULL | e.g., header name or label |
| secret_encrypted | TEXT | **Encrypted** secret (Laravel `Crypt`) |
| masked_hint | VARCHAR(32) | e.g., last 4 chars for UI display |
| rotated_at | TIMESTAMP NULL | |
| expires_at | TIMESTAMP NULL | |

Indices: `integration_id`.

### `api_test_logs` (planned; not migrated)

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| integration_id | FK → platform_integrations.id | |
| capability | ENUM(...) | |
| request_summary | JSON | Method, URL, headers (redacted), body |
| response_status | INT NULL | |
| response_body | TEXT NULL | Truncated |
| response_time_ms | INT NULL | |
| validation_result | JSON NULL | Passed fields, missing fields, errors |
| passed | BOOLEAN | |
| run_by | FK → users.id NULL | |
| created_at | TIMESTAMP | |

Indices: `integration_id`, `created_at`.
Retention: open decision (prune old logs).

### `webhook_configs` (Future/service basis)

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| platform_id | FK → platforms.id | |
| event | VARCHAR(50) | e.g., `conversation.created` |
| url | VARCHAR(2048) | |
| secret_key_encrypted | TEXT | For signature verification |
| is_active | BOOLEAN | |
| created_at | TIMESTAMP | |

Unique: (platform_id, event).

---

## Real-Time Chat Domain

> Implemented in Phase 3D (`2026_09_23_000001`–`000003`) + **Phase 3E
> presence columns (`2026_09_23_000004` on `platform_external_user_map`)**. The
> identity map table is the Phase 3A/3C **`platform_external_user_map`** (docs
> section "Integration Domain — identity mapping"); it plays the role that the
> earlier-planned `external_users` table was going to fill, holding ONLY identity
> references (per §15 Data Strategy) — there is no `external_users` table.

### `platform_external_user_map` — presence columns (Phase 3E)

Basic presence is DB-resident (no Redis): the map row owns the online/offline
state + last-heartbeat timestamp, and staleness is always *computed* from
`presence_seen_at` (see `docs/realtime-chat.md` §Presence).

| Column | Type | Notes |
|---|---|---|
| presence_status | ENUM(online, offline) DEFAULT offline | Effective presence; also `ExternalUserMap::PRESENCE_ONLINE/OFFLINE` |
| presence_seen_at | TIMESTAMP NULL | Last heartbeat / transition time; stale reading = offline |
| (existing last_seen_at) | TIMESTAMP NULL | Identity-map "seen" timestamp — reconciled on every heartbeat |

Index: `(platform_id, presence_status)` — `external_user_map_presence_platform`
(per-platform sweep in `chat:presence-sweep`).

### `conversations`

A platform-scoped message thread. Identified to clients by a public ULID; deduplicated
server-side by a deterministic pair key.

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| public_id | CHAR(26) UNIQUE | Public conversation ID (ULID) sent to clients |
| platform_id | FK → platforms.id | Isolation root |
| participant_key | CHAR(64) | Normalized sorted participant-map pair, e.g., `<lowerMapId>:<higherMapId>` (numeric sort) — deterministic resolve |
| status | ENUM(active, archived, closed) DEFAULT active | Lifecycle; NO soft delete (keeps pair key authoritative) |
| last_message_id | BIGINT UNSIGNED NULL | Denormalized; **no FK** (circular conversations↔messages — see migration comment) |
| last_message_at | TIMESTAMP NULL | For sorting conversation list |
| last_message_excerpt | VARCHAR(255) NULL | `Str::limit(messages.content, config('chat.message.excerpt_length'))` |
| last_message_sender_external_user_map_id | BIGINT UNSIGNED NULL | Denormalized sender echo |
| created_at / updated_at | TIMESTAMP | |

Unique: `(platform_id, participant_key)` — `conversations_platform_pair`.
Index: `(platform_id, last_message_at)` — `conversations_platform_recent` (list ordering).
Public id: `conversations_public_id_unique`.

### `conversation_participants`

Membership + per-user read state (unread fast-path lives here so listing is O(1)).

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| conversation_id | FK → conversations.id (CASCADE) | |
| platform_id | FK → platforms.id (CASCADE) | Denormalized tenant |
| external_user_map_id | FK → platform_external_user_map.id (CASCADE) | The participant's identity map |
| last_read_message_id | BIGINT UNSIGNED NULL FK → messages.id (SET NULL) | Read cursor (internal id; opaque to clients) |
| last_read_at | TIMESTAMP NULL | When the cursor was last advanced |
| unread_count | INT UNSIGNED DEFAULT 0 | Display fast-path; incremented on send, reset on read — inside the same transactions |
| joined_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | |
| left_at | TIMESTAMP NULL | |
| created_at / updated_at | TIMESTAMP | |

Unique: `(conversation_id, external_user_map_id)` — `conversation_participants_seat` (one seat per user).
Index: `(platform_id, external_user_map_id)` — `conversation_participants_user_convs` ("list my conversations").
Index: `(conversation_id, last_read_message_id)` — `conversation_participants_read`.

### `messages`

A single chat entry, STRICTLY platform-scoped and idempotent under a client key.

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | Internal PK; used as the cursor in history pagination |
| public_id | CHAR(26) UNIQUE | Public message ID (ULID) |
| platform_id | FK → platforms.id (CASCADE) | Isolation root |
| conversation_id | FK → conversations.id (CASCADE) | |
| sender_external_user_map_id | BIGINT UNSIGNED NULL FK → platform_external_user_map.id (SET NULL) | NULLABLE (MySQL requires it for SET NULL) |
| type | ENUM(text, image, file, system) DEFAULT text | Text at MVP |
| status | ENUM(sent, delivered, read) DEFAULT sent | Delivery lifecycle |
| content | TEXT | |
| client_message_id | CHAR(64) NULL | Idempotency key from the sender widget |
| deleted_at | TIMESTAMP NULL | Soft delete reserved for a future recall scenario (nothing deletes at MVP) |
| created_at / updated_at | TIMESTAMP | |

Unique: `(platform_id, conversation_id, client_message_id)` — `messages_platform_conversation_client`.
Index: `(conversation_id, id)` — `messages_conversation_cursor` (cursor pagination).
Index: `(conversation_id, created_at)` — `messages_conversation_created`.
Index: `sender_external_user_map_id` — `messages_sender_map`.

### `message_statuses`

Audit/tracking of per-recipient status transitions (optional at MVP; recommended for correctness).

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| message_id | FK → messages.id | |
| recipient_user_id | FK → external_users.id | |
| status | ENUM(sent, delivered, read) | |
| changed_at | TIMESTAMP | |

Unique: (message_id, recipient_user_id).

### `message_attachments` (Future consideration)

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| message_id | FK → messages.id | |
| platform_id | FK → platforms.id | |
| storage_path | TEXT | |
| file_name, mime_type, size_bytes | — | |
| public_url | VARCHAR(2048) NULL | |
| created_at | TIMESTAMP | |

Indices: `message_id`.

### `widget_configs`

| Column | Type | Notes |
|---|---|---|
| id | BIGINT UNSIGNED PK | |
| platform_id | FK → platforms.id (unique) | One live config per platform at MVP |
| integration_id | FK → platform_integrations.id | Reference to active integration |
| mode | ENUM(test, live) | |
| branding | JSON | Colors, logo, label, welcome message |
| layout | JSON | Position, offset, z-index, launch behavior |
| features | JSON | toggles: search, presence, attachments |
| script_version | VARCHAR(32) | Version of embed script to serve |
| live_at | TIMESTAMP NULL | |
| updated_at | TIMESTAMP | |

Unique: `platform_id`.
Indices: `platform_id`, `mode`.

---

## Future Domains (not implemented)

Documented conceptually only. **Do not create migrations.**

### `whatsapp_integrations`
WhatsApp Business API connections for delivering chat messages over WhatsApp (future).

### `ai_agents`
Agent definitions (Matrimony AI Agent, Data Entry Agent): provider, model config, prompts, capabilities, platform linkage.

### `ai_conversations`
Conversations between agent and end users / staff, referencing external users and/or MyVivahAI users.

### `ai_usage_tracking`
Token/finegrained usage records per platform/subscription for metered billing.

### `data_entry_jobs`
Job definitions, status, payloads, and results for the Data Entry Agent.

---

## Cross-Domain Relationships

| Relationship | Cardinality |
|---|---|
| users 1—N platforms (owner) | One user can own multiple platforms |
| users N—M platforms (admins via platform_admins) | |
| platforms 1—N subscriptions | |
| services 1—N plans | |
| subscriptions N—1 plans | |
| subscriptions 1—N payments | |
| platforms 1—N platform_integrations | One per service |
| platform_integrations 1—N api_endpoint_configs | One per capability |
| platform_integrations 1—N api_credentials | |
| platform_integrations 1—N api_test_logs | |
| platforms 1—N platform_external_user_map | Identity references (the "external users" domain) |
| platforms 1—N conversations | |
| conversations 1—2 conversation_participants | Exactly two at MVP |
| conversations 1—N messages | |
| platform_external_user_map 1—N messages (as sender) | |
| messages 1—N message_statuses | |
| platforms 1—1 widget_configs (MVP) | |
| platforms 1—1 platform_service_access (per service) | |

---

## Platform Isolation Requirements

Every chat/integration/config table carries a `platform_id` column and is queried with platform context enforced by middleware/service.

Rules:

- `platform_external_user_map`: UNIQUE (platform_id, external_user_id)
- `conversations`: UNIQUE (platform_id, participant_key); always filtered by platform_id
- `conversation_participants`: seat UNIQUE (conversation_id, external_user_map_id); always filtered by platform_id
- `messages`: UNIQUE (platform_id, conversation_id, client_message_id); reachable only through conversations; conversation platform_id must equal caller's platform
- `platform_integrations`: UNIQUE (platform_id, service_id)
- `widget_configs`: UNIQUE platform_id
- `api_credentials/api_endpoint_configs`: only reachable via platform_integrations belonging to the platform

No cross-platform foreign-key paths exist between chat tables of different platforms.

---

## Soft-Delete / Timestamp Considerations

| Table | Soft Delete | Timestamps |
|---|---|---|
| users | Yes | Yes |
| platforms | Yes | Yes |
| services | Yes | Yes |
| plans | Yes | Yes |
| subscriptions | No (status transitions instead) | Yes |
| payments | No (retain financial records) | Yes (paid_at) |
| platform_integrations | Yes | Yes |
| api_endpoint_configs | Yes | Yes |
| api_credentials | Yes | Yes |
| api_test_logs | No (append-only; prune later) | created_at |
| platform_external_user_map | No (persist identity references) | Yes |
| conversations | Yes (archived) | Yes |
| conversation_participants | No | Yes |
| messages | Yes (recall) | Yes |
| message_statuses | No | changed_at |
| widget_configs | Yes | Yes |

---

## Public Identifiers

Public identifiers (ULID strings) are distinct from internal auto-increment PKs:
- `platforms.public_id`
- `conversations.public_id`
- `messages.public_id`

They are used in embed scripts, WebSocket channels, and client HTTP payloads, and they prevent ID enumeration.

---

## Scalability Concerns

| Concern | Mitigation |
|---|---|
| Messages growth | Partition/archive via `created_at`; retention policy; index `(conversation_id, created_at)` |
| Conversation list perf | Denormalized `last_message_at`; paginate via cursor |
| Hot platform contention | Platform-level sharding later (open decision) |
| Unread count recompute | Incremental updates + periodic reconciliation job |
| Search on external users | Backed by client platform's search API (MyVivahAI does not index full user sets) |
| Presence scale | **DB-backed presence (Phase 3E), Redis optional** — `presence_status`/`presence_seen_at` on `platform_external_user_map` + computed staleness + `chat:presence-sweep` cron; a future Redis-backed profile is an optional optimization, not a dependency |
| JSON columns growth | Keep bounded; move to child tables when analytical needs arise |

---

## Open Decisions (Database)

1. Primary key strategy: auto-increment `BIGINT` internal + explicit `ULID` public IDs (recommended) vs ULID-only tables.
2. Whether `platform_service_access` is a cache table synced by events (recommended) or computed on demand.
3. Whether conversation dedup key (`participant_key`) survives multiple architecture revisions — confirm normalization algorithm.
4. JSON vs relational storage for API test results and field mappings.
5. Whether external user cache fields expand beyond name/photo/email/phone.
6. Timezone storage strategy (TIMESTAMP with UTC convention).
7. Database naming/schema strategy if per-platform databases are ever needed (diversion from shared-schema recommended baseline).
