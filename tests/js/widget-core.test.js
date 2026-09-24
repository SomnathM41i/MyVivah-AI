/**
 * Unit tests for the MyVivahAI widget's pure helpers (window.MyVivahAIWidget._core).
 *
 * Run: npm run test:widget  (or: node --test tests/js/)
 *
 * The widget file is a browser IIFE with zero dependencies; we execute it in a
 * bare node:vm context (no window/document needed — helpers are pure) and
 * exercise the exported `_core` namespace.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';
import { fileURLToPath } from 'node:url';

const WIDGET_SOURCE = readFileSync(
  fileURLToPath(new URL('../../public/js/myvivah-widget.js', import.meta.url)),
  'utf8'
);

function loadWidget() {
  const sandbox = {};
  vm.createContext(sandbox);
  vm.runInContext(WIDGET_SOURCE, sandbox, { filename: 'myvivah-widget.js' });
  assert.ok(sandbox.MyVivahAIWidget, 'MyVivahAIWidget must be exported');
  return sandbox.MyVivahAIWidget;
}

const API = loadWidget();
const core = API._core;

// Objects created inside the node:vm context live in a different JS realm, so
// deepStrictEqual fails on identical-but-cross-realm values. Compare via JSON.
const j = (v) => JSON.stringify(v);

test('api surface exposes the documented methods', () => {
  assert.equal(typeof API.init, 'function');
  assert.equal(typeof API.open, 'function');
  assert.equal(typeof API.close, 'function');
  assert.equal(typeof API.toggle, 'function');
  assert.equal(typeof API.destroy, 'function');
  assert.equal(typeof API.get, 'function');
  assert.ok(API.version.length > 0);
});

test('buildUrl handles slash variance', () => {
  assert.equal(core.buildUrl('https://x.test/api/v1/widget', 'chat/conversations'),
    'https://x.test/api/v1/widget/chat/conversations');
  assert.equal(core.buildUrl('https://x.test/api/v1/widget/', '/chat/conversations'),
    'https://x.test/api/v1/widget/chat/conversations');
  assert.equal(core.buildUrl('', '/a'), '/a');
});

test('createClientMessageId matches the server regex [A-Za-z0-9_-]{8,64}', () => {
  for (let i = 0; i < 200; i++) {
    const id = core.createClientMessageId();
    assert.match(id, /^[A-Za-z0-9_-]{8,64}$/);
  }
  const a = core.createClientMessageId();
  const b = core.createClientMessageId();
  assert.notEqual(a, b);
});

test('clamp bounds values and falls back', () => {
  assert.equal(core.clamp(5, 1, 10), 5);
  assert.equal(core.clamp(0, 1, 10), 1);
  assert.equal(core.clamp(99, 1, 10), 10);
  assert.equal(core.clamp(undefined, 1, 10, 3), 3);
  assert.equal(core.clamp('x', 1, 10, 3), 3);
});

test('reconnectDelay grows exponentially and caps at 30s', () => {
  assert.equal(core.reconnectDelay(1), 1000);
  assert.equal(core.reconnectDelay(2), 2000);
  assert.equal(core.reconnectDelay(3), 4000);
  assert.equal(core.reconnectDelay(6), 30000);
  assert.equal(core.reconnectDelay(20), 30000);
});

test('isAuthError recognises the server auth codes', () => {
  ['TOKEN_EXPIRED', 'TOKEN_REVOKED', 'INVALID_TOKEN', 'INVALID_CLIENT'].forEach((c) => {
    assert.equal(core.isAuthError(c), true);
  });
  ['VALIDATION_FAILED', 'NOT_FOUND', 'SERVICE_NO_ACCESS', 'CHANNEL_DENIED', 'HTTP_429'].forEach((c) => {
    assert.equal(core.isAuthError(c), false);
  });
});

test('esc is HTML-safe', () => {
  assert.equal(core.esc('<script>alert("x&\'y")</script>'),
    '&lt;script&gt;alert(&quot;x&amp;&#39;y&quot;)&lt;/script&gt;');
});

test('avatarLabel and displayName extract stable labels', () => {
  assert.equal(core.avatarLabel('alice@demo.example.test'), 'A');
  assert.equal(core.avatarLabel(''), '?');
  assert.equal(core.displayName({ external_user_id: 'bob@demo' }), 'bob@demo');
  assert.equal(core.displayName({ local_public_id: 'user_001' }), 'user_001');
  assert.equal(core.displayName(null), '?');
});

test('mergeMessages dedupes by id and replaces optimistic rows by client_message_id', () => {
  const existing = [
    { id: 'm1', client_message_id: 'm1c', content: 'one', sent_at: '2026-01-01T00:00:01Z' }
  ];
  const incoming = [
    { id: 'm1', client_message_id: 'm1c', content: 'one', sent_at: '2026-01-01T00:00:01Z' }, // dup
    { id: null, client_message_id: 'opt1', content: 'draft', sent_at: '2026-01-01T00:00:02Z' }, // optimistic
    { client_message_id: 'opt1', id: 'm2', content: 'draft', sent_at: '2026-01-01T00:00:02Z' }, // server ack
    { id: 'm3', client_message_id: 'm3c', content: 'three', sent_at: '2026-01-01T00:00:03Z' }
  ];
  const merged = core.mergeMessages(existing, incoming);
  assert.equal(merged.length, 3);
  assert.equal(merged[0].id, 'm1');
  assert.equal(merged[1].id, 'm2'); // replaced the optimistic temp row
  assert.equal(merged[2].id, 'm3');
});

test('mergeMessages sorts ascending by sent_at', () => {
  const merged = core.mergeMessages([], [
    { id: 'b', sent_at: '2026-01-01T00:00:02Z' },
    { id: 'a', sent_at: '2026-01-01T00:00:01Z' },
    { id: 'c', sent_at: null }
  ]);
  const ids = Array.from(merged).map((m) => m.id);
  assert.equal(j(ids), j(['c', 'a', 'b']));
});

test('parseWsMessage handles pusher frames and garbage', () => {
  assert.equal(j(core.parseWsMessage('{"event":"pusher:ping","data":{}}')),
    j({ event: 'pusher:ping', channel: null, data: {} }));
  const withChannel = core.parseWsMessage('{"event":"message.created","channel":"private-chat.abc","data":{"x":1}}');
  assert.equal(withChannel.channel, 'private-chat.abc');
  assert.equal(core.parseWsMessage('not json'), null);
  assert.equal(core.parseWsMessage(''), null);
});

test('eventToMessage maps message.created into a wire message', () => {
  const msg = core.eventToMessage({
    event: 'message.created',
    channel: 'private-chat.abc',
    data: { conversation_id: 'abc', message_id: 'm9', sender_id: 'bob@demo', type: 'text', status: 'sent', content: 'hi', client_message_id: 'c9', sent_at: '2026-01-01T00:00:00Z' }
  });
  assert.equal(msg.id, 'm9');
  assert.equal(msg.sender.external_user_id, 'bob@demo');
  assert.equal(msg.content, 'hi');
  assert.equal(core.eventToMessage({ event: 'user.online', data: {} }), null);
});

test('buildWsUrl builds a pusher-protocol endpoint', () => {
  const url = core.buildWsUrl({
    enabled: true, scheme: 'wss', host: 'ws-mt1.pusher.com', port: 443,
    path: '/app/key1', app_key: 'key1'
  });
  assert.ok(url.startsWith('wss://ws-mt1.pusher.com:443/app/key1?protocol=7&client=myvivah-widget&version='));
  assert.equal(core.buildWsUrl({ enabled: false }), null);
  assert.equal(core.buildWsUrl(null), null);
});

test('formatRelative renders friendly timestamps', () => {
  const now = Date.parse('2026-09-23T12:00:00Z');
  assert.equal(core.formatRelative('2026-09-23T11:59:30Z', now), 'now');
  assert.equal(core.formatRelative('2026-09-23T11:55:00Z', now), '5m');
  assert.equal(core.formatRelative('2026-09-23T10:00:00Z', now), '2h');
  assert.equal(core.formatRelative('2026-09-22T12:00:00Z', now), '1d');
  assert.equal(core.formatRelative('2026-01-02T00:00:00Z', now), '2026-01-02');
  assert.equal(core.formatRelative('', now), '');
});

test('normalizeConfig requires a session with access_token', () => {
  const good = core.normalizeConfig({
    apiBaseUrl: 'https://x.test/api/v1/widget/',
    session: {
      access_token: 'v4.local.abc',
      self: { external_user_id: 'a@b' },
      channels: { private: 'private-chat', presence: 'presence-chat' },
      realtime: { enabled: false }
    }
  });
  assert.equal(good.apiBaseUrl, 'https://x.test/api/v1/widget');
  assert.equal(good.pollIntervalMs, 5000);
  assert.equal(good.realtimeMaxReconnectAttempts, 6);
  assert.equal(good.session.access_token, 'v4.local.abc');

  const missing = core.normalizeConfig({ apiBaseUrl: 'https://x.test', session: null });
  assert.equal(missing.session, null);
});