# MyVivahAI — Real-Time Chat Service

## Overview

The Real-Time Chat service is the first service offered by MyVivahAI. Client platforms integrate the chat widget into their websites. End users (the client platform's users) can search for other users, open conversations, and exchange real-time messages.

This document describes the chat domain: conversations, messages, participants, WebSockets, presence, delivery, authorization, and data model considerations.

---

## Scope

**In scope (MVP):**

- Conversations between two users of the same platform
- Real-time message send/receive
- Conversation history
- Unread counts
- Timestamps
- Conversation list
- User search (driven by client's search API)
- Online/offline presence

**Not in scope (MVP):**

- Group conversations
- Voice/video calls
- Attachments (future consideration)
- Typing indicators (future consideration)
- Read receipts (future consideration)
- Cross-platform conversations (a user from Platform A chatting with a user from Platform B) — **explicitly out of scope and disallowed**

---

## Core Concepts

| Concept | Definition |
|---|---|
| Conversation | A private, two-party message thread |
| Participant | One of the users in a conversation |
| Message | A single chat entry in a conversation |
| Message Status | Delivery/sent status markers |
| External User | A user on the client platform referenced by ID |
| Presence | Online/offline state of a user |

---

## Conversation Model

- A conversation has exactly two participants.
- A conversation is scoped to a platform.
- Both participants must belong to the same platform.
- Conversation identity should be stable for a given pair of users (look up existing conversation before creating a new one).
- The deduplication key can be a normalized pair of external user IDs scoped by platform.

```
Platform = P
Users: U1 (id=113), U2 (id=841)
Conversation key: P / sorted pair → "113:841" (normalized)
```

---

## Message Model

A message includes:

- Message ID (public, unique)
- Conversation ID
- Sender (external user ID)
- Content
- Type (text by default; future: image, file, system)
- State (sent, delivered, read — where implemented)
- Created at / Pushed at timestamps
- Client-generated message ID for deduplication (idempotency)

---

## Real-Time Transport

### Recommended: WebSockets

- Widget connects to a MyVivahAI WebSocket endpoint.
- Connection is authenticated with the (short-lived) identity token exchange.
- After exchange, client receives a socket session; the connection subscribes to conversation channels for that user.
- Message push is both **published** on the socket and **acknowledged** via HTTP/REST to guarantee persistence.

### Channel Naming (recommended)

```
private-vivah.{platformId}.user.{externalUserId}
private-vivah.{platformId}.conversation.{conversationId}
```

Use private channels so only the participant user can subscribe. Subscriptions enforced server-side.

### Connection Sequence

```
1. Widget requests a short-lived identity token (client backend)
2. Widget POSTs token to MyVivahAI: POST /api/v1/widget/authenticate
3. Response includes socket session + user context + subscribed channels
4. Widget opens WebSocket to MyVivahAI endpoint with the socket session
5. Server validates subscription to conversation channels via authorization check
6. Real-time messaging begins
```

---

## Delivery Flow

### Sending a message

```
1. Sender widget publishes message over WebSocket:
   { conversationId, clientMessageId, content }
2. Server:
   - Authenticates sender (socket session)
   - Verifies sender is participant
   - Verifies conversation belongs to same platform as sender
   - Deduplicates by clientMessageId (if present)
   - Persists message (MySQL)
   - Updates conversation last_message_at
3. Server broadcasts message to conversation channel
4. Recipient's subscribed socket receives message event
5. Recipient unread count increments (server-side tracking);
   recipient acknowledges receipt (optional read status)
6. Sender receives ack with server message ID
```

### Receiving a message

```
1. Recipient socket receives "message.new" event
2. Widget appends to conversation UI
3. Widget increments the unread badge for that conversation
4. Widget updates timestamp
5. (Optional) Widget sends read receipt when conversation is open
```

---

## History & Pagination

- Conversation list: paginated, ordered by last message time (desc).
- Messages per conversation: paginated (newest first or oldest first with cursor). Recommended approach: query older pages via `before_message_id` cursor.
- Widget may choose to load the last N messages, then lazy-load older ones on scroll.

---

## Unread Counts

Recommended implementation approach:

- Maintain `last_read_message_id` per participant per conversation.
- Unread = count of messages with id > last_read_message_id for that conversation, excluding own messages.
- Maintain a denormalized `unread_count` on participant row for fast conversation-list rendering; recalculate periodically or maintain incrementally. **Open decision** on exact strategy.

---

## Presence

Recommended approach:

- Track online state in Redis: `vivah:{platformId}:presence:{externalUserId}`
- Heartbeat from connected sockets (e.g., every 30s) refreshes presence TTL.
- On connect/disconnect, publish presence change to the user's conversation peers.
- A user is "online" if any active socket session exists for that user (multiple tabs = online).
- Presence is platform-scoped.

---

## Authorization Rules

| Check | Rule |
|---|---|
| Platform scope | Conversation must belong to the caller's platform |
| Participant | Only participants can read/send messages in a conversation |
| Token validity | Identity token must be valid, unexpired, signed |
| Conversation creation | Both users must be same-platform; optional Chat Permission API check |
| Search | Search is performed via client's API on behalf of the platform |

No user may query another platform's conversations, messages, or users.

---

## Chat Permission API Integration

When the client configures the optional Chat Permission API:

- Before opening a conversation with user B, the widget (or server) queries the client's `can-chat` endpoint.
- If `allowed=false`, the widget shows the platform-specific reason.
- In MVP, this check happens at conversation-open time; enforcement on message send is recommended for strictness. **Open decision.**

---

## Rate Limits (Recommended)

| Action | Limit |
|---|---|
| Message send per user | e.g., 30 msg/min (configurable per platform) |
| Conversation create per user | e.g., 20/hr |
| WebSocket connect attempts | e.g., 10/min |
| Auth token exchanges | e.g., 30/min |

---

## Future Considerations

- Typing indicators (via WebSocket `typing` events)
- Read receipts (message status updates)
- Attachments (file upload to MyVivahAI storage; client controls policy)
- Moderation hooks (client moderation API to approve/reject messages) — future
- Historical message export for the client platform
- Blocking/reporting flows

---

## Open Decisions

1. Whether presence is included in the MVP.
2. Unread count consistency strategy (incremental vs periodic recalculation).
3. Whether chat permission is enforced server-side at message send (recommended) or only at conversation open (lighter).
4. Message retention policy (keep indefinitely vs prune after N days) and the client's right to export.
5. Whether group chats are ever in scope.
6. WebSocket server choice (Laravel Reverb / Pusher / Soketi) — see decisions.md.
7. Whether messages may include structured payloads (e.g., matrimony profile cards) — future service consideration.