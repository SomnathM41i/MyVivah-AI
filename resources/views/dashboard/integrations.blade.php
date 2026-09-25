<x-layout.dashboard title="Integrations" :breadcrumbs="$breadcrumbs">
    @include('partials.dashboard.platform-summary', ['platform' => $platform])

    @if (session('status'))
        <div class="mb-6 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <ul class="mb-6 rounded-xl bg-red-50 p-4 text-sm text-red-700">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <x-card>
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-ink">API integration</h3>
                @if ($integration)
                    <x-badge tone="success" dot>Configured</x-badge>
                @else
                    <x-badge tone="neutral">Not set up</x-badge>
                @endif
            </div>

            @if ($integration)
                <dl class="mt-6 space-y-4 text-sm">
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-ink/55">Status</dt>
                        <dd>
                            <x-badge :tone="$integration->status === 'active' ? 'success' : 'warning'">
                                {{ ucfirst($integration->status) }}
                            </x-badge>
                        </dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-ink/55">PASETO version</dt>
                        <dd>
                            <x-badge tone="accent">{{ $integration->paseto_version }}</x-badge>
                        </dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-ink/55">Token validity</dt>
                        <dd class="font-medium text-ink">{{ $integration->token_ttl_seconds }}s</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-ink/55">Rate limit</dt>
                        <dd class="font-medium text-ink">{{ $integration->rate_limit_per_minute }}/min</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-ink/55">API key</dt>
                        <dd class="font-mono text-ink/60">••••••••••••••••</dd>
                    </div>
                </dl>

                <p class="mt-6 rounded-xl bg-ink/5 p-4 text-sm text-ink/55">
                    <x-icon name="shield" class="mr-1.5 inline h-4 w-4 " />
                    Secrets are encrypted at rest and never displayed again. Key rotation &
                    secret management arrive in a later phase.
                </p>
            @else
                <p class="mt-4 text-sm leading-relaxed text-ink/60">
                    This platform has no integration configured yet. Your account's first platform will
                    receive one automatically as onboarding is built out in a later phase.
                </p>
            @endif
        </x-card>

        <x-card>
            <h3 class="font-semibold text-ink">Current API surface</h3>
            <ul class="mt-4 space-y-3 text-sm">
                @php
                    $endpoints = [
                        ['method' => 'POST', 'path' => '/api/v1/auth/token', 'desc' => 'Exchange client ID and secret for a short-lived API token'],
                        ['method' => 'POST', 'path' => '/api/v1/auth/revoke', 'desc' => 'Revoke the presented API token'],
                        ['method' => 'GET',  'path' => '/api/v1/platform/me', 'desc' => 'Read platform identity and service access'],
                        ['method' => 'POST', 'path' => '/api/v1/users/verify', 'desc' => 'Create or refresh an external member reference'],
                        ['method' => 'GET',  'path' => '/api/v1/users/{external_user_id}', 'desc' => 'Resolve a member reference'],
                        ['method' => 'POST', 'path' => '/api/v1/chat/conversations', 'desc' => 'Open or create a member conversation'],
                        ['method' => 'GET',  'path' => '/api/v1/chat/conversations/{id}/messages', 'desc' => 'Read conversation messages'],
                        ['method' => 'POST', 'path' => '/api/v1/chat/conversations/{id}/messages', 'desc' => 'Send a conversation message'],
                        ['method' => 'POST', 'path' => '/api/v1/widget/session', 'desc' => 'Create a short-lived widget session for a logged-in member'],
                    ];
                @endphp
                @foreach ($endpoints as $endpoint)
                    <li class="flex items-start gap-3 rounded-xl border border-ink/8 p-3">
                        <span class="mt-0.5 shrink-0 rounded-md px-1.5 py-0.5 font-mono text-xs font-semibold {{ str_starts_with($endpoint['method'], 'P') ? 'bg-brand-50 text-brand-700' : 'bg-accent-50 text-accent-700' }}">
                            {{ $endpoint['method'] }}
                        </span>
                        <div class="min-w-0">
                            <code class="block truncate font-mono text-xs text-ink">{{ $endpoint['path'] }}</code>
                            <p class="mt-0.5 text-xs text-ink/50">{{ $endpoint['desc'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </x-card>
    </div>

    @if (! $platform)
        <x-card class="mt-6">
            <h3 class="font-semibold text-ink">Platform integrations are unavailable for this account</h3>
            <p class="mt-2 text-sm leading-relaxed text-ink/60">
                This account is not linked to a platform it owns or administers. Ask the platform owner to add this account as an accepted platform admin, then reopen Integrations. The API and widget settings are scoped to that platform.
            </p>
        </x-card>
    @elseif (! $integration)
        <x-card class="mt-6">
            <h3 class="font-semibold text-ink">API integration is not provisioned</h3>
            <p class="mt-2 text-sm leading-relaxed text-ink/60">
                Platform: <strong class="text-ink">{{ $platform->name }}</strong> ({{ $platform->public_id }}). Its API integration record is missing, so search settings cannot be saved yet. Contact MyVivahAI support to provision this platform's integration.
            </p>
        </x-card>
    @else
        <x-card class="mt-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="font-semibold text-ink">API credentials</h3>
                    <p class="mt-1 text-sm text-ink/60">Exchange your client ID and secret for a short-lived API token from your server.</p>
                </div>
                @if ($canUseApi)
                    <form method="POST" action="{{ route('dashboard.integrations.api-key.rotate') }}">
                        @csrf
                        <button class="rounded-lg border border-brand-700 px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50" type="submit">{{ $primaryApiKey ? 'Rotate API secret' : 'Create API secret' }}</button>
                    </form>
                @else
                    <x-badge tone="neutral">Select a service plan first</x-badge>
                @endif
            </div>
            @if (session('api_secret_once'))
                <div class="mt-5 rounded-xl border border-amber-300 bg-amber-50 p-4">
                    <h4 class="font-semibold text-amber-900">Copy this secret now</h4>
                    <p class="mt-1 text-sm text-amber-800">It is shown once. Store it in your website's server-side secret storage; do not put it in browser code.</p>
                    <label class="mt-3 block text-xs font-medium text-amber-900">Client secret
                        <textarea readonly rows="2" class="mt-1 w-full select-all rounded-lg border border-amber-300 bg-white p-2 font-mono text-xs text-ink">{{ session('api_secret_once') }}</textarea>
                    </label>
                </div>
            @endif
            <dl class="mt-5 grid gap-3 text-sm sm:grid-cols-2">
                <div class="rounded-lg bg-ink/5 p-3"><dt class="text-ink/55">API base URL</dt><dd class="mt-1 break-all font-mono text-xs text-ink">{{ rtrim(config('app.url'), '/') }}/api/v1</dd></div>
                <div class="rounded-lg bg-ink/5 p-3"><dt class="text-ink/55">Client ID</dt><dd class="mt-1 break-all font-mono text-xs text-ink">{{ $platform->public_id }}</dd></div>
                <div class="rounded-lg bg-ink/5 p-3 sm:col-span-2"><dt class="text-ink/55">Credential status</dt><dd class="mt-1 text-ink">{{ $primaryApiKey ? 'Active key · '.substr($primaryApiKey->key_fingerprint, 0, 12) : 'No active API key yet' }}</dd></div>
            </dl>
            <div class="mt-5 rounded-xl bg-ink/5 p-4">
                <h4 class="text-sm font-semibold text-ink">Server-side token request</h4>
                <pre class="mt-2 overflow-x-auto whitespace-pre-wrap break-all font-mono text-xs text-ink/70">POST {{ rtrim(config('app.url'), '/') }}/api/v1/auth/token
Content-Type: application/json

{"client_id":"{{ $platform->public_id }}","client_secret":"YOUR_SERVER_SIDE_SECRET"}</pre>
                <p class="mt-2 text-xs text-ink/50">Use the returned short-lived access token from your backend for protected MyVivahAI API calls. Never place the client secret in HTML or JavaScript.</p>
            </div>
        </x-card>

        <x-card class="mt-6">
            <h3 class="font-semibold text-ink">Widget integration</h3>
            <p class="mt-2 text-sm text-ink/60">Use the hosted widget client with a short-lived widget session created by your server. Keep the platform client secret on your server; never place it in browser JavaScript.</p>
            <form method="POST" action="{{ route('dashboard.integrations.widget-origins.update') }}" class="mt-5 grid gap-3 sm:grid-cols-[1fr_auto]">
                @csrf @method('PUT')
                <label class="text-sm font-medium text-ink">Allowed website origins (one HTTPS origin per line)
                    <textarea name="widget_origins" required rows="3" class="mt-1 w-full rounded-lg border border-ink/15 p-2 font-mono text-xs" placeholder="https://matrimony.example">{{ old('widget_origins', implode("\n", $widgetOrigins)) }}</textarea>
                    <span class="mt-1 block text-xs font-normal text-ink/50">Enter only the origin, with no page path. Add staging and production domains on separate lines.</span>
                </label>
                <div class="flex items-end"><button class="rounded-lg border border-brand-700 px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50" type="submit">Save origins</button></div>
            </form>
            <dl class="mt-4 grid gap-3 text-sm md:grid-cols-2">
                <div class="rounded-lg bg-ink/5 p-3">
                    <dt class="text-ink/55">Widget JavaScript</dt>
                    <dd class="mt-1 break-all font-mono text-xs text-ink">{{ url('/js/myvivah-widget.js') }}</dd>
                </div>
                <div class="rounded-lg bg-ink/5 p-3">
                    <dt class="text-ink/55">Widget API base</dt>
                    <dd class="mt-1 break-all font-mono text-xs text-ink">{{ url('/api/v1/widget') }}</dd>
                </div>
                <div class="rounded-lg bg-ink/5 p-3 md:col-span-2">
                    <dt class="text-ink/55">Search route used by the widget</dt>
                    <dd class="mt-1 break-all font-mono text-xs text-ink">GET {{ url('/api/v1/widget/users/search') }}?q=NAME&amp;limit=20</dd>
                    <p class="mt-1 text-xs text-ink/50">This route searches your configured external member directory. <code>/api/v1/widget/integration/users</code> only lists identities already recorded by MyVivahAI.</p>
                </div>
            </dl>
            <div class="mt-5 rounded-xl bg-ink/5 p-4">
                <h4 class="text-sm font-semibold text-ink">Widget session flow</h4>
                <ol class="mt-2 list-decimal space-y-1 pl-5 text-sm text-ink/65">
                    <li>Your server exchanges the client ID and secret at <code>/api/v1/auth/token</code>.</li>
                    <li>Your server sends the logged-in member's own ID to <code>POST /api/v1/widget/session</code> using that API token.</li>
                    <li>Your page initializes the widget with the returned short-lived session data and the hosted JavaScript below.</li>
                </ol>
                <pre class="mt-3 overflow-x-auto whitespace-pre-wrap break-all font-mono text-xs text-ink/70">&lt;script src="{{ url('/js/myvivah-widget.js') }}"&gt;&lt;/script&gt;
&lt;script&gt;
const widgetSession = widgetSessionReturnedByYourBackend;
MyVivahAIWidget.init({
  apiBaseUrl: "{{ url('/api/v1/widget') }}",
  session: widgetSession
});
&lt;/script&gt;</pre>
                <p class="mt-2 text-xs text-ink/50">The session endpoint and API credential belong on your backend. Never accept an external user ID directly from browser input.</p>
            </div>
            <p class="mt-4 text-sm text-ink/60">For server-side session setup and the page embed, follow the widget integration guide provided with your platform onboarding.</p>
        </x-card>
    @endif

    @if ($integration)
        <x-card class="mt-6">
            <h3 class="font-semibold text-ink">Widget user search API</h3>
            <p class="mt-2 text-sm text-ink/60">Search runs from MyVivahAI's backend against your user directory. Return only users visible and eligible to chat with the signed-in requester. Credentials stay server-side and are encrypted at rest.</p>
            <form method="POST" action="{{ route('dashboard.integrations.user-search.update') }}" class="mt-5 grid gap-4 md:grid-cols-2">
                @csrf @method('PUT')
                <label class="text-sm font-medium text-ink">Search endpoint URL
                    <input class="mt-1 w-full rounded-lg border border-ink/15 p-2" type="url" name="user_search_endpoint" required value="{{ old('user_search_endpoint', $searchEndpoint) }}" placeholder="https://matrimony.example/api/chat/users/search">
                </label>
                <label class="text-sm font-medium text-ink">Allowed API host (HTTPS)
                    <input class="mt-1 w-full rounded-lg border border-ink/15 p-2" type="url" name="allowed_search_origin" required value="{{ old('allowed_search_origin', $searchEndpoint ? 'https://'.parse_url($searchEndpoint, PHP_URL_HOST) : '') }}" placeholder="https://matrimony.example">
                </label>
                <label class="text-sm font-medium text-ink">Authentication
                    <select class="mt-1 w-full rounded-lg border border-ink/15 p-2" name="user_search_auth_type">
                        <option value="bearer" @selected(old('user_search_auth_type', $searchAuthType) === 'bearer')>Bearer token</option>
                        <option value="header" @selected(old('user_search_auth_type', $searchAuthType) === 'header')>Custom header</option>
                    </select>
                </label>
                <label class="text-sm font-medium text-ink">Custom header name (when selected)
                    <input class="mt-1 w-full rounded-lg border border-ink/15 p-2" type="text" name="user_search_auth_header" value="{{ old('user_search_auth_header', $searchHeader) }}" placeholder="X-Platform-Search-Key">
                </label>
                <label class="text-sm font-medium text-ink md:col-span-2">Credential {{ $searchSecretConfigured ? '(configured; leave blank to keep it)' : '' }}
                    <input class="mt-1 w-full rounded-lg border border-ink/15 p-2" type="password" name="user_search_auth_secret" autocomplete="new-password" {{ $searchSecretConfigured ? '' : 'required' }}>
                </label>
                <div class="md:col-span-2"><button class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white" type="submit">Save search integration</button></div>
            </form>
            @if (session('search_test_status'))
                <p class="mt-4 rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('search_test_status') }}</p>
            @endif
            @error('search_test')<p class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ $message }}</p>@enderror
            <form method="POST" action="{{ route('dashboard.integrations.user-search.test') }}" class="mt-5 grid gap-4 border-t border-ink/10 pt-5 md:grid-cols-3">
                @csrf
                <label class="text-sm font-medium text-ink">Test as external user
                    <input class="mt-1 w-full rounded-lg border border-ink/15 p-2" type="text" name="requester_external_user_id" required maxlength="255" placeholder="USR1001">
                </label>
                <label class="text-sm font-medium text-ink">Sample query
                    <input class="mt-1 w-full rounded-lg border border-ink/15 p-2" type="search" name="query" required minlength="2" maxlength="100" placeholder="Example">
                </label>
                <div class="flex items-end"><button class="rounded-lg border border-brand-700 px-4 py-2 text-sm font-semibold text-brand-700" type="submit">Test connection</button></div>
            </form>
            <p class="mt-4 text-xs text-ink/50">The form saves your endpoint URL, exact HTTPS host, and shared bearer credential. The credential is encrypted at rest and never shown again.</p>
        </x-card>
    @endif
</x-layout.dashboard>
