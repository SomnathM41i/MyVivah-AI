# MyVivahAI — AGENTS.md

## 1. Project Overview

MyVivahAI is a modular, multi-platform SaaS application that provides communication, API integration, and AI-powered services to external platforms, primarily matrimony websites.

The platform is designed to allow businesses using different technologies—Core PHP, Laravel, React, Node.js, or other stacks—to register, subscribe to services, integrate APIs, and use MyVivahAI features through APIs and embeddable widgets.

MyVivahAI is the main platform. Individual products and services operate as modules within it.

### Planned Services

1. Real-Time Chatting Platform
2. Matrimony AI Agent
3. Data Entry Agent
4. Additional AI and automation services in the future

### Current Development Priority

The first service being developed is:

**Real-Time Chatting Platform**

Do not implement future AI-agent or data-entry functionality unless explicitly requested. However, the architecture must allow these services to be added without major changes to the existing core.

---

## 2. Core Product Concept

MyVivahAI works as a service provider for external platforms.

Example:

```text
External Matrimony Platform
        │
        │ API Integration + Chat Widget
        ▼
MyVivahAI
        │
        ├── Real-Time Chat Service
        ├── Matrimony AI Agent (Future)
        ├── Data Entry Agent (Future)
        └── Other Services (Future)
```

Each external platform may use a different technology stack, database structure, and user-management system.

MyVivahAI must not assume that every external platform uses Laravel, PHP, MySQL, or any particular framework.

---

## 3. Important Terminology

Use the following terminology consistently.

| Term                  | Meaning                                                                                                      |
| --------------------- | ------------------------------------------------------------------------------------------------------------ |
| MyVivahAI             | The main SaaS platform                                                                                       |
| Platform              | An external business website or application using MyVivahAI                                                  |
| Service               | A product offered by MyVivahAI                                                                               |
| Real-Time Chat        | The first service being developed                                                                            |
| Platform Account      | The account created by a business on MyVivahAI                                                               |
| External User         | A user belonging to the client's own platform                                                                |
| Widget                | The embeddable chat interface installed on an external website                                               |
| Integration           | The connection between MyVivahAI and an external platform                                                    |
| Subscription          | A purchased service plan                                                                                     |
| Tenant / Multi-Tenant | Do not use this terminology in product-facing language. Use "multi-platform" or "platform-specific" instead. |

Internally, database isolation concepts may still be implemented using appropriate technical patterns, but user-facing terminology should use **platform**.

---

## 4. Technology Stack

### Backend

- Laravel
- PHP
- MySQL

### Frontend

Use the frontend technology selected for the project. Keep the architecture modular and maintainable.

### Real-Time Communication

Use a scalable real-time communication architecture, such as:

- WebSockets
- Laravel Reverb, Soketi, or another compatible WebSocket solution
- Redis where required for queues, broadcasting, caching, or scaling

The final real-time stack must be decided based on the actual project configuration. Do not introduce unnecessary infrastructure without a clear requirement.

### General Principles

- Use Laravel conventions wherever practical.
- Use migrations for database changes.
- Use Eloquent models and relationships.
- Use Form Requests for validation.
- Use Policies and Gates for authorization.
- Use Services for business logic.
- Use Events and Listeners where appropriate.
- Use Jobs and Queues for asynchronous tasks.
- Use API Resources for consistent API responses.
- Keep controllers thin.
- Avoid unnecessary complexity.

---

## 5. Architecture Principles

### 5.1 Modular Design

The system must be designed as a modular SaaS platform.

The Real-Time Chat service should not be tightly coupled to future AI-agent services.

Recommended conceptual structure:

```text
MyVivahAI
│
├── Core
│   ├── Authentication
│   ├── Platform Management
│   ├── Service Management
│   ├── Plans
│   ├── Subscriptions
│   ├── Billing
│   └── Platform Access Control
│
├── Real-Time Chat
│   ├── Chat Configuration
│   ├── External Users
│   ├── Conversations
│   ├── Messages
│   ├── Attachments
│   ├── Presence
│   └── Widget Integration
│
├── API Integration
│   ├── API Credentials
│   ├── API Configuration
│   ├── API Testing
│   ├── API Logs
│   └── Webhooks
│
├── WhatsApp Integration
│   └── Future / Separate Module
│
└── AI Services
    ├── Matrimony AI Agent
    └── Data Entry Agent
```

The exact directory structure may vary, but the separation of responsibilities must be maintained.

---

## 6. Platform Registration Flow

MyVivahAI must support a central registration process.

### Expected Flow

```text
User visits MyVivahAI
        │
        ▼
Creates Account
        │
        ▼
Verifies Email / Phone
        │
        ▼
Logs In
        │
        ▼
Views Available Services
        │
        ▼
Selects a Service
        │
        ▼
Views Service Plans
        │
        ▼
Purchases a Plan
        │
        ▼
Subscription Activated
        │
        ▼
Accesses Service Dashboard
```

### Registration Requirements

- Account registration
- Email verification and/or phone verification
- Secure password handling
- Login and logout
- Password reset
- Account status management
- Platform/business information
- Terms and privacy acceptance where required

Do not assume that registering on MyVivahAI automatically registers users on the external matrimony platform. These are separate systems.

---

## 7. Service Selection and Subscription Flow

MyVivahAI will offer multiple services.

A user should first register on the main platform and then select the service they want to use.

### Expected Flow

```text
MyVivahAI Account
        │
        ▼
Available Services
        │
        ├── Real-Time Chat
        ├── Matrimony AI Agent
        └── Data Entry Agent
```

When a user selects a service:

1. Display the service details.
2. Display available plans for that service.
3. Allow the user to select a plan.
4. Process payment.
5. Confirm payment status.
6. Activate the subscription only after successful payment verification.
7. Grant access to the relevant service dashboard.
8. Allow the user to manage the service according to the subscription.

### Important Rules

- Plans must be associated with a specific service.
- A subscription must belong to a platform account.
- Subscription status must be tracked.
- Payment status must be tracked separately from subscription status.
- Expired or cancelled subscriptions must not retain unauthorized access.
- Service access should be controlled through subscription entitlements.
- Do not hardcode service access checks throughout the application.

---

## 8. Real-Time Chat Service

Real-Time Chat is the first active service in MyVivahAI.

The service allows external platforms to integrate a chat widget into their websites.

### Main Concept

```text
External Matrimony Website
        │
        ├── Existing User Database
        ├── Existing Login / Session
        └── MyVivahAI Chat Widget
                    │
                    ▼
              MyVivahAI APIs
                    │
                    ▼
             Real-Time Chat System
                    │
                    ▼
          Platform-Specific Conversations
```

MyVivahAI does not need to copy the complete user database of the external platform.

The external platform remains the source of truth for its user profile information.

---

## 9. External Platform API Integration

Each external platform must integrate specific APIs to allow MyVivahAI to work with its users.

The external platform may be built using Core PHP, Laravel, React, Node.js, or another technology.

The integration must be technology-independent.

### Required API Capabilities

The external platform should provide APIs for:

1. User Authentication / Identity Verification
2. Fetching User Details
3. Searching Users
4. Optional Chat Permission Validation

The exact API contract must be documented and validated through the MyVivahAI API testing environment.

---

## 10. External User Identification

The external platform already has its own logged-in user and session.

MyVivahAI must identify that user securely.

### Recommended Approach

Do not rely on sending only a raw user ID from the browser.

The external platform should generate a short-lived, signed authentication token for the logged-in user.

Example flow:

```text
User Logs In to External Platform
        │
        ▼
External Platform Reads Its Own Session
        │
        ▼
External Platform Generates Signed Token
        │
        ▼
Widget Sends Token to MyVivahAI
        │
        ▼
MyVivahAI Verifies Token
        │
        ▼
MyVivahAI Identifies External User
        │
        ▼
Chat Widget Loads
```

### Token Requirements

- Token must be generated by the external platform's backend.
- Token must be signed securely.
- Token must have an expiration time.
- Token must identify the external platform.
- Token must identify the external user.
- Token must not expose sensitive information unnecessarily.
- MyVivahAI must validate the token before allowing access.
- Never trust a user ID supplied directly by an untrusted browser request.

### Example Conceptual Token Payload

```json
{
  "platform_id": "client_platform_123",
  "external_user_id": "4582",
  "issued_at": 1720000000,
  "expires_at": 1720000300
}
```

This is only a conceptual example. The actual implementation should use a secure signing and verification mechanism.

---

## 11. Required External Platform API Contract

The integration dashboard should explain exactly what APIs the client must provide.

### API 1: Identity / Current User

Purpose: Verify the logged-in user and return the user's external ID.

Example:

```http
GET /api/myvivahai/auth/user
Authorization: Bearer {token}
```

Example response:

```json
{
  "success": true,
  "data": {
    "user_id": "4582"
  }
}
```

### API 2: User Details

Purpose: Fetch profile details for a specific external user.

Example:

```http
GET /api/myvivahai/users/{user_id}
Authorization: Bearer {token}
```

Example response:

```json
{
  "success": true,
  "data": {
    "user_id": "4582",
    "name": "Example User",
    "email": "user@example.com",
    "phone": "+91XXXXXXXXXX",
    "profile_photo": "https://example.com/photo.jpg"
  }
}
```

Only the fields required by the integration should be returned.

### API 3: User Search

Purpose: Search users from the external platform.

Example:

```http
GET /api/myvivahai/users/search?q=example
Authorization: Bearer {token}
```

Example response:

```json
{
  "success": true,
  "data": [
    {
      "user_id": "4582",
      "name": "Example User",
      "profile_photo": "https://example.com/photo.jpg"
    }
  ]
}
```

### API 4: Chat Permission Check

This API is optional depending on the integration design.

Purpose: Allow the external platform to determine whether two users are permitted to communicate.

Example:

```http
POST /api/myvivahai/chat/permission
Authorization: Bearer {token}
```

Example request:

```json
{
  "sender_id": "4582",
  "receiver_id": "9012"
}
```

Example response:

```json
{
  "success": true,
  "allowed": true
}
```

### Important

The exact API names, authentication method, request fields, and response structure must be finalized before implementation.

The API testing environment should help clients identify:

- Missing parameters
- Invalid credentials
- Incorrect response formats
- Invalid user IDs
- Authentication failures
- Connection errors
- Incorrect field mappings

---

## 12. API Integration Dashboard Flow

After purchasing the Real-Time Chat service, the client should access the API Integration section.

### Expected Flow

```text
Client Purchases Real-Time Chat
        │
        ▼
Opens Chat Service Dashboard
        │
        ▼
Opens API Integration
        │
        ▼
Reads Integration Requirements
        │
        ▼
Configures API Endpoints
        │
        ▼
Configures Authentication
        │
        ▼
Maps Response Fields
        │
        ▼
Tests APIs
        │
        ▼
Integration Validation
        │
        ▼
API Integration Completed
```

### Dashboard Requirements

- Show required APIs.
- Show API documentation.
- Allow endpoint configuration.
- Allow authentication configuration.
- Allow request header configuration.
- Allow field mapping where required.
- Provide test buttons.
- Display request and response details.
- Display validation errors.
- Show integration status.
- Allow re-testing after changes.
- Separate test and production configuration where possible.

---

## 13. Chat Widget Integration Flow

Once the API integration is completed, the client should be able to configure and install the widget.

### Expected Flow

```text
API Integration Completed
        │
        ▼
Open Widget Integration
        │
        ▼
Configure Widget
        │
        ▼
Generate Integration Code
        │
        ▼
Add Required Code to Website
        │
        ▼
Test Widget
        │
        ▼
Verify User Authentication
        │
        ▼
Verify Chat Functionality
        │
        ▼
Widget Goes Live
```

### Widget Installation

The client platform may have a common layout such as:

```text
Website
│
├── Header
├── Page Content
└── Footer
```

The integration should be simple enough for the client developer to add the required files to their existing layout.

For example:

- Required CSS can be included in the website's `<head>` or through the widget loader.
- Required JavaScript can be included before the closing `</body>` tag or loaded through a single script.
- The widget should initialize automatically after the required configuration is available.

### Preferred Integration Experience

Where possible, provide a single embed script:

```html
<script
  src="https://myvivahai.digitalji.in/widget.js"
  data-platform-key="YOUR_PLATFORM_KEY"
></script>
```

The exact implementation must be decided during development.

The script must not expose secret API keys or private credentials in the browser.

---

## 14. Widget Behavior

The widget should appear as a floating chat button, generally at the bottom-right corner of the website.

### Expected User Experience

```text
External Website
        │
        ▼
Floating Chat Button
        │
        ▼
User Clicks Button
        │
        ▼
Chat Popup Opens
        │
        ▼
User Search
        │
        ▼
Select User
        │
        ▼
Open Conversation
        │
        ▼
Send / Receive Messages in Real Time
```

### Widget Features

- Floating chat button
- Chat popup or panel
- Conversation list
- User search
- User details display
- Real-time messaging
- Message history
- Message timestamps
- Online/offline presence where supported
- Unread message count
- Loading states
- Error states
- Responsive design
- Platform-specific configuration
- Secure authentication

The widget should be reusable across different external platforms.

---

## 15. Conversation Data Strategy

MyVivahAI should not copy the entire external matrimony database.

The external platform remains responsible for its complete user profile data.

MyVivahAI should store only the data required to operate the chat service.

### Data We May Store

- MyVivahAI platform reference
- External user ID
- Conversation ID
- Conversation participants
- Message ID
- Message content
- Message timestamps
- Message status
- Attachments, if supported
- Required integration references
- Required audit information

### Data We Should Not Duplicate Unnecessarily

- Complete user profiles
- Full matrimony profile details
- Personal information that is not required for chat
- External platform's unrelated business data
- Duplicate user records beyond the required integration reference

When the widget needs user details, MyVivahAI can retrieve them through the configured external platform APIs, subject to the integration's permissions and requirements.

---

## 16. Platform Data Isolation

Every external platform must have isolated data.

For example:

```text
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

### Requirements

- Platform A must never access Platform B's data.
- All platform-owned records must be associated with the correct platform.
- Authorization must verify platform ownership.
- API requests must resolve the correct platform context.
- Conversation queries must be platform-scoped.
- External user references must be platform-scoped.
- API credentials must be platform-scoped.
- Widget configurations must be platform-scoped.

### Database Strategy

Start with a scalable architecture that supports strong platform-level isolation.

A shared database with properly scoped records may be suitable initially, but the design must not prevent a future dedicated database per platform if required.

Do not assume that adding a `platform_id` alone is sufficient. Application-level authorization, query scoping, validation, and testing are also required.

---

## 17. Suggested Core Database Domains

The database should be designed around the complete MyVivahAI platform, not only the chat module.

### Core Platform Domain

- Accounts / Users
- Platforms
- Services
- Plans
- Subscriptions
- Payments
- Platform Service Access

### Integration Domain

- Platform Integrations
- API Configurations
- API Credentials
- API Endpoints
- API Test Logs
- Webhook Configurations

### Real-Time Chat Domain

- External Users
- Conversations
- Conversation Participants
- Messages
- Message Statuses
- Message Attachments
- Widget Configurations

### Future Domains

- WhatsApp Integrations
- AI Agents
- Data Entry Jobs
- AI Usage Tracking
- AI Conversations
- Agent Configurations

Do not create unnecessary future tables until their requirements are defined.

---

## 18. Real-Time Chat Technical Requirements

### Messaging

- Messages must be associated with the correct platform.
- Messages must be associated with a conversation.
- Conversations must have participants.
- Messages must have a sender reference.
- External user IDs must be handled consistently.
- Message IDs must be unique.
- Duplicate messages must be prevented.
- Message ordering must be reliable.
- Message timestamps must be stored consistently.

### Real-Time Delivery

- Use WebSockets or an appropriate real-time transport.
- Authenticate WebSocket connections securely.
- Ensure users can only subscribe to authorized conversations.
- Broadcast messages only to the correct participants.
- Handle reconnects.
- Handle connection failures.
- Prevent duplicate message display.
- Support fallback behavior where necessary.

### Presence

If online presence is implemented:

- Track connection state appropriately.
- Do not assume a user is online permanently.
- Handle multiple connections per user where required.
- Clear stale presence data.
- Keep presence platform-scoped.

---

## 19. Security Requirements

Security is a core requirement.

### General

- Never trust client-supplied platform IDs.
- Never trust client-supplied user ownership claims.
- Validate all incoming requests.
- Use authorization policies.
- Protect API credentials.
- Do not expose secret keys in frontend code.
- Use HTTPS in production.
- Protect webhook endpoints.
- Validate webhook signatures where supported.
- Apply rate limiting.
- Log security-relevant failures.
- Avoid storing unnecessary personal data.

### API Credentials

- Encrypt sensitive credentials where appropriate.
- Never display full secret credentials after creation.
- Never log secrets.
- Support credential rotation.
- Allow credentials to be revoked.
- Separate test and production credentials where applicable.

### Widget Security

- The widget must not accept arbitrary platform identity from the browser.
- User authentication must be verified by MyVivahAI.
- Widget requests must be associated with the correct platform.
- Conversation access must be authorized server-side.
- Never rely only on frontend checks.

---

## 20. Coding Guidelines

### General

- Write clean, readable, maintainable code.
- Follow Laravel conventions.
- Use meaningful names.
- Avoid duplicate logic.
- Keep business logic out of views and controllers where possible.
- Prefer reusable services and components.
- Add validation for all external inputs.
- Use database transactions where required.
- Handle exceptions properly.
- Do not silently ignore errors.

### Database

- Use migrations for schema changes.
- Add appropriate indexes.
- Add foreign keys where suitable.
- Use appropriate data types.
- Consider UUIDs or ULIDs where beneficial for public identifiers.
- Avoid exposing sequential internal IDs unnecessarily.
- Add unique constraints where required.
- Design indexes based on actual query patterns.
- Ensure platform-scoped queries are efficient.

### APIs

- Use consistent response formats.
- Version public APIs where appropriate.
- Validate request parameters.
- Return meaningful HTTP status codes.
- Document required fields.
- Avoid breaking existing integrations without versioning.
- Use pagination for large datasets.
- Apply rate limits.

### Frontend / Widget

- Keep widget code isolated from the client's website.
- Avoid CSS conflicts with host websites.
- Avoid global JavaScript variable pollution.
- Use namespaced classes and events.
- Ensure responsive behavior.
- Handle loading and error states.
- Do not expose sensitive credentials.

---

## 21. Development Workflow

Before implementing a feature:

1. Read this `AGENTS.md`.
2. Inspect the existing project structure.
3. Understand the current architecture.
4. Check existing migrations, models, routes, and services.
5. Identify whether the feature belongs to Core, Integration, Chat, or another module.
6. Review existing conventions before introducing new ones.
7. Plan the database and API changes.
8. Implement the feature in a modular way.
9. Add or update validation.
10. Test the feature.
11. Update documentation when necessary.

Do not rewrite working functionality without a clear reason.

Do not introduce a new package, framework, or infrastructure component without checking whether it is necessary.

---

## 22. Documentation Requirements

Maintain project documentation as the system grows.

Recommended documentation files:

```text
docs/
├── architecture.md
├── database.md
├── api-integration.md
├── widget-integration.md
├── realtime-chat.md
├── subscription-flow.md
├── security.md
├── current-tasks.md
├── changelog.md
├── known-issues.md
└── decisions.md
```

### Documentation Rules

- Document important architectural decisions.
- Document database changes.
- Document API contracts.
- Document widget installation steps.
- Document integration requirements.
- Document known limitations.
- Update the changelog after significant changes.
- Do not claim a feature is complete unless it has been implemented and tested.

---

## 23. Current Scope

### In Scope

- MyVivahAI account registration
- Platform registration and management
- Service listing
- Service plans
- Subscription structure
- Real-Time Chat service
- External platform API integration
- API testing environment
- Widget integration
- External user references
- Conversations
- Messages
- Platform-level data isolation
- Scalable architecture

### Out of Scope for Now

- Matrimony AI Agent implementation
- Data Entry Agent implementation
- Advanced WhatsApp automation
- Complex AI workflows
- Unrelated business features

These may be added as separate modules in the future.

---

## 24. Important Instructions for AI Coding Agents

- Treat MyVivahAI as a multi-platform SaaS platform.
- Do not refer to the product as "multi-tenant" in user-facing documentation.
- Real-Time Chat is the first service, not the entire MyVivahAI product.
- Keep future services in mind, but do not implement them prematurely.
- Do not copy complete external platform databases.
- Store only the external user references and chat data required by MyVivahAI.
- Do not assume all external platforms use the same technology.
- Keep API integration flexible.
- Keep widget integration simple for client developers.
- Prioritize secure user identification.
- Never trust raw user IDs from the browser without verification.
- Enforce platform-level data isolation.
- Do not hardcode platform-specific logic into the core chat system.
- Do not expose API secrets in frontend code.
- Do not make major architectural decisions without documenting them.
- Ask for clarification when requirements are ambiguous.
- Prefer a clean, scalable implementation over a quick, tightly coupled solution.

---

## 25. Project Goal

The long-term goal is to make MyVivahAI a reusable platform where businesses can register once, subscribe to one or more services, integrate their existing systems, and manage those services from a centralized dashboard.

The first milestone is to build a reliable, secure, and scalable Real-Time Chat service with:

- External platform API integration
- Secure external user identification
- API testing environment
- Embeddable chat widget
- Real-time messaging
- Platform-specific conversation data
- A foundation for future AI services
