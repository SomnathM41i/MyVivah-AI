<x-layout.dashboard title="Dashboard" :breadcrumbs="$breadcrumbs">
    @if (! $platform)
        <x-card class="max-w-2xl">
            <div class="flex items-start gap-3">
                <x-icon name="info" class="mt-0.5 h-5 w-5 text-brand-600" />
                <div>
                    <h2 class="font-semibold text-ink">You don't have a platform yet</h2>
                    <p class="mt-1.5 text-sm text-ink/60">
                        Register a platform under Settings to start adding services.
                    </p>
                    <x-button href="{{ route('dashboard.settings') }}" variant="primary" size="sm" class="mt-4">
                        Go to Settings
                    </x-button>
                </div>
            </div>
        </x-card>
    @else
        @include('partials.dashboard.platform-summary', ['platform' => $platform])

        <div class="grid gap-6 md:grid-cols-3">
            {{-- Services --}}
            <x-card>
                <div class="flex items-center justify-between">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-brand-50 text-brand-700">
                        <x-icon name="layers" class="h-5 w-5" />
                    </span>
                    <x-badge tone="neutral">{{ $serviceAccess->count() }} active</x-badge>
                </div>
                <h3 class="mt-4 font-semibold text-ink">Services</h3>
                <p class="mt-1 text-sm text-ink/55">Entitlements currently granted to your platform.</p>
                <a href="{{ route('dashboard.services') }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
                    Manage services <x-icon name="chevron-right" class="h-4 w-4" />
                </a>
            </x-card>

            {{-- Subscription --}}
            <x-card>
                <div class="flex items-center justify-between">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-accent-50 text-accent-700">
                        <x-icon name="credit-card" class="h-5 w-5" />
                    </span>
                    @if ($activeSubscription)
                        <x-badge tone="success" dot>Active</x-badge>
                    @else
                        <x-badge tone="neutral">No plan</x-badge>
                    @endif
                </div>
                <h3 class="mt-4 font-semibold text-ink">Subscription</h3>
                <p class="mt-1 text-sm text-ink/55">
                    {{ $activeSubscription?->plan?->name ?? 'No active subscription yet.' }}
                </p>
                <a href="{{ route('dashboard.subscription') }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
                    View subscription <x-icon name="chevron-right" class="h-4 w-4" />
                </a>
            </x-card>

            {{-- Integrations --}}
            <x-card>
                <div class="flex items-center justify-between">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-700">
                        <x-icon name="plug" class="h-5 w-5" />
                    </span>
                    @if ($platform->integration)
                        <x-badge tone="success" dot>Configured</x-badge>
                    @else
                        <x-badge tone="neutral">Not set up</x-badge>
                    @endif
                </div>
                <h3 class="mt-4 font-semibold text-ink">Integrations</h3>
                <p class="mt-1 text-sm text-ink/55">API connection status for your platform.</p>
                <a href="{{ route('dashboard.integrations') }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 hover:text-brand-800">
                    View integrations <x-icon name="chevron-right" class="h-4 w-4" />
                </a>
            </x-card>
        </div>

        @if ($recentPayments->isNotEmpty())
            <x-card class="mt-6">
                <h3 class="font-semibold text-ink">Recent payments</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-ink/8 text-xs uppercase tracking-wide text-ink/45">
                                <th class="py-2 pr-4 font-medium">Invoice</th>
                                <th class="py-2 pr-4 font-medium">Date</th>
                                <th class="py-2 pr-4 font-medium">Amount</th>
                                <th class="py-2 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentPayments as $payment)
                                <tr class="border-b border-ink/5">
                                    <td class="py-3 pr-4 text-ink">{{ $payment->gateway_transaction_id ?? '—' }}</td>
                                    <td class="py-3 pr-4 text-ink/60">{{ $payment->paid_at?->format('d M Y') ?? $payment->created_at->format('d M Y') }}</td>
                                    <td class="py-3 pr-4 font-medium text-ink">₹{{ number_format((float) $payment->amount, 2) }}</td>
                                    <td class="py-3">
                                        <x-badge :tone="match ($payment->status) { 'completed' => 'success', 'failed' => 'danger', 'refunded' => 'warning', default => 'neutral' }">
                                            {{ ucfirst($payment->status) }}
                                        </x-badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        @endif
    @endif
</x-layout.dashboard>