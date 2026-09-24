{{-- Phase 4 local-only widget embedding demo (ChatDemoController). --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MyVivahAI — Chat Widget Demo</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f5f6fa;
            color: #1f2430;
        }
        header.demo {
            background: linear-gradient(135deg, #6d28d9, #8b5cf6);
            color: #fff;
            padding: 40px 24px 28px;
            text-align: center;
        }
        header.demo h1 { margin: 0 0 6px; font-size: 26px; }
        header.demo p { margin: 0; opacity: .92; font-size: 14px; }
        header.demo .badge {
            display: inline-block; margin-top: 12px; padding: 4px 12px;
            border-radius: 999px; font-size: 12px; font-weight: 600;
            background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3);
        }
        main.demo { max-width: 1080px; margin: 0 auto; padding: 28px 20px 120px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        @media (max-width: 760px) { .grid { grid-template-columns: 1fr; } }
        .panel {
            background: #fff; border: 1px solid #e4e7ef; border-radius: 14px;
            padding: 18px 20px; box-shadow: 0 6px 18px rgba(31,36,48,.06);
        }
        .panel h2 { margin: 0 0 4px; font-size: 18px; display:flex; align-items:center; gap:8px; }
        .panel .email { margin: 0 0 16px; color: #6b7280; font-size: 13px; }
        .dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
        .dot.online { background: #22c55e; }
        .notes { margin-top: 28px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 16px 18px; font-size: 13px; line-height: 1.6; }
        .notes code { background: #f3f4f6; border-radius: 4px; padding: 1px 6px; font-size: 12px; }
        footer.demo { text-align: center; padding: 24px; color: #9ca3af; font-size: 12px; }
        .inline-widget-host { margin-top: 8px; }
    </style>
</head>
<body>
    <header class="demo">
        <h1>MyVivahAI Chat Widget — API Demo</h1>
        <p>Two embedded widgets, each holding its own server-minted widget session token.</p>
        <span class="badge">Realtime: {{ $realtimeEnabled ? ucfirst((string) $realtimeConnection) : 'OFF — REST fallback (polling)' }}</span>
    </header>

    <main class="demo">
        <div class="grid">
            <section class="panel">
                <h2><span class="dot online"></span> Alice</h2>
                <p class="email">alice@demo.example.test · embedded (floating)</p>
                <div id="widget-alice" data-widget></div>
            </section>

            <section class="panel">
                <h2><span class="dot online"></span> Bob</h2>
                <p class="email">bob@demo.example.test · inline (container)</p>
                <div class="inline-widget-host" id="widget-bob" data-widget></div>
            </section>
        </div>

        <div class="notes">
            <strong>How this page works</strong>
            <ul>
                <li>Widget sessions are minted <strong>server-side</strong> by <code>ChatDemoController</code>
                    (exactly what a real platform's backend does through <code>POST /api/v1/widget/session</code>).
                    The browser never sees your platform API secret — only the short-lived session token.</li>
                <li>No cookies are used: every widget API call sends <code>Authorization: Bearer &lt;session token&gt;</code>
                    and the acting identity is <em>bound inside the token</em>, so the browser cannot impersonate another user.</li>
                <li>Open the chat, search for <code>cara@demo.example.test</code>, start a thread, and message it.
                    Bob's widget will show the same conversation (Reverb welcome SVG).</li>
            </ul>
        </div>
    </main>

    <footer class="demo">Loaded script: <code>{{ $widgetScript }}</code></footer>

    <script src="{{ $widgetScript }}"></script>
    <script>
        (function () {
            var el = document.getElementById('widget-alice');
            MyVivahAIWidget.init({
                apiBaseUrl: 'http://127.0.0.1/api/v1/widget',
                session: @json($sessionMe),
                searchPlaceholder: 'Search users…',
                theme: { primary: '#6d28d9' }
            });
            var bob = document.getElementById('widget-bob');
            MyVivahAIWidget.init({
                apiBaseUrl: 'http://127.0.0.1/api/v1/widget',
                session: @json($sessionThem),
                containerId: 'widget-bob',
                theme: { primary: '#0ea5e9' }
            });
        })();
    </script>
</body>
</html>