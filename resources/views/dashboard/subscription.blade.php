<x-layout.dashboard title="Plan & Subscription" :breadcrumbs="$breadcrumbs">
    @include('partials.dashboard.platform-summary', ['platform' => $platform])

    @if (session('status'))
        <div class="mb-6 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif

    @if (! $platform)
        <x-card><p class="text-sm text-ink/60">No platform is linked to this account yet.</p></x-card>
    @elseif ($subscriptions->isEmpty())
        <x-card>
            <h2 class="font-semibold text-ink">Choose your first service</h2>
            <p class="mt-2 text-sm text-ink/60">Start with a Free plan or request a paid plan. Paid plans stay pending until MyVivahAI reviews them.</p>
            <x-button href="{{ route('dashboard.services') }}" variant="primary" size="sm" class="mt-4" icon="arrow-right">Browse services and plans</x-button>
        </x-card>
    @else
        <div class="space-y-4">
            @foreach ($subscriptions as $subscription)
                <x-card>
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-ink/45">{{ $subscription->service?->name ?? 'Service' }}</p>
                            <h2 class="mt-1 text-lg font-semibold text-ink">{{ $subscription->plan?->name ?? 'Plan' }}</h2>
                            @if ($subscription->plan?->description)
                                <p class="mt-1 text-sm text-ink/60">{{ $subscription->plan->description }}</p>
                            @endif
                        </div>
                        <x-badge :tone="match ($subscription->status) { 'active' => 'success', 'pending' => 'warning', 'suspended' => 'danger', default => 'neutral' }" :dot="$subscription->status === 'active'">
                            {{ ucfirst($subscription->status) }}
                        </x-badge>
                    </div>
                    <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-3">
                        <div><dt class="text-ink/50">Plan price</dt><dd class="mt-1 font-medium text-ink">@if ((float) ($subscription->plan?->price ?? 0) <= 0)Free — no charge@else{{ strtoupper($subscription->plan?->currency ?? 'INR') }} {{ number_format((float) $subscription->plan->price, 2) }} / {{ $subscription->plan->billing_period }}@endif</dd></div>
                        <div><dt class="text-ink/50">Requested / started</dt><dd class="mt-1 font-medium text-ink">{{ $subscription->starts_at?->timezone(config('app.timezone'))->format('d M Y, H:i') ?? '—' }}</dd></div>
                        <div><dt class="text-ink/50">Ends</dt><dd class="mt-1 font-medium text-ink">{{ $subscription->ends_at?->timezone(config('app.timezone'))->format('d M Y') ?? 'No end date set' }}</dd></div>
                    </dl>
                    @if ($subscription->status === 'pending')
                        <div class="mt-4 rounded-lg bg-amber-50 p-3 text-sm text-amber-800">
                            <p>This paid plan request is waiting for manual review. It does not grant service access or create a payment. We also sent the request to our support inbox when configured.</p>
                            <a class="mt-2 inline-block font-semibold underline" href="{{ route('contact') }}">Contact support</a>
                        </div>
                    @endif
                </x-card>
            @endforeach
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <x-button href="{{ route('dashboard.services') }}" variant="secondary" size="sm">Browse services</x-button>
            <x-button href="{{ route('dashboard.payments') }}" variant="secondary" size="sm">View payments</x-button>
        </div>
    @endif
</x-layout.dashboard>
