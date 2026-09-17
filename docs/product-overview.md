# MyVivahAI — Product Overview

## What is MyVivahAI?

MyVivahAI is a multi-platform SaaS application that enables external businesses — primarily matrimony platforms — to register, subscribe to services, and integrate those services into their own websites and applications.

MyVivahAI acts as a centralized platform providing AI-powered services that external platforms can consume through API integrations and embeddable widgets.

---

## Target Users

### Primary: Matrimony Platform Owners

Businesses that operate matrimony matchmaking websites or applications. They have their own user base, user profiles, authentication systems, and business logic. They want to enhance their platforms with modern chat capabilities and AI-powered features.

### Secondary: Platform End Users

The actual users (brides, grooms, families) on the external matrimony platforms who interact with the chat widget and eventually with AI services.

### Tertiary: MyVivahAI Administrators

Internal team members who manage the MyVivahAI platform, onboard new client platforms, monitor service health, and manage billing.

---

## Core Services

### Current Priority: Real-Time Chat Platform

A embeddable, real-time messaging widget that external matrimony platforms can install on their websites. End users can search for other users, open conversations, and exchange messages in real time without leaving the external website.

### Planned: Matrimony AI Agent

An AI assistant specialized in matrimony matchmaking. This service is **not** being developed now but the architecture must accommodate it later.

### Planned: Data Entry Agent

An AI-powered data entry automation service. This service is **not** being developed now but the architecture must accommodate it later.

### Planned: Additional Services

Future AI and automation services can be added as new service modules.

---

## Platform Model

Each external business that registers on MyVivahAI is a **client platform**. A client platform:

- Registers an account on MyVivahAI
- Subscribes to one or more services
- Receives API credentials and integration documentation
- Configures API endpoints for user data exchange
- Installs a widget on its own website
- Manages its configuration through a centralized dashboard

**Important:** The external platform remains the source of truth for its user data. MyVivahAI stores only the references and data needed for the services it provides.

---

## Business Flow Summary

```
External platform owner visits MyVivahAI
    → Registers account
    → Verifies email/phone
    → Logs in
    → Browses available services
    → Selects Real-Time Chat (first service)
    → Views available plans
    → Purchases a plan
    → Payment is verified
    → Subscription is activated
    → API Integration dashboard becomes available
    → Configures external API endpoints
    → Tests API connectivity
    → Widget Integration dashboard becomes available
    → Configures widget appearance and behavior
    → Generates embed script
    → Adds script to external website
    → Tests widget functionality
    → Widget goes live
    → External website users can chat
```

---

## Revenue Model

- **Subscription-based:** Each service has multiple plans with different feature limits and pricing tiers.
- **Per-platform billing:** Each registered platform has its own subscription and billing cycle.
- **Future:** Potential for usage-based pricing on AI services (tokens, API calls, etc.).

---

## Differentiators

| Feature | Description |
|---|---|
| Technology agnostic integration | External platforms using any technology stack can integrate |
| No database duplication | External platforms keep their own user data; MyVivahAI stores only what it needs |
| Embeddable widget | No redirect required; widget lives on the external website |
| Multi-platform isolation | Data, credentials, and configuration are isolated per platform |
| Modular services | Chat, AI, and other services are independent modules that can be subscribed individually |

---

## Current Status

- **Phase:** Documentation Foundation
- **No code has been implemented yet.**
- **No database migrations exist yet.**
- The Laravel project has not been initialized in the workspace.

---

## Open Decisions

- Exact pricing tiers and plan structures are not yet defined.
- Whether MyVivahAI provides a public-facing website for external end users or only a dashboard for platform owners.
- Whether external platform owners manage everything through a dashboard or also have API access for automation.
- Whether MyVivahAI offers a trial/free tier.
- Specific branding and white-labeling requirements for the widget.
