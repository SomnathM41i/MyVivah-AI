<x-layout.dashboard title="Integrations" :breadcrumbs="$breadcrumbs">
    @include('partials.dashboard.platform-summary', ['platform' => $platform])

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
                        ['method' => 'POST', 'path' => '/api/v1/auth/token', 'desc' => 'Exchange client creds for a PASETO token'],
                        ['method' => 'POST', 'path' => '/api/v1/auth/revoke', 'desc' => 'Revoke an issued token'],
                        ['method' => 'GET',  'path' => '/api/v1/me', 'desc' => 'Platform identity + scopes'],
                        ['method' => 'POST', 'path' => '/api/v1/conversations', 'desc' => 'Open a member chat conversation'],
                        ['method' => 'GET',  'path' => '/api/v1/conversations/{id}/messages', 'desc' => 'Read message history'],
                        ['method' => 'POST', 'path' => '/api/v1/messages', 'desc' => 'Send a chat message'],
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

    @if ($integration)
        <x-card class="mt-6">
            <h3 class="font-semibold text-ink">Widget user search API</h3>
            <p class="mt-2 text-sm text-ink/60">Search runs from MyVivahAI's backend against your user directory. Return only users visible and eligible to chat with the signed-in requester. Credentials stay server-side and are encrypted at rest.</p>
            @if (session('status'))
                <p class="mt-4 rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('status') }}</p>
            @endif
            @if ($errors->any())
                <ul class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            @endif
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
            <p class="mt-4 text-xs text-ink/50">Contract: <code>docs/external-user-search-v1.md</code>. Embed-code generation and test/live configuration remain outstanding.</p>
        </x-card>
    @endif
</x-layout.dashboard>
