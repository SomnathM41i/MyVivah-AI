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
</x-layout.dashboard>