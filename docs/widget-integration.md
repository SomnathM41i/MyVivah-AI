# MyVivahAI — Widget Integration

## Overview

After API integration is completed and activated, the client platform can configure the chat widget and install it on its website. This document describes how the widget works, how it is configured, how it authenticates users, how it communicates, and how clients go from testing to production.

---

## What the Widget Does

The widget is an embeddable JavaScript component that:

- Appears as a floating chat button (usually bottom-right)
- Opens a popup/side panel on click
- Does **not** redirect the user away from the external website
- Lets the end user search for other users
- Lets the end user open conversations
- Sends/receives messages in real time
- Shows conversation history
- Shows unread counts
- Shows timestamps
- Shows online/offline presence where implemented
- Uses platform-specific branding and configuration

---

## Integration Flow

```
API Integration completed & activated
    → Open Widget Integration
    → Configure widget (appearance, behavior, branding)
    → Generate embed code
    → Add code to external website
    → Test widget
      → Verify user authentication
      → Verify user search
      → Verify conversation handling
      → Verify real-time messaging
    → Switch from testing mode to live mode
    → Widget goes live
```

---

## Step 1: Open Widget Integration

Available in the dashboard after the API integration for the Chat service is `ACTIVE`.

## Step 2: Configure Widget

Configuration categories:

| Category | Settings |
|---|---|
| Branding | Primary color, accent color, widget title, logo URL, welcome message |
| Positioning | Bottom-right/bottom-left; offset; z-index |
| Launch | Auto-open after N seconds (optional); show on condition (optional) |
| Behavior | Allow search toggle, allow attachment toggle, max message length |
| Defaults | Language, timezone display |
| Test mode | Test/live toggle |

## Step 3: Generate Integration Code

A single embed script is generated, e.g.:

```html
<script src="https://widget.myvivahai.com/chat.js"
        data-platform="abc123"
        data-token-endpoint="https://client.example.com/iviva-token"
        data-theme="default">
</script>
```

The generated script:

- Loads widget JS (and CSS) from the MyVivahAI CDN/host
- Is configured via a public platform identifier alongside runtime-provided config
- Resolves the end user's identity at runtime by calling the client's token endpoint (or receiving a token via a known attribute)
- Does **not** contain any secret credentials

**Security note:** Everything in the page source is visible to any visitor. Therefore:

- The public platform identifier is **not** a secret.
- Identity must come from a signed token generated server-side by the client (see [security.md](security.md)).
- Never place API keys/secret tokens in widget config.

### Alternative: Runtime API Initialization

```html
<script>
  window.MyVivahAIWidget.init({
    platform: "abc123",
    tokenEndpoint: "https://client.example.com/iviva-token",
    placement: "bottom-right"
  });
</script>
<script async src="https://widget.myvivahai.com/chat.js"></script>
```

---

## Step 4: Add Code to External Website

The client adds the embed code to:

- Layout template(s) that render on authenticated pages (recommended)
- The product page/layout where chat should appear

For authenticated experiences, the widget must live on pages where the client's session exists so the client backend can mint identity tokens.

---

## Step 5: How the Widget Identifies the Logged-In User

Preferred flow:

```
External user logs into the client platform
    → Client's page renders (widget script installed)
    → Widget asks client backend for an identity token
      (either via the configured token endpoint, or a pre-rendered token)
    → Client backend reads its own session
    → Client backend signs a short-lived token
      containing platform + external user id + claims + expiry
    → Widget receives token and sends it to MyVivahAI
    → MyVivahAI verifies signature, expiry, platform linkage
    → MyVivahAI resolves the external user in platform context
    → Widget connects to chat with the verified identity
```

**Why a raw user ID is NOT trusted from the browser:**
- Any visitor could edit the script/DOM to impersonate another user.
- The browser environment is attacker-controlled.
- A raw ID has no integrity or authenticity.

**The token must:**
- Be issued only by the client's backend (server-side)
- Have a short expiry (e.g., 5–15 minutes; refreshable)
- Identify the platform (public platform id) + the external user id
- Contain a unique jti (token id) used for replay protection
- Be signed with a secret the client registered with MyVivahAI

**Recommended token standard:** PASETO v4.local (approved — ADR-006, see [decisions.md](decisions.md)). Token lifetime ~5 minutes.

---

## Step 6: How the Widget Communicates

| Path | Purpose |
|---|---|
| Widget ↔ MyVivahAI (HTTPS API) | Token exchange, session bootstrap, conversation list, history, search trigger |
| Widget ↔ MyVivahAI (WebSocket) | Real-time messages, presence, read receipts, unread updates |
| Widget ↔ Client Platform (via MyVivahAI proxy or direct) | **Search is hosted server-side by the client API**; the widget delegates search to MyVivahAI, which calls the client's User Search API on behalf of the platform |

MyVivahAI is the middle layer so that the client's API credentials never appear in the browser.

---

## Step 7: How the Widget Avoids CSS/JS Conflicts with the Host Website

- All widget styles are scoped under a unique wrapper (e.g., `.mvv-root` or Shadow DOM).
- Widget uses its own namespaced class names.
- Widget JS uses an IIFE/closure and avoids polluting the global namespace except for a single `window.MyVivahAIWidget` handle.
- CSS is loaded within the widget container (Shadow DOM preferred where browsers allow) to prevent host styles from leaking in and widget rules from leaking out.
- `z-index` is configurable to sit above host content.
- No globals are overwritten; conflicts are documented in [widget-integration.md] and tested during integration testing.

**Recommended:** Use Shadow DOM for full CSS isolation; fall back to scoped class prefix for older browsers if required.

---

## Step 8: Test the Widget

The client uses a **test/live** toggle:

| Mode | Behavior |
|---|---|
| **Test mode** | Widget runs against sandbox configuration; only test platform user IDs and test conversations; debug endpoints accessible |
| **Live mode** | Widget runs against the real production integration |

### Testing checklist (surfaced in the dashboard)

1. Widget loads (`window.MyVivahAIWidget` exists)
2. Widget button renders at expected position
3. Identity token flow works (unauthenticated state is handled gracefully)
4. User search returns expected results
5. Conversation list loads
6. A conversation opens
7. Messages send and receive in real time (open two browser windows)
8. Unread counts update correctly
9. Timestamps display in the correct format
10. Presence indicators work (if enabled)

### Debug panel

In test mode, the widget exposes a debug overlay showing:

- Loaded configuration
- Current identity (platform id, external user id, token expiry)
- Last API calls (request/response bodies)
- WebSocket connection status
- Errors and warnings

---

## Step 9: Switch from Test to Live

After testing passes, the client toggles the widget to **Live** mode in the dashboard. MyVivahAI:

1. Marks the widget configuration as live.
2. Confirms which API integration is referenced.
3. Optionally requires a final re-validation that integration is still `ACTIVE`.

The embed script URL can remain identical across test/live if the mode is resolved server-side; otherwise a different URL or config flag distinguishes them. **Open decision.**

---

## Chat Widget Feature Matrix (in scope for current service)

| Feature | Supported |
|---|---|
| Floating button | Yes |
| Popup / side panel | Yes |
| Search users | Yes (requires client search API) |
| Open conversation | Yes |
| Real-time send/receive | Yes |
| Conversation history | Yes |
| Unread counts | Yes |
| Timestamps | Yes |
| Online/offline presence | Recommended |
| Attachments | Future consideration |
| Typing indicator | Future consideration |
| Read receipts | Future consideration |
| Voice/video | Not in scope |

---

## Open Decisions

1. Use Shadow DOM vs scoped CSS for isolation.
1. Token standard (JWT vs PASETO vs HMAC). **Resolved** — PASETO v4.local (ADR-006); see decisions.md.
3. Whether search is a required feature at MVP.
4. How test/live modes are distinguished (single embed URL with server-side resolution vs separate URLs).
5. Whether the token endpoint is called by the widget directly or a pre-rendered token is injected into the page by the client's server.
6. Whether image URLs are proxied through MyVivahAI for privacy (referrer hiding).
7. Widget caching/CDN versioning strategy.
8. Browser support matrix (modern only vs legacy browsers).

---

## Phase 4 — Implemented Widget Backend + Client JS

The sections above describe the product flow. This section documents the **implemented** Phase 4 build (backend, client JS, and test coverage). See also `docs/integration-api.md` (§ Widget) for the wire contract and `docs/security.md` (§ Widget) for the trust model.

### What was shipped

| Area | Implementation |
|---|---|
| Session bootstrap | `POST /api/v1/widget/session` (platform PASETO, server-to-server) mints a single-user widget session |
| Widget token | PASETO **v4.local**, `aud = widget:{slug}`, `sub = external_user_id`, `platform_id` + `external_user_id` claims, `scope = [realtime_chat:read, realtime_chat:write]`, footer `kid` |
| Lifetime | `config('widget.session.ttl_seconds')` (default 900 s, capped at `max_ttl_seconds` = 1800) |
| Widget API routes | `api/v1/widget/**` — conversations CRUD/read, messages index/store, read/markRead, presence heartbeat/show/me, `socket/auth`, `integration/users`, `users/{external_user_id}`, `session/revoke` |
| Auth middleware | `ValidateWidgetToken` → sets `widget_session` + an adapted `paseto` so scope/entitlement/audit/rate-limit middleware run unchanged |
| Identity rule | Widget identity ALWAYS comes from the token (`ExternalUserContext`), never from `X-External-User-Id` — a spoofed header is ignored |
| Cross-route isolation | A widget token can never be replayed on platform routes (`PLATFORM_MISMATCH` 403), and vice versa |
| Revocation | `POST /api/v1/widget/session/revoke` blacklists the token `jti`; expiry enforced on every request |
| CORS | `config/cors.php` serves `api/v1/widget/*` preflights; per-platform `integration.allowed_origins` (exact or `https://*.sub.example`) strips ACAO in `RestrictWidgetOrigins` (global, runs after `HandleCors`) |
| Guard | Widget callers may only open threads where the token-bound user is a participant (422 otherwise) |
| Client JS | `public/js/myvivah-widget.js` — zero-dependency IIFE; `init/open/close/toggle/destroy/get/version`; floating + inline modes; REST-first with optional realtime; optimistic sends with dedupe by `client_message_id`; presence dots via polling |
| Demo | `GET /demo/chat` (local + APP_DEBUG only) renders two side-by-side users; `DemoPlatformSeeder` seeds the demo integration + 5 demo users |
| Tests | PHP feature suites `WidgetApiTest`/`WidgetIntegrationTest` (auth, isolation, spoofing, revoke, expiry, E2E, CORS, realtime broadcast) + 15 Node unit tests over the widget `_core` |

### External user search and search-to-chat (2026-09-25)

Search delegates to the configured client API, not the MyVivahAI identity-map list. The short-lived widget session binds platform and requester identity. The backend sends those identities server-to-server and applies timeouts, response validation, rate limits, and endpoint host checks. The widget receives only the fields in [external-user-search-v1.md](external-user-search-v1.md), plus a MyVivahAI-derived `presence_status` (`online` or `offline`) for each result. Presence reflects a MyVivahAI widget heartbeat; it cannot report a user's Disha website login when that user has not loaded the chat widget. A five-minute encrypted candidate token binds the selected target to that requester and platform; conversation creation rejects raw target IDs from browsers. MyVivahAI creates the target identity reference and deterministic conversation only after token validation. The client remains responsible for visibility and chat eligibility when producing search results.

Dashboard → Integrations stores the endpoint and encrypted credential and can test it with an operator-supplied requester and sample query. A generated embed snippet/config record, test/live mode, and full checklist are not yet available. The Core PHP sample is a reference, not a verified integration against a live database.

### Embedding (runtime API initialization — implemented)

```html
<script>
  window.MyVivahAIWidget.init({
    session: {
      apiBaseUrl: "https://myvivahai.example/api/v1/widget",
      accessToken: "v4.local....",          // from the platform backend
      externalUserId: "4582",               // echoed for styling "your" messages
      sessionEndpoint: "/iviva/session"     // optional — refresh expired sessions
    },
    placement: "bottom-right"
  });
</script>
<script async src="https://myvivahai.example/js/myvivah-widget.js"></script>
```

The browser only ever holds the short-lived widget token; **the platform's PASETO secret and API keys never leave the client server**. When the token expires the widget either re-requests the configured `sessionEndpoint` or falls back to a "session expired" state.

### Local demo

1. `php artisan db:seed` (idempotent `DemoPlatformSeeder`).
2. Serve locally; visit `/demo/chat`.
3. Open both panes and send a message — realtime wire events are visible in the other pane (or via REST fallback when realtime is disabled).
