{{-- Phase 4 demo error state (ChatDemoController). --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MyVivahAI — Widget Demo</title>
    <style>
        body { margin:0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background:#f5f6fa; color:#1f2430; display:grid; place-items:center; min-height:100vh; }
        .box { background:#fff; border:1px solid #e4e7ef; border-radius:14px; padding:32px 36px; max-width:520px; box-shadow:0 6px 18px rgba(31,36,48,.06); }
        h1 { margin:0 0 8px; font-size:20px; color:#b91c1c; }
        p { margin:0 0 16px; font-size:14px; line-height:1.6; }
        code { background:#f3f4f6; border-radius:4px; padding:1px 6px; font-size:12px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Demo not ready</h1>
        <p>{{ $message }}</p>
        <p>After seeding, revisit <code>/demo/chat</code>.</p>
    </div>
</body>
</html>