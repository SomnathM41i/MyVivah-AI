/**
 * MyVivahAI Chat Widget (Phase 4).
 *
 * Drop-in embeddable chat for external matrimony platforms:
 *
 *   <script src="<MYVIVAH_WIDGET_URL>"></script>
 *   <script>
 *     MyVivahAIWidget.init({
 *       apiBaseUrl: 'https://platform.example.test/api/v1/widget',
 *       session: { access_token, self, channels, realtime, expires_at, ... },
 *       // optional: sessionEndpoint lets the widget re-mint a session BEFORE it
 *       // expires / after it is revoked (you implement it server-side exactly
 *       // like POST /api/v1/widget/session but for YOUR logged-in user).
 *     });
 *   </script>
 *
 * Security model:
 *   - Server-authenticated bootstrap: the page's backend mints a short-lived
 *     widget session PASETO and hands it to this script. No platform secret ever
 *     ships to the browser.
 *   - The acting identity is BOUND INSIDE the token; a browser cannot re-assert
 *     a different user, and the widget never looks at any X-External-User-Id.
 *   - When the token expires/revokes the widget stops silently requesting and
 *     (if present) re-mints via the sessionEndpoint.
 *
 * Realtime: speaks the Pusher protocol (Reverb / Soketi / Pusher) natively with
 * no dependencies. When `session.realtime.enabled` is false — or the socket
 * cannot be reached after `realtimeMaxReconnectAttempts` — it gracefully falls
 * back to polling the REST API. Correctness never depends on realtime.
 */
(function (global) {
  'use strict';

  var VERSION = '1.0.0';
  var CONNECTION_MAX_ATTEMPTS = 6;
  var POLL_INTERVAL_MS = 5000;
  var PRESENCE_HEARTBEAT_MS = 45000;
  var WS_PING_MS = 20000;

  /* ------------------------------------------------------------------ *
   *  Pure helpers (window.MyVivahAIWidget._core) — unit-tested in node.  *
   * ------------------------------------------------------------------ */

  var core = {
    version: VERSION,

    clamp: function (n, min, max, fallback) {
      n = Number(n);
      if (!isFinite(n)) return fallback === undefined ? min : fallback;
      var lo = Number(min), hi = Number(max);
      if (isFinite(lo) && n < lo) n = lo;
      if (isFinite(hi) && n > hi) n = hi;
      return n;
    },

    createClientMessageId: function () {
      // 8-64 chars of [A-Za-z0-9_-]; 26 chars is plenty and collision-free.
      var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';
      var out = 'm';
      for (var i = 0; i < 25; i++) {
        out += chars[Math.floor(Math.random() * chars.length)];
      }
      return out;
    },

    buildUrl: function (base, path) {
      var b = String(base == null ? '' : base).replace(/\/+$/, '');
      var p = String(path == null ? '' : path);
      return b + (p.charAt(0) === '/' ? p : '/' + p);
    },

    buildWsUrl: function (realtime) {
      if (!realtime || !realtime.enabled) return null;
      var scheme = realtime.scheme || 'wss';
      var host = realtime.host || '';
      var port = realtime.port == null ? 443 : realtime.port;
      var path = realtime.path || '/app/' + realtime.app_key;
      var realm = 'myvivah-' + Math.random().toString(36).slice(2, 12);
      return scheme + '://' + host + ':' + port + path +
        '?protocol=7&client=myvivah-widget&version=' + encodeURIComponent(VERSION) +
        '&realm=' + encodeURIComponent(realm);
    },

    reconnectDelay: function (attempt) {
      var n = Math.max(1, Number(attempt) || 1);
      return Math.min(1000 * Math.pow(2, n - 1), 30000);
    },

    isAuthError: function (code) {
      return code === 'TOKEN_EXPIRED' ||
        code === 'TOKEN_REVOKED' ||
        code === 'INVALID_TOKEN' ||
        code === 'INVALID_CLIENT';
    },

    esc: function (value) {
      return String(value == null ? '' : value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    },

    avatarLabel: function (name) {
      var s = String(name == null ? '' : name).trim();
      return s ? s.charAt(0).toUpperCase() : '?';
    },

    displayName: function (ref) {
      if (!ref) return '?';
      if (typeof ref.display_name === 'string' && ref.display_name) return ref.display_name;
      if (typeof ref.external_user_id === 'string' && ref.external_user_id) return ref.external_user_id;
      if (typeof ref.local_public_id === 'string' && ref.local_public_id) return ref.local_public_id;
      return 'Unknown';
    },

    mergeMessages: function (existing, incoming) {
      // Server rows (have `id`) are authoritative. Client-side optimistic rows
      // (no `id`) are dropped whenever a server row with the SAME
      // `client_message_id` is present — that is the "ack replaces the temp
      // bubble" behaviour the UI needs for idempotent sends.
      var byId = Object.create(null);
      var finalClient = Object.create(null); // client_message_id seen on a row WITH id
      var out = [];

      var lists = [Array.isArray(existing) ? existing : [], Array.isArray(incoming) ? incoming : []];

      // Pass 1: mark client ids that have a server counterpart somewhere.
      for (var pass = 0; pass < 2; pass++) {
        var list = lists[pass];
        for (var i = 0; i < list.length; i++) {
          var m = list[i];
          if (m && m.id && m.client_message_id) finalClient[m.client_message_id] = true;
        }
      }

      // Pass 2: append in order, honouring the rules above.
      for (pass = 0; pass < 2; pass++) {
        list = lists[pass];
        for (i = 0; i < list.length; i++) {
          var msg = list[i];
          if (!msg) continue;
          if (msg.id) {
            if (byId[msg.id]) continue;
            byId[msg.id] = true;
            out.push(msg);
          } else if (!msg.client_message_id || !finalClient[msg.client_message_id]) {
            // Optimistic row; keep only if no server ack is in this merge.
            out.push(msg);
          }
        }
      }

      out.sort(function (a, b) {
        var at = a.sent_at ? +new Date(a.sent_at) : 0;
        var bt = b.sent_at ? +new Date(b.sent_at) : 0;
        if (at !== bt) return at - bt;
        return String(a.id || '').localeCompare(String(b.id || ''));
      });

      return out;
    },

    parseWsMessage: function (raw) {
      if (typeof raw !== 'string' || !raw) return null;
      try {
        var msg = JSON.parse(raw);
        return {
          event: typeof msg.event === 'string' ? msg.event : null,
          channel: typeof msg.channel === 'string' ? msg.channel : null,
          data: msg.data === undefined ? {} : msg.data
        };
      } catch (err) {
        return null;
      }
    },

    eventToMessage: function (parsed) {
      if (!parsed || parsed.event !== 'message.created') return null;
      var d = parsed.data || {};
      return {
        id: d.message_id || null,
        type: d.type || 'text',
        status: d.status || 'sent',
        content: d.content || '',
        client_message_id: d.client_message_id || null,
        sender: d.sender_id ? {
          platform: null,
          external_user_id: d.sender_id,
          local_public_id: null
        } : null,
        sent_at: d.sent_at || new Date().toISOString()
      };
    },

    formatRelative: function (iso, nowMs) {
      if (!iso) return '';
      var t = +new Date(iso);
      if (!isFinite(t)) return '';
      var now = isFinite(nowMs) ? nowMs : Date.now();
      var s = Math.round(Math.abs(now - t) / 1000);
      if (s < 60) return 'now';
      var m = Math.round(s / 60);
      if (m < 60) return m + 'm';
      var h = Math.round(m / 60);
      if (h < 24) return h + 'h';
      var d = Math.round(h / 24);
      if (d < 7) return d + 'd';
      var dt = new Date(t);
      return String(dt.getFullYear()) + '-' +
        String(dt.getMonth() + 1).padStart(2, '0') + '-' +
        String(dt.getDate()).padStart(2, '0');
    },

    normalizeConfig: function (raw) {
      var cfg = raw || {};
      var session = cfg.session || null;
      var base = String(cfg.apiBaseUrl == null ? '' : cfg.apiBaseUrl).replace(/\/+$/, '');
      if (!base) base = '/api/v1/widget';
      if (!session || !session.access_token) {
        return { apiBaseUrl: base, session: null };
      }
      return {
        apiBaseUrl: base,
        session: session,
        sessionEndpoint: cfg.sessionEndpoint ? String(cfg.sessionEndpoint) : null,
        containerId: cfg.containerId ? String(cfg.containerId) : null,
        searchPlaceholder: cfg.searchPlaceholder || 'Search people…',
        pollIntervalMs: core.clamp(cfg.pollIntervalMs, 1000, 60000, POLL_INTERVAL_MS),
        realtimeMaxReconnectAttempts: core.clamp(cfg.realtimeMaxReconnectAttempts, 1, 30, CONNECTION_MAX_ATTEMPTS),
        theme: Object.assign({ primary: '#6d28d9', accent: '#ffffff' }, cfg.theme || {}),
        i18n: Object.assign({}, cfg.i18n || {})
      };
    }
  };

  /* ------------------------------------------------------------------ *
   *  DOM helpers                                                        *
   * ------------------------------------------------------------------ */

  function el(tag, className, text) {
    var node = document.createElement(tag);
    if (className) node.className = className;
    if (text !== undefined && text !== null) node.textContent = text;
    return node;
  }

  function debounce(fn, wait) {
    var timer = null;
    return function () {
      var args = arguments;
      var self = this;
      clearTimeout(timer);
      timer = setTimeout(function () { fn.apply(self, args); }, wait || 350);
    };
  }

  function authError(code, message) {
    var err = new Error(message || code);
    err.code = code;
    err.status = 401;
    return err;
  }

  /* ------------------------------------------------------------------ *
   *  Realtime (minimal Pusher-protocol client)                          *
   * ------------------------------------------------------------------ */

  function RealtimeClient(opts) {
    this._socket = null;
    this._connected = false;
    this._attempts = 0;
    this._maxAttempts = opts.maxAttempts;
    this._url = opts.url;
    this._socketId = null;
    this._subscriptions = [];
    this._pingTimer = null;
    this._reconnectTimer = null;
    this._halted = false;
    this.onEvent = opts.onEvent;
    this.onConnectionState = opts.onConnectionState;
    this.onSocketId = opts.onSocketId;
    this.onHalted = opts.onHalted;
    this.onAuthError = opts.onAuthError;
    this.authenticate = opts.authenticate;
    this._boundOnOpen = this._onOpen.bind(this);
    this._boundOnMessage = this._onMessage.bind(this);
    this._boundOnClose = this._onClose.bind(this);
    this._boundOnError = this._onError.bind(this);
  }

  RealtimeClient.prototype.connect = function () {
    if (this._halted || this._socket) return;
    if (typeof WebSocket === 'undefined') {
      this._halt();
      return;
    }
    var ws;
    try {
      ws = new WebSocket(this._url);
    } catch (err) {
      this._halt();
      return;
    }
    this._socket = ws;
    ws.addEventListener('open', this._boundOnOpen);
    ws.addEventListener('message', this._boundOnMessage);
    ws.addEventListener('close', this._boundOnClose);
    ws.addEventListener('error', this._boundOnError);
  };

  RealtimeClient.prototype.subscribe = function (channel, channelData) {
    var self = this;
    if (!this._connected || !this._socket || this._socket.readyState !== 1) {
      this._subscriptions.push({ channel: channel, channel_data: channelData || null });
      return;
    }
    this.authenticate(channel, function (err, options) {
      if (err) {
        if (err.code === 'CHANNEL_DENIED' && self.onAuthError) self.onAuthError();
        return;
      }
      var payload = { event: 'pusher:subscribe', data: { channel: channel, auth: options.auth } };
      if (options.channel_data) payload.data.channel_data = options.channel_data;
      self._send(payload);
    });
  };

  RealtimeClient.prototype.unsubscribe = function (channel) {
    this._subscriptions = this._subscriptions.filter(function (s) { return s.channel !== channel; });
    if (!this._socket) return;
    this._send({ event: 'pusher:unsubscribe', data: { channel: channel } });
  };

  RealtimeClient.prototype.halt = function () {
    this._halted = true;
    this.cleanup();
  };

  RealtimeClient.prototype.cleanup = function () {
    if (this._pingTimer) { clearInterval(this._pingTimer); this._pingTimer = null; }
    if (this._reconnectTimer) { clearTimeout(this._reconnectTimer); this._reconnectTimer = null; }
    if (this._socket) {
      this._socket.removeEventListener('open', this._boundOnOpen);
      this._socket.removeEventListener('message', this._boundOnMessage);
      this._socket.removeEventListener('close', this._boundOnClose);
      this._socket.removeEventListener('error', this._boundOnError);
      try { this._socket.close(); } catch (e) { /* noop */ }
      this._socket = null;
    }
    this._connected = false;
    this._socketId = null;
  };

  RealtimeClient.prototype._send = function (obj) {
    if (!this._socket || this._socket.readyState !== WebSocket.OPEN) return;
    try { this._socket.send(JSON.stringify(obj)); } catch (e) { /* noop */ }
  };

  RealtimeClient.prototype._onOpen = function () {
    this._connected = true;
    this._attempts = 0;
    if (this.onConnectionState) this.onConnectionState('open');
  };

  RealtimeClient.prototype._onMessage = function (evt) {
    var parsed = core.parseWsMessage(evt && evt.data);
    if (!parsed) return;
    var d = parsed.data;
    switch (parsed.event) {
      case 'pusher:connection_established':
        this._socketId = d.socket_id;
        if (this.onSocketId) this.onSocketId(this._socketId);
        this._startPing();
        var subs = this._subscriptions.splice(0, this._subscriptions.length);
        var self = this;
        subs.forEach(function (sub) { self.subscribe(sub.channel, sub.channel_data); });
        if (this.onConnectionState) this.onConnectionState('ready');
        break;
      case 'pusher:ping':
        this._send({ event: 'pusher:pong', data: {} });
        break;
      case 'pusher:pong':
        break;
      default:
        if (this.onEvent) this.onEvent(parsed);
    }
  };

  RealtimeClient.prototype._onClose = function () {
    var wasConnected = this._connected;
    this.cleanup();
    if (this._halted) return;
    if (this.onConnectionState) this.onConnectionState('closed');
    this._scheduleReconnect(wasConnected);
  };

  RealtimeClient.prototype._onError = function () {
    if (this.onConnectionState) this.onConnectionState('error');
  };

  RealtimeClient.prototype._scheduleReconnect = function (wasConnected) {
    var self = this;
    if (this._halted) return;
    if (wasConnected) this._attempts = 0;
    this._attempts += 1;
    if (this._attempts > this._maxAttempts) {
      this._halt();
      return;
    }
    this._reconnectTimer = setTimeout(function () {
      self.connect();
    }, core.reconnectDelay(this._attempts));
  };

  RealtimeClient.prototype._startPing = function () {
    var self = this;
    this._pingTimer = setInterval(function () {
      self._send({ event: 'pusher:ping', data: {} });
    }, WS_PING_MS);
  };

  RealtimeClient.prototype._halt = function () {
    this._halted = true;
    this.cleanup();
    if (this.onConnectionState) this.onConnectionState('halted');
    if (this.onHalted) this.onHalted();
  };

  /* ------------------------------------------------------------------ *
   *  Widget controller                                                  *
   * ------------------------------------------------------------------ */

  function Widget(opts) {
    this.cfg = opts.cfg;
    this.session = this.cfg.session;
    this.token = this.session ? this.session.access_token : null;
    this.selfRef = this.session && this.session.self ? {
      platform: { public_id: null },
      external_user_id: this.session.self.external_user_id,
      local_public_id: this.session.self.local_public_id || null
    } : null;
    this.meId = this.selfRef ? this.selfRef.external_user_id : null;

    this.channels = this.session && this.session.channels ? this.session.channels : {
      private: 'private-chat',
      presence: 'presence-chat'
    };

    this.state = {
      view: 'list',
      conversations: [],
      active: null,
      history: [],
      searchResults: [],
      searchCursor: null,
      searchHasMore: false,
      searching: false,
      searchQuery: '',
      presence: {},
      unauthorized: false,
      polling: !this._realtimeEnabled()
    };

    this._timers = { poll: null, presence: null };
    this._refreshTokenTimer = null;
    this._markReadTimer = null;
    this._requestInFlight = false;
    this._authRefreshed = false;
    this._pollingStarted = false;
    this._listError = null;
    this._searchError = false;
    this._searchSequence = 0;
    this._socketId = null;

    this.id = 'mvw-' + Math.random().toString(36).slice(2, 10);
    this.panel = null;
    this.threadEl = null;
    this.threadTitle = null;
    this.threadPresence = null;
    this.composeInput = null;
    this.searchInput = null;
    this.searchResultsEl = null;
    this.listEl = null;
    this.metaEl = null;
    this.searchBtn = null;
    this.subtitleEl = null;
    this.realtime = null;
    this._rootHost = null;

    this._buildDom();
    this.open();

    if (this.cfg.sessionEndpoint) {
      this._scheduleTokenRefresh();
    }
  }

  Widget.prototype._realtimeEnabled = function () {
    return !!(this.session && this.session.realtime && this.session.realtime.enabled);
  };

  Widget.prototype._apiPath = function (path) {
    return core.buildUrl(this.cfg.apiBaseUrl, path);
  };

  Widget.prototype._request = function (path, options, refreshing) {
    var self = this;
    if (!this.token) return Promise.reject(authError('INVALID_TOKEN', 'Missing session token'));

    var headers = { Accept: 'application/json', Authorization: 'Bearer ' + this.token };
    var init = { method: options.method || 'GET', headers: headers };
    if (options.body) {
      headers['Content-Type'] = 'application/json';
      init.body = JSON.stringify(options.body);
    }
    var url = this._apiPath(path);
    if (options.query) url += '?' + this._qs(options.query);

    return fetch(url, init).then(function (res) {
      return res.json().then(function (body) {
        if (!res.ok || !body || body.success === false) {
          var bad = body && body.error ? body.error : {};
          var err = new Error(bad.message || 'Request failed');
          err.code = bad.code || ('HTTP_' + res.status);
          err.status = res.status;
          throw err;
        }
        return body.data;
      }, function () {
        var err = new Error('Malformed response');
        err.code = 'HTTP_' + res.status;
        err.status = res.status;
        throw err;
      });
    }).catch(function (err) {
      if (core.isAuthError(err.code) && !refreshing && self.cfg.sessionEndpoint) {
        return self._refreshSession().then(function () {
          return self._request(path, options, true);
        });
      }
      throw err;
    });
  };

  Widget.prototype._api = function (path, options) {
    return this._request(path, options || {}, false);
  };

  Widget.prototype._qs = function (params) {
    return Object.keys(params).map(function (k) {
      return encodeURIComponent(k) + '=' + encodeURIComponent(params[k]);
    }).join('&');
  };

  Widget.prototype._refreshSession = function () {
    var self = this;
    if (!this.cfg.sessionEndpoint) return Promise.reject(authError('TOKEN_EXPIRED', 'No sessionEndpoint'));
    if (this._authRefreshed) return Promise.reject(authError('TOKEN_EXPIRED', 'refresh in flight'));
    this._authRefreshed = true;

    return fetch(this.cfg.sessionEndpoint, {
      method: 'POST',
      headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
      body: '{}'
    }).then(function (res) {
      return res.json().then(function (body) {
        if (!res.ok || !body || body.success === false) {
          var bad = body && body.error ? body.error : {};
          var err = new Error(bad.message || 'Session refresh failed');
          err.code = 'TOKEN_EXPIRED';
          throw err;
        }
        var data = body.data || body;
        if (!data.access_token) throw authError('TOKEN_EXPIRED', 'Session endpoint returned no token');
        self._adoptSession(data);
        return data;
      });
    }).finally(function () {
      self._authRefreshed = false;
    });
  };

  Widget.prototype._adoptSession = function (nextSession) {
    var realtimePrevEnabled = this._realtimeEnabled();
    this.session = nextSession;
    this.token = nextSession.access_token || null;
    if (this.selfRef && nextSession.self) {
      this.selfRef.external_user_id = nextSession.self.external_user_id || this.selfRef.external_user_id;
      this.selfRef.local_public_id = nextSession.self.local_public_id || this.selfRef.local_public_id;
      this.meId = this.selfRef.external_user_id;
    }
    if (nextSession.channels) this.channels = nextSession.channels;

    if (this.realtime) { this.realtime.halt(); this.realtime = null; }

    var realtimeEnabled = this._realtimeEnabled();
    if (!realtimeEnabled) {
      this.state.polling = true;
      this._startPollTimer();
    } else {
      this.state.polling = false;
      this._connectRealtime();
    }

    if (this.cfg.sessionEndpoint) this._scheduleTokenRefresh();
  };

  Widget.prototype._handleAuthError = function (err) {
    var self = this;
    if (!this.cfg.sessionEndpoint) { this._setUnauthorized(); return; }
    this._refreshSession().then(function () {
      self._render();
    }).catch(function () { self._setUnauthorized(); });
  };

  Widget.prototype._setUnauthorized = function () {
    var self = this;
    this.state.unauthorized = true;
    if (this.realtime) { this.realtime.halt(); this.realtime = null; }
    this._stopPollTimer();
    this._showMeta(this._t('unauthorized'), true);
  };

  Widget.prototype._scheduleTokenRefresh = function () {
    if (this._refreshTokenTimer) clearTimeout(this._refreshTokenTimer);
    var self = this;
    if (!this.cfg.sessionEndpoint || !this.session || !this.session.expires_at) return;

    var msUntil = Math.max(0, (+new Date(this.session.expires_at)) - Date.now() - 60000);
    if (msUntil <= 0) {
      this._refreshSession().catch(function () { self._setUnauthorized(); });
      return;
    }
    this._refreshTokenTimer = setTimeout(function () {
      self._refreshSession().then(function () { self._render(); })
        .catch(function () { self._setUnauthorized(); });
    }, msUntil);
  };

  Widget.prototype._t = function (key) {
    var map = this.cfg.i18n;
    var fallback = {
      title: 'Chat',
      subtitleConnected: 'connected',
      subtitlePolling: 'polling',
      empty: 'No conversations yet. Search for someone to start chatting.',
      emptyThread: 'No messages yet — say hello!',
      emptySearch: 'No matching users.',
      searchHint: 'Type to search people…',
      searchMinChars: 'Enter at least 2 characters.',
      unauthorized: 'Chat session expired. Please reload the page.',
      listError: 'Could not load conversations.',
      searchError: 'Search unavailable right now.',
      threadError: 'Could not load this conversation.',
      send: 'Send',
      composePlaceholder: 'Type a message…',
      loading: 'Loading…',
      retry: 'Tap to retry',
      presenceOnline: 'online',
      presenceOffline: 'offline',
      unknown: 'Unknown'
    }[key];
    return map && map[key] !== undefined ? map[key] : (fallback !== undefined ? fallback : key);
  };

  /* -------- DOM construction -------- */

  Widget.prototype._buildDom = function () {
    var self = this;
    var container = this.cfg.containerId ? document.getElementById(this.cfg.containerId) : null;

    var host = document.createElement('div');
    host.className = 'myvivah-widget';
    var shadow = this.shadowRoot = host.attachShadow({ mode: 'open' });
    this._injectStyles(shadow);

    if (container) {
      container.appendChild(host);
    } else {
      this.launcher = el('button', 'mwv-launcher', '+');
      this.launcher.setAttribute('aria-label', 'Open chat');
      this.launcher.addEventListener('click', function () { self.toggle(); });
      shadow.appendChild(this.launcher);
      document.body.appendChild(host);
    }

    this.panel = el('div', 'mwv-panel');
    if (container) this.panel.classList.add('mwv-inline');
    this._buildPanelContent();
    shadow.appendChild(this.panel);

    if (container) this.panel.classList.add('mwv-open');
    this._rootHost = host;
  };

  Widget.prototype._injectStyles = function (shadow) {
    var theme = this.cfg.theme;
    var s = el('style');
    s.textContent = [
      ':host{all:initial}',
      '*{box-sizing:border-box;margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif}',
      '.mwv-launcher{position:fixed;right:20px;bottom:20px;z-index:2147483000;width:58px;height:58px;border-radius:50%;border:none;cursor:pointer;font-size:30px;line-height:1;box-shadow:0 6px 18px rgba(0,0,0,.25);transition:transform .15s ease;background:' + theme.primary + ';color:' + theme.accent + '}',
      '.mwv-launcher:hover{transform:scale(1.07)}',
      '.mwv-panel{position:fixed;right:20px;bottom:88px;z-index:2147483000;width:372px;max-width:calc(100vw - 24px);height:min(620px,calc(100vh - 110px));border-radius:16px;background:#fff;color:#1f2430;box-shadow:0 18px 40px rgba(0,0,0,.24);display:flex;flex-direction:column;overflow:hidden;transform:scale(.92);opacity:0;pointer-events:none;transition:transform .18s ease,opacity .18s ease}',
      '.mwv-panel.mwv-open{transform:scale(1);opacity:1;pointer-events:auto}',
      '.mwv-panel.mwv-inline{position:static;width:100%;height:520px;bottom:auto;right:auto;margin:0;transform:none;opacity:1;pointer-events:auto;box-shadow:0 6px 18px rgba(0,0,0,.08)}',
      '.mwv-header{background:linear-gradient(135deg,' + theme.primary + ',#7c3aed);color:#fff;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;gap:8px;flex:none}',
      '.mwv-title{font-size:14px;font-weight:700}',
      '.mwv-subtitle{font-size:11px;opacity:.9;margin-top:2px}',
      '.mwv-actions{display:flex;gap:2px}',
      '.mwv-icon-btn{background:transparent;border:none;color:#fff;cursor:pointer;font-size:16px;line-height:1;padding:4px 6px;border-radius:6px}',
      '.mwv-icon-btn:hover{background:rgba(255,255,255,.18)}',
      '.mwv-body{flex:1;overflow:hidden;background:#f7f8fb;position:relative}',
      '.mwv-view{display:none;height:100%;flex-direction:column}',
      '.mwv-view.mwv-active{display:flex}',
      '.mwv-searchbar{padding:10px 12px;border-bottom:1px solid #e8ebf1;flex:none}',
      '.mwv-searchbar input{width:100%;padding:8px 12px;border:1px solid #d8dce6;border-radius:8px;font-size:13px;outline:none}',
      '.mwv-list{flex:1;overflow-y:auto;padding:6px 8px}',
      '.mwv-row{display:flex;align-items:center;gap:10px;padding:10px;border-radius:10px;cursor:pointer}',
      '.mwv-row:hover{background:#eef1f7}',
      '.mwv-avatar{width:40px;height:40px;border-radius:50%;background:' + theme.primary + ';color:' + theme.accent + ';display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;flex:none;position:relative}',
      '.mwv-avatar .mwv-dot{position:absolute;right:-1px;bottom:-1px;width:10px;height:10px;border-radius:50%;border:2px solid #fff;background:#cbd2df}',
      '.mwv-avatar .mwv-dot.online{background:#22c55e}',
      '.mwv-row-main{flex:1;min-width:0}',
      '.mwv-row-top{display:flex;justify-content:space-between;gap:8px}',
      '.mwv-row-name{font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}',
      '.mwv-row-presence{margin-top:2px;font-size:11px;color:#64748b}',
      '.mwv-row-time{font-size:11px;color:#9aa1b0;flex:none}',
      '.mwv-row-excerpt{font-size:12px;color:#69707e;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px}',
      '.mwv-badge{min-width:18px;height:18px;border-radius:9px;background:#ef4444;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;padding:0 5px;flex:none}',
      '.mwv-thread{flex:1;overflow-y:auto;padding:14px 12px;display:flex;flex-direction:column;gap:6px}',
      '.mwv-thread-head{background:#fff;color:#1f2430;border-bottom:1px solid #e8ebf1}',
      '.mwv-back{display:flex;align-items:center;gap:4px;padding:4px 6px;background:transparent;border:none;color:inherit;cursor:pointer;font-size:16px}',
      '.mwv-msg{max-width:78%;padding:8px 12px;border-radius:14px;font-size:13px;line-height:1.45;word-break:break-word;white-space:pre-wrap}',
      '.mwv-msg.mwv-mine{align-self:flex-end;background:' + theme.primary + ';color:' + theme.accent + ';border-bottom-right-radius:4px}',
      '.mwv-msg.mwv-theirs{align-self:flex-start;background:#fff;color:#1f2430;border:1px solid #e3e7f0;border-bottom-left-radius:4px}',
      '.mwv-msg-meta{display:flex;gap:6px;margin-top:3px;font-size:10px;opacity:.75;align-items:center}',
      '.mwv-msg.mwv-msg-failed{background:#fee2e2;border:1px solid #fca5a5}',
      '.mwv-msg-failed .mwv-msg-status{color:#b91c1c;cursor:pointer;text-decoration:underline;opacity:1}',
      '.mwv-compose{display:flex;gap:8px;padding:10px 12px;border-top:1px solid #e8ebf1;background:#fff;flex:none}',
      '.mwv-compose textarea{flex:1;resize:none;border:1px solid #d8dce6;border-radius:10px;padding:8px 10px;font-size:13px;min-height:38px;max-height:96px;outline:none;font-family:inherit}',
      '.mwv-send{background:' + theme.primary + ';color:' + theme.accent + ';border:none;border-radius:10px;padding:0 16px;font-weight:600;cursor:pointer;font-size:13px}',
      '.mwv-send:disabled{opacity:.5;cursor:not-allowed}',
      '.mwv-fab{display:flex;align-items:center;justify-content:center;text-align:center;padding:18px;color:#69707e;font-size:13px}',
      '.mwv-fab.mwv-err{color:#b91c1c}',
      '.mwv-spinner{border:2px solid rgba(0,0,0,.2);border-top-color:' + theme.primary + ';border-radius:50%;width:14px;height:14px;animation:mwv-spin .7s linear infinite;display:inline-block}',
      '@keyframes mwv-spin{to{transform:rotate(360deg)}}'
    ].join('');
    shadow.appendChild(s);
  };

  Widget.prototype._buildPanelContent = function () {
    var self = this;

    var header = el('div', 'mwv-header');
    var left = el('div');
    left.appendChild(el('div', 'mwv-title', this._t('title')));
    this.subtitleEl = el('div', 'mwv-subtitle', this._t('subtitlePolling'));
    left.appendChild(this.subtitleEl);
    header.appendChild(left);

    var actions = el('div', 'mwv-actions');
    this.searchBtn = el('button', 'mwv-icon-btn', '🔍');
    this.searchBtn.title = 'Search';
    this.searchBtn.addEventListener('click', function () {
      if (self._currentView() === 'search') { self._showView('list'); return; }
      self._showView('search');
      setTimeout(function () { if (self.searchInput) self.searchInput.focus(); }, 0);
    });
    var closeBtn = el('button', 'mwv-icon-btn', '✕');
    closeBtn.addEventListener('click', function () { self.close(); });
    actions.appendChild(this.searchBtn);
    actions.appendChild(closeBtn);
    header.appendChild(actions);
    this.panel.appendChild(header);

    var body = el('div', 'mwv-body');

    var listView = el('div', 'mwv-view mwv-view-list');
    this.listEl = el('div', 'mwv-list');
    listView.appendChild(this.listEl);

    var searchView = el('div', 'mwv-view mwv-view-search');
    var bar = el('div', 'mwv-searchbar');
    this.searchInput = el('input');
    this.searchInput.type = 'search';
    this.searchInput.placeholder = this.cfg.searchPlaceholder;
    this.searchInput.addEventListener('keyup', debounce(function () {
      self._refreshSearch(self.searchInput.value);
    }, 350));
    this.searchInput.addEventListener('input', function () {
      if (self.searchInput.value === '') self._refreshSearch('');
    });
    bar.appendChild(this.searchInput);
    this.searchResultsEl = el('div', 'mwv-list');
    searchView.appendChild(bar);
    searchView.appendChild(this.searchResultsEl);

    var threadView = el('div', 'mwv-view mwv-view-thread');
    var head = el('div', 'mwv-header mwv-thread-head');
    this.threadBackBtn = el('button', 'mwv-back', '‹');
    this.threadBackBtn.title = 'Back';
    this.threadBackBtn.addEventListener('click', function () {
      self._leaveThread();
      self._showView('list');
    });
    var headWrap = el('div');
    headWrap.style.flex = '1';
    headWrap.style.minWidth = '0';
    this.threadTitle = el('div', 'mwv-title');
    this.threadPresence = el('div', 'mwv-subtitle');
    this.threadPresence.style.color = '#69707e';
    headWrap.appendChild(this.threadTitle);
    headWrap.appendChild(this.threadPresence);
    head.appendChild(this.threadBackBtn);
    head.appendChild(headWrap);
    threadView.appendChild(head);

    this.threadEl = el('div', 'mwv-thread');
    threadView.appendChild(this.threadEl);

    var compose = el('div', 'mwv-compose');
    this.composeInput = el('textarea');
    this.composeInput.rows = '1';
    this.composeInput.placeholder = this._t('composePlaceholder');
    this.composeInput.addEventListener('keydown', function (ev) {
      if (ev.key === 'Enter' && !ev.shiftKey) { ev.preventDefault(); self._sendCurrent(); }
    });
    this.sendBtn = el('button', 'mwv-send', this._t('send'));
    this.sendBtn.addEventListener('click', function () { self._sendCurrent(); });
    compose.appendChild(this.composeInput);
    compose.appendChild(this.sendBtn);
    threadView.appendChild(compose);

    var metaView = el('div', 'mwv-view mwv-view-meta');
    this.metaEl = el('div', 'mwv-fab');
    metaView.appendChild(this.metaEl);

    body.appendChild(listView);
    body.appendChild(searchView);
    body.appendChild(threadView);
    body.appendChild(metaView);
    this.panel.appendChild(body);
  };

  /* -------- view switching -------- */

  Widget.prototype._currentView = function () {
    return this.state.view;
  };

  Widget.prototype._showView = function (name) {
    this.state.view = name;
    var views = this.shadowRoot.querySelectorAll('.mwv-view');
    for (var i = 0; i < views.length; i++) {
      views[i].classList.toggle('mwv-active', views[i].classList.contains('mwv-view-' + name));
    }
    if (name === 'list') this._refreshConversations();
    if (name === 'search') this._refreshSearch(this.searchInput.value);
  };

  Widget.prototype._showMeta = function (text, isError) {
    this.metaEl.textContent = text;
    this.metaEl.classList.toggle('mwv-err', !!isError);
    this._showView('meta');
  };

  /* -------- lifecycle taps -------- */

  Widget.prototype.open = function () {
    if (this.state.unauthorized) return;
    this._showView(this.state.view);
    this._syncPresence();
    this._ensurePolling();
    this._startPresenceTimer();
  };

  Widget.prototype.close = function () {
    // panel hidden; keep polling so unread counts stay fresh
  };

  Widget.prototype.toggle = function () {
    if (this.panel.classList.contains('mwv-open') && !this.cfg.containerId) {
      this.panel.classList.remove('mwv-open');
    } else {
      this.open();
      this.panel.classList.add('mwv-open');
    }
  };

  Widget.prototype.destroy = function () {
    var self = this;
    Object.keys(this._timers).forEach(function (k) {
      if (self._timers[k]) { clearInterval(self._timers[k]); clearTimeout(self._timers[k]); self._timers[k] = null; }
    });
    if (this._refreshTokenTimer) clearTimeout(this._refreshTokenTimer);
    if (this._markReadTimer) clearTimeout(this._markReadTimer);
    if (this.realtime) this.realtime.halt();
    if (this._rootHost && this._rootHost.parentNode) this._rootHost.parentNode.removeChild(this._rootHost);
  };

  Widget.prototype._startPresenceTimer = function () {
    var self = this;
    if (this._timers.presence) return;
    this._timers.presence = setInterval(function () {
      if (self.state.unauthorized) return;
      self._syncPresence();
    }, PRESENCE_HEARTBEAT_MS);
  };

  Widget.prototype._syncPresence = function () {
    var self = this;
    this._api('chat/presence/me').then(function (data) {
      if (data && data.external_user_id) {
        self.state.presence[data.external_user_id] = data.presence_status || 'offline';
        self._render();
      }
    }).catch(function () { /* best effort */ });

    this._api('chat/presence', { method: 'POST', body: { status: 'online' } })
      .catch(function () { /* best effort */ });
  };

  Widget.prototype._ensurePolling = function () {
    if (this._pollingStarted) return;
    this._pollingStarted = true;

    if (this._realtimeEnabled()) {
      this.state.polling = false;
      this._connectRealtime();
    } else {
      this.state.polling = true;
      this._startPollTimer();
    }
  };

  Widget.prototype._startPollTimer = function () {
    var self = this;
    if (this._timers.poll) return;
    this._timers.poll = setInterval(function () {
      if (self.state.unauthorized) return;
      var view = self._currentView();
      if (view === 'list') self._refreshConversations();
      if (view === 'search') self._refreshSearch(self.searchInput.value);
      if (view === 'thread' && self.state.active) {
        self._loadHistory(self.state.active.id, true);
        var peerId = self.state.active.peerId;
        if (peerId) {
          self._api('chat/presence/' + encodeURIComponent(peerId)).then(function (data) {
            if (data && data.external_user_id) {
              self.state.presence[data.external_user_id] = data.presence_status || 'offline';
            }
          }).catch(function () { /* best effort */ });
        }
      }
    }, this.cfg.pollIntervalMs);
  };

  Widget.prototype._stopPollTimer = function () {
    if (this._timers.poll) { clearInterval(this._timers.poll); this._timers.poll = null; }
  };

  Widget.prototype._subtitle = function () {
    if (this.state.unauthorized) return this._t('unauthorized');
    if (this.realtime && !this.state.polling) return this._t('subtitleConnected');
    return this._t('subtitlePolling');
  };

  /* -------- realtime wiring -------- */

  Widget.prototype._connectRealtime = function () {
    var self = this;
    if (this.state.unauthorized) return;
    var realtime = this.session && this.session.realtime;
    var url = core.buildWsUrl(realtime);
    if (!url) {
      this.state.polling = true;
      this.startPollTimerSafe();
      return;
    }

    if (this.realtime) { this.realtime.halt(); this.realtime = null; }

    this.realtime = new RealtimeClient({
      url: url,
      maxAttempts: this.cfg.realtimeMaxReconnectAttempts,
      authenticate: function (channel, done) { self._authenticateChannel(channel, done); },
      onEvent: function (parsed) { self._onRealtimeEvent(parsed); },
      onConnectionState: function (stateName) {
        if (stateName === 'ready') self.state.polling = false;
        if (stateName === 'halted') { self.state.polling = true; self._startPollTimerSafe(); }
        self.subtitleEl.textContent = self._subtitle();
      },
      onHalted: function () {
        self.state.polling = true;
        self._startPollTimerSafe();
      },
      onAuthError: function () {
        if (self.cfg.sessionEndpoint) self._refreshSession().catch(function () { self._setUnauthorized(); });
      },
      onSocketId: function (id) { self._socketId = id; }
    });

    this.realtime.connect();
    this.subtitleEl.textContent = this._subtitle();
  };

  Widget.prototype._startPollTimerSafe = function () { this._startPollTimer(); };

  Widget.prototype._authenticateChannel = function (channel, done) {
    var self = this;
    if (!this._socketId || !channel) {
      done(authError('INVALID_CLIENT', 'Socket not connected'), null);
      return;
    }
    this._api('chat/socket/auth', {
      method: 'POST',
      body: { socket_id: this._socketId, channel_name: channel }
    }).then(function (data) {
      done(null, { auth: data.auth, channel_data: data.channel_data || null });
    }).catch(function (err) { done(err, null); });
  };

  Widget.prototype._privateChannelFor = function (conversationId) {
    return this.channels.private + '.' + conversationId;
  };

  Widget.prototype._onRealtimeEvent = function (parsed) {
    var self = this;
    var data = parsed.data || {};
    switch (parsed.event) {
      case 'message.created':
        var msg = core.eventToMessage(parsed);
        if (!msg) return;
        self.state.history = core.mergeMessages(self.state.history, [msg]);
        self._renderThread();
        self._markReadSoon();
        self._refreshConversations();
        break;
      case 'conversation.updated':
        self._refreshConversations();
        if (self.state.active && data.conversation_id === self.state.active.id) {
          self._loadHistory(self.state.active.id, true);
        }
        break;
      case 'message.read':
        if (data.reader_id !== self.meId) {
          self._clearUnread(data.conversation_id);
          self._refreshConversations();
        }
        break;
      case 'user.online':
        if (data.external_user_id) self.state.presence[data.external_user_id] = 'online';
        self._renderList();
        self._renderThreadPresence();
        break;
      case 'user.offline':
        if (data.external_user_id) self.state.presence[data.external_user_id] = 'offline';
        self._renderList();
        self._renderThreadPresence();
        break;
      default:
        break;
    }
  };

  Widget.prototype._enterThread = function (conv) {
    this.state.active = this._convModel(conv);
    this.state.history = [];
    this._showView('thread');
    this.threadTitle.textContent = this.state.active.peerLabel;
    this._renderThreadPresence();
    this._loadHistory(conv.id, false);
    if (this.realtime) this.realtime.subscribe(this._privateChannelFor(conv.id));
  };

  Widget.prototype._leaveThread = function () {
    if (this.state.active && this.realtime) {
      this.realtime.unsubscribe(this._privateChannelFor(this.state.active.id));
    }
    this.state.active = null;
    this.state.history = [];
  };

  Widget.prototype._markReadSoon = function () {
    var self = this;
    if (!this.state.active) return;
    if (this._markReadTimer) clearTimeout(this._markReadTimer);
    this._markReadTimer = setTimeout(function () {
      var active = self.state.active;
      if (!active) return;
      self._api('chat/conversations/' + active.id + '/read', { method: 'POST' })
        .then(function () {
          self._clearUnread(active.id);
          self._renderList();
        })
        .catch(function () { /* non-fatal */ });
    }, 800);
  };

  Widget.prototype._clearUnread = function (conversationId) {
    this.state.conversations.forEach(function (c) {
      if (c.id === conversationId) c.unread_count = 0;
    });
  };

  /* -------- API loaders -------- */

  Widget.prototype._refreshConversations = function () {
    var self = this;
    if (this._requestInFlight || this.state.unauthorized) return;
    this._requestInFlight = true;
    this._api('chat/conversations').then(function (data) {
      self.state.conversations = data || [];
      self._listError = null;
      self._renderList();
    }).catch(function (err) {
      if (core.isAuthError(err.code)) { self._handleAuthError(err); return; }
      self._listError = err;
      self._renderList();
    }).then(function () { self._requestInFlight = false; });
  };

  Widget.prototype._refreshSearch = function (q) {
    var self = this;
    q = String(q || '').trim();
    this.state.searchQuery = q;
    var sequence = ++this._searchSequence;
    this.state.searching = true;
    this._renderSearchResults();
    if (!q) {
      this.state.searching = false;
      this.state.searchResults = [];
      this._renderSearchResults();
      return;
    }
    if (q.length < 2) {
      this.state.searching = false;
      this.state.searchResults = [];
      this._searchError = false;
      this._renderSearchResults();
      return;
    }
    this.state.searchCursor = null;
    this.state.searchHasMore = false;
    this._api('users/search', { query: { q: q, limit: 20 } }).then(function (data) {
      if (sequence !== self._searchSequence || self.state.searchQuery !== q) return;
      self.state.searchResults = data && Array.isArray(data.results) ? data.results : [];
      self.state.searchCursor = data && data.pagination ? data.pagination.next_cursor : null;
      self.state.searchHasMore = !!(data && data.pagination && data.pagination.has_more);
      self._searchError = false;
    }).catch(function () {
      if (sequence !== self._searchSequence) return;
      self.state.searchResults = [];
      self._searchError = true;
    }).then(function () {
      if (sequence !== self._searchSequence) return;
      self.state.searching = false;
      self._renderSearchResults();
    });
  };

  Widget.prototype._loadMoreSearch = function () {
    var self = this, q = this.state.searchQuery, cursor = this.state.searchCursor;
    if (!q || !cursor || this.state.searching) return;
    this.state.searching = true;
    this._renderSearchResults();
    this._api('users/search', { query: { q: q, limit: 20, cursor: cursor } }).then(function (data) {
      if (self.state.searchQuery !== q) return;
      self.state.searchResults = self.state.searchResults.concat(data && Array.isArray(data.results) ? data.results : []);
      self.state.searchCursor = data && data.pagination ? data.pagination.next_cursor : null;
      self.state.searchHasMore = !!(data && data.pagination && data.pagination.has_more);
      self._searchError = false;
    }).catch(function () { self._searchError = true; }).then(function () {
      self.state.searching = false;
      self._renderSearchResults();
    });
  };

  Widget.prototype._openUser = function (map) {
    var self = this;
    var me = this.meId;
    if (!me || !map || !map.candidate_token) return;
    this._api('chat/conversations', {
      method: 'POST',
      body: { candidate_token: map.candidate_token }
    }).then(function (conv) {
      self._replaceOrAppendConv(conv);
      self._enterThread(conv);
    }).catch(function () {
      self._showMeta(self._t('threadError'), true);
    });
  };

  Widget.prototype._replaceOrAppendConv = function (conv) {
    var idx = this.state.conversations.findIndex(function (c) { return c.id === conv.id; });
    if (idx >= 0) this.state.conversations[idx] = conv;
    else this.state.conversations.unshift(conv);
  };

  Widget.prototype._loadHistory = function (conversationId, silent) {
    var self = this;
    this._api('chat/conversations/' + conversationId + '/messages').then(function (data) {
      self.state.history = core.mergeMessages(self.state.history, data);
      self._renderThread();
      self._markReadSoon();
    }).catch(function (err) {
      if (core.isAuthError(err.code)) { self._handleAuthError(err); return; }
      if (!silent) self._showMeta(self._t('threadError'), true);
    });
  };

  Widget.prototype._convModel = function (conv) {
    var peer = null;
    var peers = conv.participants || [];
    for (var i = 0; i < peers.length; i++) {
      if (core.displayName(peers[i]) !== this.meId) peer = peers[i];
    }
    var last = conv.last_message || null;
    return {
      id: conv.id,
      peer: peer,
      peerId: peer ? core.displayName(peer) : null,
      peerLabel: peer ? core.displayName(peer) : this._t('unknown'),
      excerpt: last ? last.excerpt : (conv.empty_msg || ''),
      sentAt: last ? last.sent_at : null,
      senderId: last ? last.sender_id : null,
      unread: conv.unread_count || 0,
      participants: peers
    };
  };

  Widget.prototype._sendCurrent = function () {
    var self = this;
    if (!this.state.active) return;
    var content = this.composeInput.value.trim();
    if (!content) return;
    var active = this.state.active;

    var clientId = core.createClientMessageId();
    var optimistic = {
      id: null,
      type: 'text',
      status: 'sending',
      content: content,
      client_message_id: clientId,
      sender: { platform: { public_id: null }, external_user_id: this.meId, local_public_id: null },
      sent_at: new Date().toISOString()
    };

    this.state.history = core.mergeMessages(this.state.history, [optimistic]);
    this.composeInput.value = '';
    this._renderThread();
    this._scrollThreadBottom();

    this._api('chat/conversations/' + active.id + '/messages', {
      method: 'POST',
      body: { client_message_id: clientId, content: content, type: 'text' }
    }).then(function (serverMsg) {
      self.state.history = core.mergeMessages(self.state.history, [serverMsg]);
      self._renderThread();
      self._refreshConversations();
    }).catch(function (err) {
      if (core.isAuthError(err.code)) {
        self.state.history = self.state.history.filter(function (m) {
          return !(m.client_message_id === clientId && !m.id);
        });
        self._renderThread();
        self._handleAuthError(err);
        return;
      }
      self.state.history = self.state.history.map(function (m) {
        return m.client_message_id === clientId && m.status === 'sending'
          ? Object.assign({}, m, { status: 'failed' })
          : m;
      });
      self._renderThread();
    });
  };

  Widget.prototype._resendFailed = function (clientId) {
    var self = this;
    var idx = this.state.history.findIndex(function (m) { return m.client_message_id === clientId; });
    if (idx < 0 || !this.state.active) return;
    var msg = this.state.history[idx];
    this.state.history[idx] = Object.assign({}, msg, { status: 'sending' });
    this._renderThread();
    this._api('chat/conversations/' + this.state.active.id + '/messages', {
      method: 'POST',
      body: { client_message_id: msg.client_message_id, content: msg.content, type: msg.type || 'text' }
    }).then(function (serverMsg) {
      self.state.history = core.mergeMessages(self.state.history, [serverMsg]);
      self._renderThread();
      self._refreshConversations();
    }).catch(function (err) {
      if (core.isAuthError(err.code)) { self._handleAuthError(err); return; }
      var i2 = self.state.history.findIndex(function (m) { return m.client_message_id === clientId; });
      if (i2 >= 0) self.state.history[i2] = Object.assign({}, self.state.history[i2], { status: 'failed' });
      self._renderThread();
    });
  };

  /* -------- rendering -------- */

  Widget.prototype._render = function () {
    if (!this.listEl) return;
    this.listEl.innerHTML = '';
    if (this._currentView() === 'list') {
      if (this.state.unauthorized) {
        this._showMeta(this._t('unauthorized'), true);
        return;
      }
      this._renderList();
    }
  };

  Widget.prototype._renderList = function () {
    if (!this.listEl || this._currentView() !== 'list' || this.state.unauthorized) return;
    this.listEl.innerHTML = '';
    if (this._listError && !this.state.conversations.length) {
      this.listEl.appendChild(this._emptyNotice(this._t('listError'), true));
      return;
    }
    if (!this.state.conversations.length) {
      this.listEl.appendChild(this._emptyNotice(this._t('empty'), false));
      return;
    }
    var frag = document.createDocumentFragment();
    var self = this;
    this.state.conversations.forEach(function (conv) {
      frag.appendChild(self._conversationRow(self._convModel(conv)));
    });
    this.listEl.appendChild(frag);
  };

  Widget.prototype._renderSearchResults = function () {
    if (!this.searchResultsEl) return;
    this.searchResultsEl.innerHTML = '';
    var q = String(this.state.searchQuery || '');
    if (!q) {
      this.searchResultsEl.appendChild(this._emptyNotice(this._t('searchHint'), false));
      return;
    }
    if (q.length < 2 && !this.state.searching) {
      this.searchResultsEl.appendChild(this._emptyNotice(this._t('searchMinChars'), false));
      return;
    }
    if (this.state.searching && !this.state.searchResults.length) {
      this.searchResultsEl.appendChild(this._emptyNotice(this._t('loading'), false));
      return;
    }
    if (this._searchError) {
      this.searchResultsEl.appendChild(this._emptyNotice(this._t('searchError'), true));
      return;
    }
    if (!this.state.searchResults.length) {
      this.searchResultsEl.appendChild(this._emptyNotice(this._t('emptySearch'), false));
      if (this.state.searchHasMore && !this.state.searching) {
        var self = this;
        var emptyMore = el('button', 'mwv-icon-btn', 'Load more');
        emptyMore.type = 'button';
        emptyMore.addEventListener('click', function () { self._loadMoreSearch(); });
        this.searchResultsEl.appendChild(emptyMore);
      }
      return;
    }
    var frag = document.createDocumentFragment();
    var self = this;
    this.state.searchResults.forEach(function (map) {
      var row = el('div', 'mwv-row');
      var avatar = self._avatar(map);
      if (map.profile_photo_url) {
        var photo = el('img'); photo.src = map.profile_photo_url; photo.alt = ''; photo.referrerPolicy = 'no-referrer';
        photo.style.cssText = 'width:40px;height:40px;border-radius:50%;object-fit:cover';
        photo.onerror = function () { photo.remove(); };
        avatar.textContent = ''; avatar.appendChild(photo);
      }
      var presence = map.presence_status === 'online' ? 'online' : 'offline';
      var dot = el('span');
      dot.className = 'mwv-dot' + (presence === 'online' ? ' online' : '');
      avatar.appendChild(dot);
      row.appendChild(avatar);
      var main = el('div', 'mwv-row-main');
      main.appendChild(el('div', 'mwv-row-name', core.displayName(map)));
      main.appendChild(el('div', 'mwv-row-presence', self._t(presence === 'online' ? 'presenceOnline' : 'presenceOffline')));
      row.appendChild(main);
      var start = el('button', 'mwv-icon-btn', 'Start chat');
      start.type = 'button'; start.addEventListener('click', function (event) { event.stopPropagation(); self._openUser(map); });
      row.appendChild(start);
      row.addEventListener('click', function () { self._openUser(map); });
      frag.appendChild(row);
    });
    this.searchResultsEl.appendChild(frag);
    if (this.state.searchHasMore) {
      var more = el('button', 'mwv-icon-btn', this.state.searching ? this._t('loading') : 'Load more');
      more.type = 'button'; more.disabled = this.state.searching;
      more.addEventListener('click', function () { self._loadMoreSearch(); });
      this.searchResultsEl.appendChild(more);
    }
  };

  Widget.prototype._avatar = function (ref) {
    var av = el('div', 'mwv-avatar', core.avatarLabel(core.displayName(ref)));
    return av;
  };

  Widget.prototype._conversationRow = function (model) {
    var self = this;
    var row = el('div', 'mwv-row');
    var presence = model.peerId ? (this.state.presence[model.peerId] || 'offline') : 'offline';
    var av = this._avatar(model.peer || {});
    var dot = el('span');
    dot.className = 'mwv-dot' + (presence === 'online' ? ' online' : '');
    av.appendChild(dot);
    row.appendChild(av);

    var main = el('div', 'mwv-row-main');
    var top = el('div', 'mwv-row-top');
    top.appendChild(el('div', 'mwv-row-name', model.peerLabel));
    if (model.sentAt) top.appendChild(el('div', 'mwv-row-time', core.formatRelative(model.sentAt)));
    main.appendChild(top);
    var excerpt = (model.senderId && model.senderId === this.meId ? 'You: ' : '') + model.excerpt;
    main.appendChild(el('div', 'mwv-row-excerpt', excerpt));
    row.appendChild(main);
    if (model.unread > 0) row.appendChild(el('span', 'mwv-badge', String(model.unread)));

    row.addEventListener('click', function () {
      self._enterExisting(model.id);
    });
    return row;
  };

  Widget.prototype._enterExisting = function (conversationId) {
    var self = this;
    var found = this.state.conversations.filter(function (c) { return c.id === conversationId; })[0];
    if (found) { this._enterThread(found); return; }
    this._api('chat/conversations/' + conversationId).then(function (conv) {
      self._enterThread(conv);
    }).catch(function () {
      self._showMeta(self._t('threadError'), true);
    });
  };

  Widget.prototype._renderThread = function () {
    if (!this.threadEl) return;
    this.threadEl.innerHTML = '';
    if (!this.state.history.length) {
      this.threadEl.appendChild(this._emptyNotice(this._t('emptyThread'), false));
      return;
    }
    var frag = document.createDocumentFragment();
    var self = this;
    this.state.history.forEach(function (msg) {
      frag.appendChild(self._messageBubble(msg));
    });
    this.threadEl.appendChild(frag);
    this._scrollThreadBottom();
  };

  Widget.prototype._messageBubble = function (msg) {
    var mine = core.displayName(msg.sender) === this.meId;
    var bubble = el('div', 'mwv-msg ' + (mine ? 'mwv-mine' : 'mwv-theirs'));
    bubble.textContent = msg.content || '';

    var meta = el('div', 'mwv-msg-meta');
    if (msg.status === 'sending') {
      meta.appendChild(el('span', 'mwv-msg-status', 'sending…'));
    } else if (msg.status === 'failed') {
      bubble.classList.add('mwv-msg-failed');
      var retry = el('span', 'mwv-msg-status', this._t('retry'));
      meta.appendChild(retry);
      bubble.addEventListener('click', (function (self, m) {
        return function () {
          if (m.id) return;
          self._resendFailed(m.client_message_id);
        };
      })(this, msg));
    }
    if (msg.sent_at) meta.appendChild(el('span', 'mwv-msg-status', core.formatRelative(msg.sent_at)));
    bubble.appendChild(meta);
    return bubble;
  };

  Widget.prototype._renderThreadPresence = function () {
    if (!this.state.active || !this.threadPresence) return;
    var peer = this.state.active.peerId;
    var status = peer ? (this.state.presence[peer] || 'offline') : 'offline';
    this.threadPresence.textContent = status === 'online' ? this._t('presenceOnline') : this._t('presenceOffline');
  };

  Widget.prototype._emptyNotice = function (text, isError) {
    var box = el('div', 'mwv-fab');
    if (isError) box.classList.add('mwv-err');
    box.textContent = text;
    return box;
  };

  Widget.prototype._scrollThreadBottom = function () {
    if (this.threadEl) this.threadEl.scrollTop = this.threadEl.scrollHeight;
  };

  /* -------- public API -------- */

  var instances = Object.create(null);

  function init(rawConfig) {
    var cfg = core.normalizeConfig(rawConfig);

    if (!cfg.session || !cfg.session.access_token) {
      if (typeof console !== 'undefined') {
        console.error('[MyVivahAIWidget] init requires a `session` object with `access_token` ' +
          '(mint it server-side via POST /api/v1/widget/session).');
      }
      return null;
    }

    if (typeof document === 'undefined') {
      throw new Error('[MyVivahAIWidget] init must run in a browser with a DOM.');
    }

    var instance = new Widget({ cfg: cfg });
    instances[instance.id] = instance;
    return instance.id;
  }

  function get(id) { return instances[id] || null; }

  function first() {
    var keys = Object.keys(instances);
    return keys.length ? instances[keys[0]] : null;
  }

  function open(id) {
    var inst = id ? get(id) : first();
    if (inst) inst.toggle();
  }

  function close(id) {
    var inst = id ? get(id) : first();
    if (inst) inst.close();
  }

  function destroy(id) {
    var inst = id ? get(id) : first();
    if (inst) { inst.destroy(); delete instances[inst.id]; }
  }

  global.MyVivahAIWidget = {
    version: VERSION,
    init: init,
    open: open,
    close: close,
    toggle: function (id) {
      var inst = id ? get(id) : first();
      if (inst) inst.toggle();
    },
    destroy: destroy,
    get: get,
    _core: core
  };
})(typeof window !== 'undefined' ? window : (typeof globalThis !== 'undefined' ? globalThis : this));
