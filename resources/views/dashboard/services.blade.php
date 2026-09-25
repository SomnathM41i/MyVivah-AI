<x-layout.dashboard title="Services" :breadcrumbs="$breadcrumbs">
    @include('partials.dashboard.platform-summary', ['platform' => $platform])

    @if (session('status'))
        <div class="mb-6 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <ul class="mb-6 rounded-xl bg-red-50 p-4 text-sm text-red-700">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    @endif

    @if (! $platform)
        <x-card>
            <h2 class="font-semibold text-ink">No platform is linked to this account</h2>
            <p class="mt-2 text-sm text-ink/60">Create an account with a platform profile or ask its owner to add you as an accepted platform admin before choosing services.</p>
        </x-card>
    @else
        <p class="mb-6 max-w-3xl text-sm leading-relaxed text-ink/60">Choose a service and plan for {{ $platform->name }}. Free plans activate now. Paid plan requests are recorded as pending and do not enable access until reviewed and approved.</p>

        <div class="grid gap-6 lg:grid-cols-2">
            @forelse ($services as $service)
                @php
                    $granted = $serviceAccess->first(fn ($access) => (int) $access->service_id === (int) $service->id && $access->has_access && (! $access->effective_until || $access->effective_until->isFuture()));
                    $subscription = $activeSubscriptionByService->get($service->id);
                    $pendingSubscription = $pendingSubscriptionByService->get($service->id);
                @endphp
                <x-card>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-ink">{{ $service->name }}</h2>
                            <p class="mt-2 text-sm leading-relaxed text-ink/60">{{ $service->description }}</p>
                        </div>
                        @if ($granted)
                            <x-badge tone="success" dot>Enabled</x-badge>
                        @elseif ($pendingSubscription)
                            <x-badge tone="warning">Request pending</x-badge>
                        @else
                            <x-badge tone="neutral">Not enabled</x-badge>
                        @endif
                    </div>

                    @if ($subscription)
                        <p class="mt-4 text-xs text-ink/55">Current plan: <strong class="text-ink">{{ $subscription->plan?->name ?? 'Plan' }}</strong></p>
                    @endif
                    @if ($pendingSubscription)
                        <p class="mt-1 text-xs text-ink/55">Plan request awaiting review: <strong class="text-ink">{{ $pendingSubscription->plan?->name ?? 'Plan' }}</strong></p>
                    @endif

                    <div class="mt-5 space-y-3">
                        @forelse ($service->plans as $plan)
                            <div class="flex flex-col justify-between gap-3 rounded-xl border border-ink/10 p-4 sm:flex-row sm:items-center">
                                <div>
                                    <h3 class="font-medium text-ink">{{ $plan->name }}</h3>
                                    @if ($plan->description)
                                        <p class="mt-1 text-xs text-ink/55">{{ $plan->description }}</p>
                                    @endif
                                    <p class="mt-2 text-sm font-semibold text-ink">
                                        @if (in_array(strtoupper($plan->currency), ['INR'], true))₹@else{{ strtoupper($plan->currency) }} @endif{{ number_format((float) $plan->price, 2) }}
                                        @if ($plan->price > 0)<span class="font-normal text-ink/50">/ {{ $plan->billing_period }}</span>@else<span class="font-normal text-ink/50">/ free</span>@endif
                                    </p>
                                </div>
                                @if ($granted && (int) $subscription?->plan_id === (int) $plan->id)
                                    <span class="shrink-0 text-xs font-medium text-emerald-700">Current plan</span>
                                @elseif ($pendingSubscription && (int) $pendingSubscription->plan_id === (int) $plan->id)
                                    <span class="shrink-0 text-xs font-medium text-amber-700">Pending review</span>
                                @elseif ($pendingSubscription)
                                    <span class="shrink-0 text-xs font-medium text-amber-700">A request is already pending</span>
                                @elseif ($granted && $subscription && $plan->price <= 0)
                                    <span class="shrink-0 text-xs font-medium text-ink/55">Another plan is active</span>
                                @elseif ($plan->price <= 0)
                                    <form method="POST" action="{{ route('dashboard.services.plans.select', $plan->public_id) }}">
                                        @csrf
                                        <button class="shrink-0 rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800" type="submit">{{ $granted ? 'Activate Free plan' : 'Start Free plan' }}</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('dashboard.services.plans.select', $plan->public_id) }}">
                                        @csrf
                                        <button class="shrink-0 rounded-lg border border-brand-700 px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50" type="submit">Request plan</button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <p class="rounded-lg bg-ink/5 p-3 text-sm text-ink/55">There are no active plans for this service yet.</p>
                        @endforelse
                    </div>
                    @if ($service->plans->isNotEmpty() && $service->plans->every(fn ($plan) => (float) $plan->price <= 0))
                        <p class="mt-4 text-xs text-ink/55">Need a paid plan or a higher limit? <a class="font-semibold text-brand-700 underline" href="{{ route('contact') }}">Contact support for current options and pricing.</a></p>
                    @endif
                </x-card>
            @empty
                <x-card class="lg:col-span-2">
                    <p class="text-sm text-ink/60">No services are currently available.</p>
                </x-card>
            @endforelse
        </div>

        <p class="mt-6 text-xs text-ink/50">Paid plans appear here after their pricing is approved. There is no online checkout; contact <a class="font-semibold text-brand-700 underline" href="{{ route('contact') }}">support</a> for current options.</p>
    @endif
</x-layout.dashboard>
