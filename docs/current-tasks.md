# MyVivahAI — Current Tasks

> Status legend: `[x]` done · `[ ]` pending · `[~]` in progress

This list is a living document. Update it as work proceeds. Do **not** mark tasks as completed unless they are actually completed (code written, tests passing, PR merged).

---

## Phase 0 — Documentation Foundation

- [x] Create documentation directory and file set (`docs/`)
- [x] Document product overview and business model
- [x] Document system architecture and module boundaries
- [x] Document registration flow
- [x] Document subscription flow and entitlements
- [x] Document API integration dashboard flow
- [x] Document external API contract
- [x] Document widget integration flow
- [x] Document real-time chat internals
- [x] Document conceptual database design
- [x] Document security requirements
- [x] Document platform isolation strategy
- [x] Document recommended Laravel architecture
- [x] Document future services extension model
- [ ] Finalize open decisions listed in `decisions.md` and `known-issues.md`

---

## Phase 1 — Project Bootstrap (Recommended Next)

- [ ] Initialize Laravel project (`composer create-project laravel/laravel`)
- [ ] Set up environment config (.env, .env.example)
- [ ] Configure MySQL connection
- [ ] Configure Redis (cache, queue, session)
- [ ] Configure `.env.example` and local Docker (optional)
- [ ] Set up base test suite and CI config
- [ ] Establish code style tooling (Pint) and static analysis (PHPStan) config

---

## Phase 2 — Core Platform Domain

- [ ] Create `users`, `platforms`, `services`, `plans`, `subscriptions`, `payments` migrations
- [ ] Implement account registration + email verification
- [ ] Implement login/logout + session management
- [ ] Implement platform creation/onboarding
- [ ] Seed `services` catalog (Real-Time Chat)
- [ ] Seed initial `plans` (pricing to be defined — pending decision)
- [ ] Implement EntitlementService + `EnsurePlatformAccess` middleware
- [ ] Implement subscription lifecycle (activate/expire/cancel/suspend)
- [ ] Implement payment integration (gateway decision pending)

---

## Phase 3 — API Integration Module

- [ ] Implement `platform_integrations`, `api_endpoint_configs`, `api_credentials`, `api_test_logs` migrations
- [ ] Implement API Integration dashboard (per-capability configuration)
- [ ] Implement field mapping UI
- [ ] Implement external API client (auth, timeout, redacted logging)
- [ ] Implement integration test runner (queued job)
- [ ] Implement validation of responses against contract
- [ ] Implement integration activation flow

---

## Phase 4 — Widget Module

- [ ] Implement `widget_configs` migration
- [ ] Implement widget configuration UI (branding/layout/features)
- [ ] Implement embed script generation service
- [ ] Implement test/live modes
- [ ] Build widget frontend (floating button, panel, conversation list, chat view)
- [ ] Implement widget identity token bootstrap
- [ ] Implement CSS/JS isolation (Shadow DOM or scoped)
- [ ] Implement widget test checklist UI

---

## Phase 5 — Real-Time Chat Module

- [ ] Implement `external_users`, `conversations`, `conversation_participants`, `messages`, `message_statuses` migrations
- [ ] Implement token authentication endpoint (verify identity token → socket session)
- [ ] Implement conversation create/open/list
- [ ] Implement message send/persist (with idempotency)
- [ ] Implement message history pagination
- [ ] Implement unread counts
- [ ] Integrate WebSocket broadcasting (Reverb/Pusher — decision pending)
- [ ] Implement private channel authorization
- [ ] Implement presence (Redis-based)
- [ ] Implement search (via client's search API)
- [ ] Implement cross-platform isolation tests

---

## Phase 6 — Hardening & Launch

- [ ] Rate limiting on all sensitive endpoints
- [ ] Audit logging
- [ ] Backup procedures and recovery runbook
- [ ] Monitoring/alerting
- [ ] Production deployment pipeline
- [ ] End-to-end test with a sample client platform
- [ ] Live pilot with a pilot matrimony platform

---

## Backlog (Not Scheduled)

- [ ] Matrimony AI Agent (defined in future-services.md)
- [ ] Data Entry Agent
- [ ] WhatsApp integration
- [ ] Message attachments
- [ ] Typing indicators / read receipts
- [ ] Group conversations (decision pending)
- [ ] Widget analytics dashboard