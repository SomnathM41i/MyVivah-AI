# MyVivahAI — System Architecture

## Overview

MyVivahAI is built as a Laravel application backed by MySQL, Redis, and WebSocket infrastructure. The system is organized into distinct modules that correspond to product domains.

---

## High-Level Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                     EXTERNAL PLATFORMS                       │
│  (Core PHP / Laravel / React / Node.js / Custom Systems)    │
│                                                              │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐                   │
│  │ Website  │  │ Mobile   │  │ Backend  │                   │
│  │ (Widget) │  │ App      │  │ (APIs)   │                   │
│  └────┬─────┘  └────┬─────┘  └────┬─────┘                   │
└───────┼──────────────┼──────────────┼────────────────────────┘
        │              │              │
        │  HTTPS       │  HTTPS       │  HTTPS
        ▼              ▼              ▼
┌─────────────────────────────────────────────────────────────┐
│                     MYVIVAH AI PLATFORM                      │
│                                                              │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │                    LOAD BALANCER                         │ │
│  └───────────────────────┬─────────────────────────────────┘ │
│                          │                                    │
│  ┌───────────────────────┼─────────────────────────────────┐ │
│  │              LARAVEL APPLICATION                         │ │
│  │                                                          │ │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │ │
│  │  │  Core        │  │  Auth &      │  │  Platform    │  │ │
│  │  │  Platform    │  │  Account     │  │  Management  │  │ │
│  │  │  Module      │  │  Module      │  │  Module      │  │ │
│  │  └──────────────┘  └──────────────┘  └──────────────┘  │ │
│  │                                                          │ │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │ │
│  │  │  Service &   │  │  Payment &   │  │  Widget      │  │ │
│  │  │  Subscription│  │  Billing     │  │  Management  │  │ │
│  │  │  Module      │  │  Module      │  │  Module      │  │ │
│  │  └──────────────┘  └──────────────┘  └──────────────┘  │ │
│  │                                                          │ │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │ │
│  │  │  API         │  │  Real-Time   │  │  Future      │  │ │
│  │  │  Integration │  │  Chat        │  │  AI Services │  │ │
│  │  │  Module      │  │  Module      │  │  Module      │  │ │
│  │  └──────────────┘  └──────────────┘  └──────────────┘  │ │
│  └─────────────────────────────────────────────────────────┘ │
│                          │                                    │
│  ┌───────────────────────┼─────────────────────────────────┐ │
│  │                  INFRASTRUCTURE                           │ │
│  │                                                          │ │
│  │  ┌────────────┐  ┌────────────┐  ┌──────────────────┐  │ │
│  │  │   MySQL    │  │   Redis    │  │  WebSocket       │  │ │
│  │  │   Database │  │   Cache &  │  │  Server          │  │ │
│  │  │            │  │   Queue    │  │  (Laravel        │  │ │
│  │  │            │  │            │  │   Reverb/Pusher) │  │ │
│  │  └────────────┘  └────────────┘  └──────────────────┘  │ │
│  │                                                          │ │
│  │  ┌────────────┐  ┌────────────┐  ┌──────────────────┐  │ │
│  │  │  File      │  │  Queue     │  │  Scheduler       │  │ │
│  │  │  Storage   │  │  Workers   │  │                  │  │ │
│  │  └────────────┘  └────────────┘  └──────────────────┘  │ │
│  └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

---

## Module Boundaries

Each module is a logical grouping of models, controllers, services, events, and jobs related to a specific domain. Modules communicate through well-defined interfaces (events, service classes) and share the database through explicit foreign keys.

### Core Platform Module

**Responsibility:** Account management, authentication, email/phone verification, session handling, password management, profile settings.

**Does not handle:** Platform-specific logic, service access, payments, or integration details.

### Platform Management Module

**Responsibility:** Registering external platforms, managing platform metadata, storing platform-level configurations, platform status lifecycle.

**Does not handle:** User data from external platforms, chat data, or widget configuration.

### Service & Subscription Module

**Responsibility:** Defining available services, defining plans per service, managing subscriptions, controlling access based on subscription status, handling subscription lifecycle (create, renew, cancel, expire).

**Does not handle:** Payment processing directly; delegates to the Payment module.

### Payment & Billing Module

**Responsibility:** Processing payments, verifying payment status, generating invoices (future), handling refunds (future), storing payment records.

**Does not handle:** Subscription access logic; notifies the Subscription module upon payment confirmation.

### API Integration Module

**Responsibility:** Managing API configurations per platform per service, storing API credentials, configuring endpoint URLs, mapping response fields, running API tests, logging test results.

**Does not handle:** Making the actual calls to external APIs from the widget; that happens on the widget side.

### Widget Management Module

**Responsibility:** Generating widget embed scripts, managing widget configurations (appearance, behavior, positioning), handling widget authentication tokens, tracking widget installation status.

### Real-Time Chat Module

**Responsibility:** Managing conversations between external users, sending and receiving messages via WebSockets, handling presence (online/offline), managing unread counts, message status tracking, conversation search, and message history.

### Future AI Services Module (Placeholder)

**Responsibility:** Not implemented now. Will handle Matrimony AI Agent, Data Entry Agent, and other AI services when requirements are defined.

---

## Communication Patterns

### Synchronous (HTTP)

- Client dashboard requests (authentication, configuration, testing)
- API integration test requests
- Widget configuration saves

### Asynchronous (Queue Jobs)

- Email sending (verification, notifications)
- Payment verification callbacks
- API test execution (background)
- Subscription expiration checks
- Scheduled cleanup tasks

### Real-Time (WebSockets)

- Chat message delivery
- Typing indicators (future)
- Online/offline presence
- Unread count updates

---

## Technology Stack

| Component | Technology | Status |
|---|---|---|
| Backend Framework | Laravel (latest stable) | Pending project initialization |
| PHP Version | 8.2+ | Pending |
| Database | MySQL 8.0+ | Recommended |
| Cache | Redis | Recommended |
| Queue | Redis (via Laravel Queue) | Recommended |
| WebSocket | Laravel Reverb (self-hosted) | **Resolved** — ADR-010 |
| File Storage | Local / S3-compatible | Open decision |
| Frontend (Dashboard) | Blade + Alpine.js or Livewire | Open decision |
| Frontend (Widget) | Vanilla JS or lightweight framework | Open decision |
| Deployment | Docker / Laravel Forge / Envoyer | Open decision |

---

## Request Lifecycle for Key Flows

### External Platform User Sends a Chat Message

```
1. Widget detects user action (type message, press send)
2. Widget sends WebSocket message to MyVivahAI with:
   - Platform identification
   - Authenticated user token
   - Conversation ID
   - Message content
3. MyVivahAI WebSocket server receives the message
4. Server verifies the token (platform + user identity)
5. Server verifies the user is a participant in the conversation
6. Server verifies the conversation belongs to the authenticated platform
7. Server persists the message to MySQL
8. Server broadcasts the message to conversation participants via WebSocket
9. Server updates unread counts
10. Server confirms delivery to sender
```

### Client Platform Configures API Integration

```
1. Platform owner opens API Integration in MyVivahAI dashboard
2. Dashboard displays required API capabilities
3. Platform owner enters endpoint URLs, auth details, headers, field mappings
4. Platform owner clicks "Test"
5. MyVivahAI sends test requests to the configured endpoints
6. MyVivahAI validates the responses against expected format
7. Dashboard displays results (success/failure with details)
8. Platform owner fixes issues and re-tests
9. After successful tests, platform owner activates the integration
10. Integration is marked as active; widget can now be configured
```

---

## Open Decisions

- Exact WebSocket server choice (Laravel Reverb, Pusher, Soketi, custom). → **Resolved**: Laravel Reverb (ADR-010).
- Whether to use Blade/Livewire or a separate SPA (Vue/React) for the client dashboard.
- Whether to use a monolithic Laravel app or modular package structure.
- File storage strategy (local vs S3-compatible).
- Queue driver for production (Redis vs SQS vs database).
- Deployment target (cloud provider, containerization).
