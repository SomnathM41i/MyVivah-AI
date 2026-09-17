# MyVivahAI — Future Services

## Overview

MyVivahAI is designed so that additional services — such as the Matrimony AI Agent and the Data Entry Agent — can be added later without restructuring the chat module or the core platform.

**None of these services are being implemented now.** This document defines the extension model and the requirements they must respect when designed.

---

## Extension Model

Each service is a cohesive module with:

1. A row in `services`
2. A set of `plans`
3. A subscription-gating rule (EntitlementService per service)
4. Its own dashboard sections (guarded by per-service entitlement middleware)
5. Its own external API integration capabilities (per-service `api_endpoint_configs`)
6. Its own data tables (platform-scoped)
7. Optional widget(s)/channels as appropriate

Adding a service must not require changes to:

- The chat module's code paths
- Core platform data model (beyond adding `services` rows and its own tables)
- The multi-platform isolation layer (it is reused)

---

## Design Rules for Future Services

| Rule | Detail |
|---|---|
| Platform isolation inherited | All future service tables include `platform_id`; reuse `PlatformContext` |
| Entitlements per service | New service access checks use `EnsurePlatformAccess:{service_key}` |
| Separate service catalog | New service added to `services` with its own `plans` |
| Separate integration contracts | Future services define their own integration capabilities and field mappings |
| No cross-service dependencies | Chat must not depend on future services; future services may call shared primitives (entitlement, platform context, widget infrastructure) |
| Reuse widget infra where possible | Chat widget framework can host additional panels for AI features later |
| Independent rollout | Each future service can be released, priced, and versioned independently |
| No hardcoded service keys | Service references go through the `services` table / config, never scattered booleans |

---

## Future Service Profiles

### Matrimony AI Agent

**Purpose (future):** AI assistant helping end users with match discovery, profile guidance, conversation starters, compatibility insights, etc.

**Open requirements (to be defined):**
- Which AI provider(s) and models
- Prompt/agent configuration storage per platform
- Whether it interacts via the chat widget as an assistant/participant
- Usage metering (tokens/messages) and pricing
- Guardrails and moderation policies
- Data handling of matrimony profile data passed to AI provider (privacy/compliance)
- Whether the client's user permissions API gates the agent

**Likely tables (concept only, not created):**
- `ai_agents` (config per platform+service+agent)
- `ai_conversations`
- `ai_usage_tracking`

### Data Entry Agent

**Purpose (future):** Automate data entry workflows for client platforms (e.g., populating profiles, forms, bulk updates from documents/Excel).

**Open requirements (to be defined):**
- Job definition format (source type, mapping template)
- File/document upload handling and storage
- Human-in-the-loop review workflow
- Notifications of job progress/errors
- Pricing model

**Likely tables (concept only, not created):**
- `data_entry_jobs`
- `data_entry_results`
- `file_uploads`

### WhatsApp Integration

**Purpose (future):** Deliver chat messages over WhatsApp Business API, bridging the chat widget and WhatsApp.

**Not in current scope.** Requirements (channel linking, number ownership, message templates, opt-in handling) to be defined later.

---

## What Must NOT Change When Adding a Service

- `database.md` future domain section will be expanded only when requirements are approved.
- The chat tables and code remain untouched unless a new service truly requires an additive feature.
- No migration that alters existing chat/platform tables strictly for a future service should run before that service is designed.

---

## Milestone Gate

A future service enters design/planning only when:

1. Business requirements are documented.
2. Plan/price definition is approved.
3. Data requirements are added to database.md.
4. Security considerations are reviewed.
5. Entitlements are wired via the existing service model.

---

## Open Decisions

1. Which AI provider(s)/models are used for AI services.
2. Whether AI agents act as participants inside chat conversations (same domain) or separate surfaces.
3. Usage-based pricing mechanics (tokens vs requests vs flat).
4. Whether the Data Entry Agent requires human approval workflow (likely yes).
5. Privacy/compliance constraints around sending matrimony data to third-party AI providers.