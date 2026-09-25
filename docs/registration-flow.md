# MyVivahAI — Registration Flow

## Overview

This document describes how a platform owner creates an account on MyVivahAI, verifies their identity, and onboards their platform.

---

## Registration Flow

### Step 1: Visit MyVivahAI

The platform owner visits the MyVivahAI website and clicks "Register" or "Get Started."

### Step 2: Provide Account Details

Required fields:

| Field | Required | Notes |
|---|---|---|
| Full Name | Yes | Owner's name |
| Email Address | Yes | Used for verification and login |
| Phone Number | Open decision | May be required for SMS verification |
| Password | Yes | Must meet strength requirements |
| Company / Platform Name | Yes | Name of the external matrimony platform |
| Website URL | No | Optional HTTPS URL of the external platform |

### Step 3: Email Verification

1. System sends a verification email with a unique link.
2. User clicks the link.
3. System verifies the token and marks the email as verified.
4. Account is not active until email is verified.

**Open decision:** Should there be a phone/SMS verification step as well?

### Step 4: Login

After email verification, the user can log in with email and password.

**Security considerations:**
- Rate limiting on login attempts
- Account lockout after repeated failures (configurable)
- Session management with secure, HTTP-only cookies
- CSRF protection on all forms

---

## Platform Onboarding (Post-Registration)

Signup creates the account, its first platform profile, and the owner's accepted platform-admin assignment. After email verification and first login, the owner continues onboarding in the dashboard:

### Step 1: Service Selection

The owner sees active services in Dashboard → Services. Real-Time Chat is the first available service.

### Step 2: Plan Selection

For each service, the owner chooses an active plan. The production deployment provisions the zero-price Free chat plan only; demo Growth and Business prices are not published. The Free plan activates immediately. Activation provisions the service entitlement, API integration, and a primary API secret shown once in Dashboard → Integrations.

When paid plans are published, selections are stored as pending requests. They do not grant access or create a payment. MyVivahAI reviews the request and handles invoicing/payment manually; there is no online checkout or self-service approval flow. Until paid prices are approved and published, owners can contact support for current options.

### Step 3: API and Widget Setup

For active services, Dashboard → Integrations provides the API base URL and client ID, lets the owner rotate the client secret, configure widget website origins, and configure/test the external user-search endpoint. The client secret stays server-side. The platform backend must create a short-lived widget session for its authenticated external user before rendering the widget.

The external platform remains responsible for its member visibility, eligibility, and block rules in its search endpoint.

---

## Account States

| State | Description |
|---|---|
| Unverified | Account created but email not yet verified |
| Verified | Email verified; account is active |
| Suspended | Account suspended by admin |
| Deactivated | Account deactivated by owner |

---

## Authentication Methods

| Method | Status |
|---|---|
| Email + Password | Confirmed |
| Social Login (Google) | Open decision |
| Social Login (Facebook) | Open decision |
| Two-Factor Authentication | Recommended, not required for MVP |
| Magic Link / Passwordless | Open decision |

---

## Session Management

- Sessions should be stored server-side (database or Redis)
- Session tokens should be rotated on login
- Active sessions should be viewable and revocable from the dashboard
- Sessions should expire after configurable inactivity period

---

## Open Decisions

1. Is phone verification mandatory during registration?
2. Should there be an admin approval step before a platform can subscribe?
3. Should there be a trial/free tier before payment?
4. Should social login (Google, Facebook) be supported?
5. What is the maximum number of platforms a single user can own?
6. Can multiple users from the same organization share a platform account?
7. Should there be role-based access within a platform account (owner, admin, developer)?
8. What password complexity requirements should be enforced?
