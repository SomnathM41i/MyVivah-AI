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
| Website URL | Yes | URL of the external platform |

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

After first login, the platform owner is guided through an onboarding flow:

### Step 1: Platform Profile Setup

The owner provides additional platform details:

| Field | Required | Notes |
|---|---|---|
| Platform Display Name | Yes | How the platform appears in MyVivahAI |
| Industry / Category | Yes | Primary category (matrimony, dating, etc.) |
| Primary Contact Name | Yes | Technical or business contact |
| Primary Contact Email | Yes | For integration support |
| Primary Contact Phone | Open decision | Optional |
| Platform Description | Optional | Brief description |

### Step 2: Service Selection

The owner sees available services and selects one or more. Initially, only "Real-Time Chat" is available.

### Step 3: Plan Selection

For each selected service, the owner sees available plans and chooses one.

### Step 4: Payment

The owner selects a payment method and completes payment.

### Step 5: Activation

After payment verification, the service subscription is activated and the relevant dashboard section becomes available.

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
