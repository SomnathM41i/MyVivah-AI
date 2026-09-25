# MyVivahAI — Subscription and Access Control

## Overview

This document describes how a platform subscribes to services, purchases plans, handles payments, and gains (or loses) access to service functionality.

## Current Dashboard Behavior

Dashboard → Services lists active plans. Production deployment provisions the zero-price Free chat plan only; the demo Growth and Business prices are not published. Selecting a zero-price plan activates it immediately, grants the service entitlement, and provisions the API integration and primary key if absent. The new API secret is shown once in Dashboard → Integrations; an owner can explicitly rotate it later.

When paid plans are published, selecting one creates a `pending` subscription and a durable support contact request. It does not grant access or create a payment. The current product has no online checkout or admin approval screen; staff review and activate paid requests manually.

---

## Key Terminology

| Term | Definition |
|---|---|
| **Account** | The MyVivahAI user account (the platform owner's login identity). |
| **Platform** | The registered external business that consumes services from MyVivahAI. A platform is associated with an account. |
| **Service** | A product offered by MyVivahAI (e.g., Real-Time Chat, Matrimony AI Agent). |
| **Plan** | A subscription tier for a service, defining limits, features, and price. |
| **Subscription** | The active relationship between a platform and a service, linked to a plan. |
| **Payment** | A financial transaction made by a platform to purchase or renew a subscription. |
| **Entitlement / Access** | The current access a platform has to a service, derived from subscription status. |

---

## Subscription Lifecycle

```
Platform selects a service
    → Views available plans for that service
    → Selects a plan
    → Checkout
    → Payment initiated
    → Payment verified
    → Subscription activated
    → Dashboard access for that service unlocked
    → Service used
    → (Renewal at period end if auto-renew)
    → (Cancellation at any time)
    → (Expiration if not renewed)
```

---

## Service Selection Flow

1. Platform logs in to MyVivahAI dashboard.
2. Platform browses the "Services" catalog.
3. Each service shows:
   - Name and description
   - Icon/branding
   - Available plans with pricing and feature comparison
4. Platform clicks "Subscribe" on a specific service.
5. Platform is shown the plans for that service.
6. Platform selects a plan.

---

## Plans

Each service has one or more plans. A plan defines:

| Attribute | Description |
|---|---|
| Name | e.g., "Starter", "Professional", "Enterprise" |
| Price | Recurring amount (monthly/yearly) |
| Billing Period | Monthly, yearly, or custom |
| Feature Limits | e.g., max users, max messages, max conversations, presence on/off |
| Included Features | Feature flags |

### Demo Plan Structure Now Defined (2026-09 approval)

Initial plan structure for the **Real-Time Chat** service is seeded as **demo data** (ADR-012):

| Plan | Billing | Placeholder Pricing (demo) |
|---|---|---|
| Free Trial | Trial | ₹0 / trial period |
| Starter | Monthly / Yearly | e.g., ₹999/mo — placeholder |
| Professional | Monthly / Yearly | e.g., ₹2,999/mo — placeholder |
| Business | Monthly / Yearly | e.g., ₹7,999/mo — placeholder |

- Plans are **admin-managed and dynamic** (no code deploys to change them).
- Placeholder pricing is **clearly marked as demo data** and can be changed later.
- Limits/features use the `plan_features` system (see database.md).
- Plans with historical subscriptions are **deactivated, never deleted**.

---

## Payment Flow (Approved Manual Workflow — MVP)

The MVP uses a **manual, admin-approved purchase workflow** (ADR-011). No online gateway is integrated at MVP.

1. Client platform selects a plan and submits a **purchase request**.
2. Admin reviews the request (`under_review`).
3. Admin **approves** or **rejects** the request.
4. Payment is **recorded manually** (no gateway).
5. Admin verifies the recorded payment.
6. Subscription becomes **active only after payment verification**.

Suggested request/approval statuses (kept as **separate states**):

```
pending → under_review → approved → payment_pending → payment_submitted
                                      → payment_verified → (subscription ACTIVE)
rejected / cancelled / expired  (terminal)
```

- **Purchase approval and payment verification are separate states.**
- The database is **gateway-agnostic** so **Razorpay or Stripe can be integrated later** without restructuring (see database.md `purchase_requests` / `payments`).
- No gateway SDKs are required at MVP.

**Open decisions (non-MVP):**
- Gateway provider once online payments are added (Razorpay/Stripe/etc.).
- One-time vs recurring at that future point.
- Multiple currencies.

**Resolved:** manual/admin payment approval — **included at MVP** (ADR-011).

---

## Subscription Model

- A platform can have multiple active subscriptions (one per service).
- Each subscription links: platform + service + plan.
- Subscription status transitions:

```
PENDING  →  ACTIVE  →  EXPIRED
              │
              ├──→  CANCELLED (at renewal boundary)
              │
              ├──→  SUSPENDED (manual/admin)
```

| Status | Meaning |
|---|---|
| PENDING | Payment initiated but not yet verified |
| ACTIVE | Payment verified; service accessible |
| EXPIRED | Subscription period ended and not renewed |
| CANCELLED | Cancelled; remains active until period end as applicable |
| SUSPENDED | Manually suspended by admin for abuse/non-payment |

---

## Access Control (Entitlements)

The system must resolve service access through an **entitlement service**, not through scattered hardcoded checks.

### Design Requirements

- Service access checks are centralized in a single `EntitlementService` (or equivalent).
- The entitlement service resolves:
  ```
  (Platform, Service) → Access status
  ```
- Access resolution considers:
  - Subscription status (must be ACTIVE)
  - Subscription period (not expired)
  - Service availability (service must be enabled)
  - Platform status (platform must be active, not suspended)
- The UI and API middleware consult the entitlement service.
- If access is revoked (expired/suspended), widgets should show a fallback behavior (e.g., disabled chat) rather than errors.

### Recommended Middleware Stack

In Laravel, the following middleware can enforce entitlements:

- `auth` — The platform owner is logged in
- `checkPlatform` — A platform is associated for the request
- `hasServiceAccess:chat` — The platform has access to the Real-Time Chat service

Middleware parameters allow per-route service checks without scattered logic.

---

## Renewal Considerations

- **Auto-renew:** Requires payment method on file and a payment scheduler.
- **Manual renew:** Platform manually purchases a new subscription period.
- **Grace period:** Open decision — provide a grace period after expiration before service is cut off?

---

## Dashboard Gating

| Dashboard Section | Required Subscription |
|---|---|
| Overview / Home | None (logged in) |
| My Platform | None (logged in) |
| Services Catalog | None (logged in) |
| API Integration (Service) | ACTIVE subscription for that service |
| Widget Configuration (Service) | ACTIVE subscription for that service |
| Usage Stats (Service) | ACTIVE subscription for that service |

---

## Billing Considerations

- Plan changes (upgrade/downgrade) — open decision on proration.
- Invoices — open decision.
- Refund policy — open decision.
- Trial periods — open decision.

---

## Open Decisions

1. Payment gateway provider(s).
2. Billing periods offered (monthly/yearly/lifetime).
3. Whether auto-renewal is included at MVP.
4. Whether a free/trial tier exists.
5. Proration rules for plan changes.
6. Grace period after expiration.
7. Whether usage-based limits can be exceeded (e.g., overage blocking or warnings).
8. Multi-currency support.
9. Whether admin can manually create/activate subscriptions.
