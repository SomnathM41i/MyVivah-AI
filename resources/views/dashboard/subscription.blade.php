<x-layout.dashboard title="Plan & Subscription" :breadcrumbs="$breadcrumbs">
    @include('partials.dashboard.platform-summary', ['platform' => $platform])

    @if ($activeSubscription)
        <div class="grid gap-6 lg:grid-cols-3">
            <x-card class="lg:col-span-2">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                    <div>
                        <h3 class="text-xl font-semibold text-ink">{{ $activeSubscription->plan?->name ?? 'Active subscription' }}</h3>
                        <p class="mt-1 text-sm text-ink/55">{{ $activeSubscription->service?->name ?? 'Service' }} &middot; {{ $activeSubscription->plan?->billing_period }}</p>
                    </div>
                    <x-badge tone="success" dot>Active</x-badge>
                </div>

                <dl class="mt-8 grid gap-4 text-sm sm:grid-cols-3">
                    <div>
                        <dt class="text-ink/50">Started</dt>
                        <dd class="mt-1 font-medium text-ink">{{ $activeSubscription->starts_at?->format('d M Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-ink/50">Renews / ends</dt>
                        <dd class="mt-1 font-medium text-ink">
                            @if ($activeSubscription->auto_renew)
                                {{ $activeSubscription->next_billing_at?->format('d M Y') ?? '—' }}
                            @else
                                {{ $activeSubscription->ends_at?->format('d M Y') ?? 'Manual renewal' }}
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-ink/50">Amount</dt>
                        <dd class="mt-1 font-medium text-ink">
                            ₹{{ number_format((float) $activeSubscription->plan?->price ?? 0, 0) }}
                            <span class="text-ink/45">/ {{ $activeSubscription->plan?->billing_period }}</span>
                        </dd>
                    </div>
                </dl>
            </x-card>

            <x-card>
                <h3 class="font-semibold text-ink">Manage</h3>
                <p class="mt-2 text-sm leading-relaxed text-ink/60">
                    Self-service plan changes, upgrades and cancellations are coming in a later phase.
                </p>
                <x-badge tone="neutral" class="mt-4">Coming soon</x-badge>
            </x-card>
        </div>
    @elseif ($platform)
        <div class="grid gap-6 lg:grid-cols-2">
            <x-card>
                <h3 class="font-semibold text-ink">No active subscription</h3>
                <p class="mt-2 text-sm leading-relaxed text-ink/60">
                    Subscriptions are activated by our team in the current phase. To get started,
                    browse the plans and reach out — we'll set you up within a business day.
                </p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <x-button href="{{ route('plans.index') }}" variant="primary" size="sm" icon="arrow-right">Browse plans</x-button>
                    <x-button href="{{ route('contact') }}" variant="secondary" size="sm">Contact us</x-button>
                </div>
            </x-card>

            <x-card>
                <h3 class="font-semibold text-ink">How subscriptions work</h3>
                <ul class="mt-4 space-y-3 text-sm text-ink/65">
                    <li class="flex items-start gap-3"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" /> Each plan is linked to a real service in our catalog.</li>
                    <li class="flex items-start gap-3"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" /> A purchase activates the exact API entitlements it promises.</li>
                    <li class="flex items-start gap-3"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" /> You always see invoices in your Payments screen.</li>
                </ul>
            </x-card>
        </div>
    @endif
</x-layout.dashboard>