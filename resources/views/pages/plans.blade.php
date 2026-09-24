<x-layout.guest
    :title="$meta['title']"
    :description="$meta['description']"
>
    <section class="relative overflow-hidden border-b border-ink/8">
        <div class="bg-grid absolute inset-0 opacity-70" aria-hidden="true"></div>
        <div class="container-x relative py-20 text-center sm:py-24">
            <p class="animate-fade-up inline-flex items-center gap-2 rounded-full border border-brand-200 bg-brand-50 px-4 py-1.5 text-xs font-semibold text-brand-700">
                <x-icon name="credit-card" class="h-4 w-4" />
                Plans &amp; pricing
            </p>
            <h1 class="animate-fade-up mx-auto mt-6 max-w-3xl text-4xl font-semibold tracking-tight text-ink sm:text-5xl" style="animation-delay:80ms">
                Simple, transparent plans <span class="text-gradient">for matrimony platforms</span>
            </h1>
            <p class="animate-fade-up mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-ink/60" style="animation-delay:160ms">
                Start free, upgrade as your member base grows. Every plan is backed by the same
                secure, isolated API — you're only paying for capacity and features.
            </p>
        </div>
    </section>

    @php
        $featureLabels = [
            'chat_1v1' => fn (mixed $v) => '1:1 chat for members',
            'chat_group' => fn (mixed $v) => 'Group chat',
            'group_member_limit' => fn (mixed $v) => "Up to {$v} members per group",
            'history_days' => fn (mixed $v) => "{$v} days of message history",
            'history_export' => fn (mixed $v) => 'History export',
            'concurrent_sessions' => fn (mixed $v) => "{$v} concurrent active sessions",
            'moderation_api' => fn (mixed $v) => 'Moderation API',
        ];
    @endphp

    <section class="container-x py-20 sm:py-24">
        @if ($plans->isEmpty())
            <x-card class="mx-auto max-w-xl">
                <div class="flex items-center gap-3 text-sm text-ink/60">
                    <x-icon name="info" class="h-5 w-5" />
                    Plan details are being finalised. Contact us and we'll set you up manually.
                </div>
            </x-card>
        @else
            <div class="mx-auto grid max-w-5xl gap-6 lg:grid-cols-3">
                @foreach ($plans as $plan)
                    @php
                        $highlight = $plan->plan_key === 'growth';
                        $features = [];
                        foreach ($plan->features ?? [] as $key => $value) {
                            if (isset($featureLabels[$key])) {
                                $features[] = $featureLabels[$key]($value);
                            }
                        }
                    @endphp

                    <x-card :padding="$highlight ? false : true" class="{{ $highlight ? 'relative ring-2 ring-brand-600' : 'relative' }}">
                        @if ($highlight)
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2">
                                <x-badge tone="brand">Most popular</x-badge>
                            </span>
                        @endif

                        <h2 class="text-lg font-semibold text-ink">{{ $plan->name }}</h2>
                        <p class="mt-1.5 min-h-10 text-sm text-ink/55">{{ $plan->description }}</p>

                        <div class="mt-5 flex items-baseline gap-1">
                            <span class="text-4xl font-semibold tracking-tight text-ink">
                                ₹{{ number_format((float) $plan->price, 0) }}
                            </span>
                            <span class="text-sm text-ink/50">/ {{ $plan->billing_period }}</span>
                        </div>

                        <ul class="mt-6 space-y-2.5 text-sm text-ink/70">
                            @foreach ($features as $feature)
                                <li class="flex items-start gap-2.5">
                                    <x-icon name="check" :class="'mt-0.5 h-4 w-4 shrink-0 '.($highlight ? 'text-brand-600' : 'text-accent-600')" />
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>

                        @auth
                            <div class="mt-8">
                                <x-badge tone="neutral">Checkout arrives in a later phase — contact us to activate</x-badge>
                            </div>
                        @else
                            <x-button href="{{ route('signup') }}" :variant="$highlight ? 'primary' : 'secondary'" class="mt-8 w-full" icon="arrow-right">
                                Start with {{ $plan->name }}
                            </x-button>
                        @endauth
                    </x-card>
                @endforeach
            </div>

            <p class="mx-auto mt-10 max-w-xl text-center text-sm text-ink/50">
                Prices in INR, billed monthly. No hidden fees, no card required for the Free plan.
                Prefer invoicing? <a href="{{ route('contact') }}" class="font-medium text-brand-700 hover:text-brand-800">Talk to us</a>.
            </p>
        @endif
    </section>

    @auth
        <section class="container-x pb-20 sm:pb-24">
            <div class="rounded-2xl border border-brand-200 bg-brand-50 p-8 text-center">
                <h2 class="text-xl font-semibold text-ink">Already have an account?</h2>
                <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-ink/60">
                    Manage your plan and subscriptions from your dashboard.
                </p>
                <x-button href="{{ route('dashboard.subscription') }}" variant="primary" icon="credit-card" class="mt-6">
                    View my subscription
                </x-button>
            </div>
        </section>
    @endauth
</x-layout.guest>