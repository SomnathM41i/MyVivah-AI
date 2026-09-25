# MyVivahAI Documentation

This directory contains the complete product, architecture, and development documentation for the MyVivahAI platform.

**Purpose:** Ensure that developers, AI coding agents, and stakeholders clearly understand the product, architecture, business flows, database design, API integration, widget integration, security model, and development rules before writing production code.

---

## Documentation Index

| Document | Description |
|---|---|
| [product-overview.md](product-overview.md) | Product vision, services, target users, and overall business model |
| [architecture.md](architecture.md) | System architecture, module boundaries, and technology decisions |
| [registration-flow.md](registration-flow.md) | Account creation, verification, login, and platform onboarding |
| [subscription-flow.md](subscription-flow.md) | Service selection, plans, payments, and subscription lifecycle |
| [api-integration.md](api-integration.md) | Client-facing API integration dashboard and configuration process |
| [integration-api.md](integration-api.md) | MyVivahAI server-side v1 API reference for client platforms (auth, identity, keys, chat, rate limits, errors) |
| [api-contract.md](api-contract.md) | External API capabilities, request/response contracts, and authentication |
| [external-user-search-v1.md](external-user-search-v1.md) | Versioned external user-search contract and security limits |
| [../examples/core-php/README.md](../examples/core-php/README.md) | Core PHP/PDO sample integration and widget-session bootstrap |
| [widget-integration.md](widget-integration.md) | Widget configuration, installation, authentication, and testing — incl. **Phase 4 implementation** (session bootstrap, widget API, client JS, demo) |
| [realtime-chat.md](realtime-chat.md) | Chat architecture, conversations, messages, REST API (Phase 3D), realtime broadcast/presence/channel auth (Phase 3E), and presence |
| [deployment.md](deployment.md) | Deployment guide — shared-hosting REST-only default, optional realtime (Reverb/Soketi/Pusher), cron, enable steps |
| [database.md](database.md) | Conceptual database design, all domains, relationships, and constraints |
| [phase-5a-public-website-auth.md](phase-5a-public-website-auth.md) | **Phase 5A — public website (Blade), web auth (signup/login/logout/forgot/reset/verification), dashboard app shell, contact inbox** — implementation report + stack deviation note |
| [phase-2d-factories-seeders.md](phase-2d-factories-seeders.md) | Phase 2D — factories + seeders (8 factories, 5 seeders), idempotent demo seed + exit verification |

| [phase-3a-api-integration-plan.md](phase-3a-api-integration-plan.md) | Phase 3A — API Integration Module plan: PASETO v4.local auth, token lifecycle, platform-scoped API authorization, external user mapping/sync, v1 endpoint contracts, versioning, validation/errors, rate limiting, audit, isolation, tests, impl order + exit criteria — **approved; Phase 3B-1 (PASETO auth foundation), 3B-2 (key lifecycle/audit/identity), 3C (integration API + per-platform rate limiting), 3D (chat REST + DB foundation) and 3E (realtime broadcast + presence + channel auth) implemented and verified** |
| [phase-2a-schema-plan.md](phase-2a-schema-plan.md) | Core Platform Domain schema plan (Phase 2A) — authoritative contract for Phase 2B migrations |
| [security.md](security.md) | Security requirements, threat model, and protection strategies |
| [platform-isolation.md](platform-isolation.md) | Multi-platform data isolation, scoping rules, and enforcement |
| [laravel-architecture.md](laravel-architecture.md) | Recommended Laravel application structure and conventions |
| [future-services.md](future-services.md) | How Matrimony AI Agent, Data Entry Agent, and other services integrate |
| [architecture-review.md](architecture-review.md) | Pre-bootstrap architecture review with issues, corrections, and approvals |
| [AGENTS.md](AGENTS.md) | Agent/developer instructions: terminology, flows, coding and documentation rules |
| [current-tasks.md](current-tasks.md) | Active development task list |
| [changelog.md](changelog.md) | Project changelog |
| [bootstrap.md](bootstrap.md) | Phase 1 — project bootstrap: environment, versions, files, commands, validation, open decisions |
| [known-issues.md](known-issues.md) | Uncertainties, missing requirements, and unresolved questions |
| [decisions.md](decisions.md) | Architecture Decision Records (ADRs) for key technical choices |

---

## Reading Order for New Developers

1. **product-overview.md** — Understand what MyVivahAI is
2. **architecture.md** — Understand how the system is structured
3. **database.md** — Understand the data model
4. **platform-isolation.md** — Understand how platforms are isolated
5. **security.md** — Understand security requirements
6. **registration-flow.md** — Understand user onboarding
7. **subscription-flow.md** — Understand the payment and access model
8. **api-contract.md** — Understand external integration contracts
9. **api-integration.md** — Understand the client integration dashboard
10. **widget-integration.md** — Understand the widget lifecycle
11. **realtime-chat.md** — Understand the chat service internals
12. **deployment.md** — Understand hosting tiers, cron, and enabling realtime
13. **laravel-architecture.md** — Understand code organization
14. **future-services.md** — Understand the roadmap
15. **decisions.md** — Review key architectural decisions
16. **known-issues.md** — Review open questions
17. **current-tasks.md** — Check what is being worked on
18. **phase-5a-public-website-auth.md** — Report on the public site + web auth + app shell (incl. Blade-vs-Inertia decision)

---

## Project Status

- **Phase:** 1 — Project Bootstrap (complete); 2A — Core Platform Schema Plan (complete); 2B — Core Platform Migrations (complete, verified); 2C — Eloquent Models (complete, verified — 8/8 models); **2D — Factories & Seeders (complete, verified — 8 factories + 5 seeders, idempotent)**; **3B-1 — PASETO Auth (complete, verified); 3B-2 — API Integration Foundation (complete, verified); 3C — Integration API & Rate Limiting (complete, verified); 3D — Chat API Foundation (complete, verified); 3E — Realtime Messaging (complete, verified — broadcast + DB-backed presence + channel authorization, optional/reverb-ready); 4 — Embeddable Widget (complete, verified — session bootstrap, widget API, client JS, CORS, demo); 5A — Public Website + Web Auth + App Shell (complete, verified — Blade marketing site, signup/login/logout/forgot/reset/email-verification, read-only dashboard shell, contact inbox)**
- **Laravel project initialized:** Yes — Laravel 12.69.2 (bootstrap.md)
- **Database migrations created:** 20 pre-existing migrations plus the additive user-search integration configuration migration (21 total in this checkout). Existing data has not been migrated or changed in this task.
- **Eloquest models:** All 8 Phase-2C models authored (`app/Models/*`) + integration/chat models (`PlatformIntegration`, `PlatformApiKey`, `ApiAuditLog`, `ExternalUserMap`, `Conversation`, `ConversationParticipant`, `Message`); ULID `public_id` + soft-delete matrix + casts + relationships per plan; Pint-clean, PHPStan clean, test suite green
- **Chat milestone additions:** Widget search now delegates to the configured client API (`GET /api/v1/widget/users/search`), authenticates server-side with encrypted per-platform credentials, and uses short-lived candidate tokens to create conversations for unmapped recipients. A basic search-configuration form exists. Realtime defaults off; Hostinger delivery is unverified. Connection testing, embed generator/config record, backups and restore validation remain incomplete.
- **Schema plan:** `docs/phase-2a-schema-plan.md` — approved; served as the authoritative 1:1 contract, implemented by the Phase 2B migrations

---

## Conventions Used in These Documents

- **Confirmed requirement** — A requirement that has been finalized and should be implemented as described.
- **Recommended approach** — A suggested solution that is preferred but not yet confirmed as the final decision.
- **Open decision** — A question or choice that must be resolved before implementation.
- **Future consideration** — Something that may be needed later but is not required now.
- **Do not use "multi-tenant"** in product-facing terminology. Use "multi-platform," "client platform," "registered platform," "platform-specific data," or "platform account" instead.
