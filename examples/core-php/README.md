# Core PHP sample integration

This reference demonstrates that a user can appear in search before opening the MyVivahAI widget and can receive a conversation later. It expects PHP 8+, PDO MySQL, and cURL. Copy `.env.example` to the web-server environment; do not commit real DB credentials or secrets.

1. Implement `users-search.php` behind HTTPS and configure its endpoint, Bearer credential, and host in Dashboard → Integrations.
2. Adapt its sample SQL table/column names and privacy, hidden-profile, block, and chat-eligibility checks to the real matrimony platform.
3. Include `widget-session.php` from an authenticated page, passing only the resulting short-lived session object to `MyVivahAIWidget.init`. The client secret remains on the PHP server.
4. Create a sample user record with `profile_visible=1` and `chat_enabled=1` who has no MyVivahAI mapping. Search returns the person; selecting the result creates the mapping and conversation. When that user later signs in, the same session bootstrap and conversation list expose the thread.

The SQL is illustrative. The platform must bind all query values, enforce its own rules, rate-limit the endpoint at its edge, and use least-privileged DB credentials. `X-MyVivahAI-Requester-ID` is accepted only alongside the server-to-server credential and identifies the requester; do not accept a browser-supplied requester ID.

Example embed. The same-origin PHP endpoint verifies the existing matrimony login session before returning a short-lived widget token:

```html
<script async src="https://myvivahai.digitalji.in/js/myvivah-widget.js"></script>
<script>
fetch('/iviva/widget-session.php', { credentials: 'same-origin' })
  .then(function (response) { if (!response.ok) throw new Error('Chat unavailable'); return response.json(); })
  .then(function (response) {
    window.MyVivahAIWidget.init({
      apiBaseUrl: 'https://myvivahai.digitalji.in/api/v1/widget',
      session: response.data
    });
  });
</script>
```
