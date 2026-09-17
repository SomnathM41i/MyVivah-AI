# MyVivahAI — Changelog

> Format: Keep a Changelog style (https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

### Added — 2026-09 (Architecture Review)

- Added `docs/architecture-review.md` — pre-bootstrap review of the full documentation set:
  - Confirmed 16 consistent decisions
  - Identified 13 contradictions/inconsistencies, 11 missing requirements
  - Reviewed database design (6 issue groups), API/auth (7), subscription/payment (6), real-time chat (8), security/isolation (7)
  - Provided 15 prioritized corrections and a 21-item approval list
  - Proposed final architecture baseline and adjusted implementation order
  - Verdict: ready for Laravel bootstrap after corrections 10.1–10.3/10.6–10.7 and section-11 approvals
- `docs/README.md` index updated to include the review document.

### Added — 2026-09 (Documentation Foundation)

- Created the documentation foundation for MyVivahAI:
  - `docs/README.md` — documentation index and reading order
  - `docs/product-overview.md` — product vision, services, target users
  - `docs/architecture.md` — system architecture and module boundaries
  - `docs/registration-flow.md` — account registration, verification, login, onboarding
  - `docs/subscription-flow.md` — plans, payments, subscription lifecycle, entitlements
  - `docs/api-integration.md` — client API integration dashboard flow
  - `docs/api-contract.md` — external API capabilities and contracts
  - `docs/widget-integration.md` — widget configuration, installation, testing
  - `docs/realtime-chat.md` — chat architecture, delivery, presence, authorization
  - `docs/database.md` — conceptual database design for all domains
  - `docs/security.md` — security requirements and threat considerations
  - `docs/platform-isolation.md` — multi-platform isolation strategy
  - `docs/laravel-architecture.md` — recommended Laravel structure and conventions
  - `docs/future-services.md` — extension model for future AI services
  - `docs/current-tasks.md` — task list (no tasks completed beyond docs)
  - `docs/changelog.md` — this changelog
  - `docs/known-issues.md` — open uncertainties and decisions to confirm
  - `docs/decisions.md` — Architecture Decision Records

**Project status:** Documentation only. No application code has been written, no migrations exist, and the Laravel project has not yet been initialized in the workspace.