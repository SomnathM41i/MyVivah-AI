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
| [api-contract.md](api-contract.md) | External API capabilities, request/response contracts, and authentication |
| [widget-integration.md](widget-integration.md) | Widget configuration, installation, authentication, and testing |
| [realtime-chat.md](realtime-chat.md) | Chat architecture, conversations, messages, WebSockets, and presence |
| [database.md](database.md) | Conceptual database design, all domains, relationships, and constraints |
| [security.md](security.md) | Security requirements, threat model, and protection strategies |
| [platform-isolation.md](platform-isolation.md) | Multi-platform data isolation, scoping rules, and enforcement |
| [laravel-architecture.md](laravel-architecture.md) | Recommended Laravel application structure and conventions |
| [future-services.md](future-services.md) | How Matrimony AI Agent, Data Entry Agent, and other services integrate |
| [architecture-review.md](architecture-review.md) | Pre-bootstrap architecture review with issues, corrections, and approvals |
| [AGENTS.md](AGENTS.md) | Agent/developer instructions: terminology, flows, coding and documentation rules |
| [current-tasks.md](current-tasks.md) | Active development task list |
| [changelog.md](changelog.md) | Project changelog |
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
12. **laravel-architecture.md** — Understand code organization
13. **future-services.md** — Understand the roadmap
14. **decisions.md** — Review key architectural decisions
15. **known-issues.md** — Review open questions
16. **current-tasks.md** — Check what is being worked on

---

## Project Status

- **Phase:** Documentation Foundation
- **Laravel project initialized:** No (pending)
- **Database migrations created:** No
- **Features implemented:** None

---

## Conventions Used in These Documents

- **Confirmed requirement** — A requirement that has been finalized and should be implemented as described.
- **Recommended approach** — A suggested solution that is preferred but not yet confirmed as the final decision.
- **Open decision** — A question or choice that must be resolved before implementation.
- **Future consideration** — Something that may be needed later but is not required now.
- **Do not use "multi-tenant"** in product-facing terminology. Use "multi-platform," "client platform," "registered platform," "platform-specific data," or "platform account" instead.
