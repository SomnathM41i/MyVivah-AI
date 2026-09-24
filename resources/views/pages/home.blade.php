<x-layout.guest
    :title="$meta['title']"
    :description="$meta['description']"
>
    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="bg-grid absolute inset-0" aria-hidden="true"></div>
        <div class="absolute -top-40 left-1/2 h-105 w-105 -translate-x-1/2 rounded-full bg-brand-400/20 blur-3xl" aria-hidden="true"></div>

        <div class="container-x relative py-20 text-center sm:py-28">
            <span class="animate-fade-up inline-flex items-center gap-2 rounded-full border border-brand-200 bg-brand-50 px-4 py-1.5 text-xs font-semibold text-brand-700">
                <x-icon name="sparkles" class="h-4 w-4" />
                API-first AI services for matrimony platforms
            </span>

            <h1 class="animate-fade-up mx-auto mt-6 max-w-3xl text-4xl font-semibold tracking-tight text-ink sm:text-5xl lg:text-6xl" style="animation-delay:80ms">
                Add real-time chat &amp; AI to your matrimony website — <span class="text-gradient">without re-platforming</span>
            </h1>

            <p class="animate-fade-up mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-ink/60" style="animation-delay:160ms">
                MyVivahAI plugs into your existing platform with simple, secure APIs. Launch instant messaging,
                AI-assisted matchmaking and automation — built privacy-first for the South Asian wedding market.
            </p>

            <div class="animate-fade-up mt-9 flex flex-col items-center justify-center gap-3 sm:flex-row" style="animation-delay:240ms">
                <x-button href="{{ route('signup') }}" variant="primary" size="lg" icon="arrow-right">
                    Get started free
                </x-button>
                <x-button href="{{ route('services.index') }}" variant="secondary" size="lg">
                    Explore services
                </x-button>
            </div>

            <p class="animate-fade-up mt-5 text-sm text-ink/45" style="animation-delay:300ms">
                No credit card required &middot; 14-day free trial &middot; Cancel anytime
            </p>
        </div>
    </section>

    {{-- Trust / platform strip --}}
    <section class="border-y border-ink/8 bg-white">
        <div class="container-x flex flex-col items-center gap-6 py-10 sm:flex-row sm:justify-between">
            <div class="grid grid-cols-3 gap-8 sm:gap-12">
                <div class="text-center sm:text-left">
                    <p class="text-2xl font-semibold text-ink">24/7</p>
                    <p class="text-sm text-ink/55">AI support, always on</p>
                </div>
                <div class="text-center sm:text-left">
                    <p class="text-2xl font-semibold text-ink">&lt; 100ms</p>
                    <p class="text-sm text-ink/55">chat message latency</p>
                </div>
                <div class="text-center sm:text-left">
                    <p class="text-2xl font-semibold text-ink">99.9%</p>
                    <p class="text-sm text-ink/55">targeted uptime SLA</p>
                </div>
            </div>
            <p class="max-w-sm text-sm leading-relaxed text-ink/50">
                Purpose-built for matrimony: profiles, matches, chats and payments handled
                with Indian privacy norms and data residency in mind.
            </p>
        </div>
    </section>

    {{-- What you get --}}
    <section class="container-x py-20 sm:py-24">
        <x-section-heading
            eyebrow="What you get"
            title="Everything your platform needs, packaged as APIs"
            description="Start with real-time chat — then layer on AI agents, automation and security as your members grow."
        />

        <div class="mt-12 grid gap-6 md:grid-cols-3">
            @php
                $features = [
                    ['icon' => 'chat', 'title' => 'Real-time chat & messaging', 'desc' => 'Instant one-to-one and group messaging for your members. Typing indicators, read receipts, presence and history sync — all through a simple REST + PASETO-secured API.'],
                    ['icon' => 'bot', 'title' => 'AI assistants & matchmaking', 'desc' => 'Conversational bots that answer member questions, recommend matches and help onboard new users. Hindi and English first.'],
                    ['icon' => 'shield', 'title' => 'Security & trust', 'desc' => 'Platform-isolated data, encrypted keys, audit trails and abuse controls built in — so your members trust every interaction.'],
                ];
            @endphp
            @foreach ($features as $f)
                <x-card class="flex flex-col">
                    <span class="mb-4 grid h-12 w-12 place-items-center rounded-xl bg-brand-50 text-brand-700">
                        <x-icon :name="$f['icon']" class="h-6 w-6" />
                    </span>
                    <h3 class="text-lg font-semibold text-ink">{{ $f['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-ink/60">{{ $f['desc'] }}</p>
                </x-card>
            @endforeach
        </div>
    </section>

    {{-- How it works --}}
    <section class="border-y border-ink/8 bg-white">
        <div class="container-x grid items-center gap-12 py-20 sm:py-24 lg:grid-cols-2">
            <div>
                <x-section-heading
                    align="left"
                    eyebrow="How it works"
                    title="Live in days, not months"
                    description="No re-platforming, no migration project, no new tech stack. Just connect your platform to our APIs."
                />

                <ol class="mt-9 space-y-6">
                    @php
                        $steps = [
                            ['num' => '1', 'title' => 'Create your platform account', 'desc' => 'Register in under two minutes — you\'ll get a platform workspace and secure integration keys.'],
                            ['num' => '2', 'title' => 'Choose a service & plan', 'desc' => 'Pick the services your members need and a plan that scales with you. Start free.'],
                            ['num' => '3', 'title' => 'Integrate with our API', 'desc' => 'Simple REST endpoints and a drop-in chat widget. Our docs walk you through it.'],
                        ];
                    @endphp
                    @foreach ($steps as $step)
                        <li class="flex gap-4">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-brand-600 to-accent-600 text-sm font-semibold text-white">{{ $step['num'] }}</span>
                            <div>
                                <h4 class="font-semibold text-ink">{{ $step['title'] }}</h4>
                                <p class="mt-1 text-sm leading-relaxed text-ink/60">{{ $step['desc'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>

                <div class="mt-9">
                    <x-button href="{{ route('signup') }}" variant="primary" icon="arrow-right">
                        Start integrating today
                    </x-button>
                </div>
            </div>

            <div class="lg:pl-8">
                <div class="rounded-2xl border border-ink/8 bg-[#1e1c22] p-6 font-mono text-sm text-white shadow-xl">
                    <div class="mb-4 flex items-center gap-1.5">
                        <span class="h-3 w-3 rounded-full bg-[#ff5f57]"></span>
                        <span class="h-3 w-3 rounded-full bg-[#febc2e]"></span>
                        <span class="h-3 w-3 rounded-full bg-[#28c840]"></span>
                        <span class="ml-3 text-xs text-white/40">client platform → MyVivahAI</span>
                    </div>
                    <pre class="overflow-x-auto leading-relaxed text-white/85"><code><span class="text-[#c792ea]">POST</span> <span class="text-[#82aaff]">/api/v1/auth/token</span>
<span class="text-[#89ddff]">Content-Type:</span> application/json

{
  "client_id": "01JKX…platform‑public‑id",
  "client_secret": "v4.local.eyJ…",
  "scope": ["chat:read", "chat:write"]
}

<span class="text-white/40"># → PASETO v4.local bearer token</span></code></pre>
                    <pre class="mt-4 overflow-x-auto leading-relaxed text-white/85"><code><span class="text-[#c792ea]">POST</span> <span class="text-[#82aaff]">/api/v1/conversations</span>
Bearer <span class="text-[#80cbc4]">&lt;token&gt;</span>

{ "member_id": "mx-10042" }

<span class="text-white/40"># → { "conversation_id": "16f8…" }</span></code></pre>
                </div>
            </div>
        </div>
    </section>

    {{-- Privacy --}}
    <section class="container-x py-20 sm:py-24">
        <x-section-heading
            eyebrow="Built on trust"
            title="Private by design, not as an afterthought"
            description="Every feature ships with isolation and data protection first."
        />

        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $privacy = [
                    ['icon' => 'lock', 'title' => 'Isolated per platform', 'desc' => 'Your data is sealed from every other platform on the network.'],
                    ['icon' => 'database', 'title' => 'Encrypted at rest', 'desc' => 'Sensitive material is encrypted and key-gated end-to-end.'],
                    ['icon' => 'activity', 'title' => 'Full audit trail', 'desc' => 'Every API call is logged securely, with hashed IPs only.'],
                    ['icon' => 'alert', 'title' => 'Abuse controls', 'desc' => 'Rate limits, blacklists and anomaly detection out of the box.'],
                ];
            @endphp
            @foreach ($privacy as $p)
                <div class="rounded-2xl border border-ink/8 bg-white p-6">
                    <x-icon :name="$p['icon']" class="h-6 w-6 text-brand-600" />
                    <h3 class="mt-4 font-semibold text-ink">{{ $p['title'] }}</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-ink/60">{{ $p['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Final CTA --}}
    <section class="container-x pb-20 sm:pb-24">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-700 to-accent-700 px-6 py-16 text-center sm:px-12">
            <div class="bg-grid absolute inset-0 opacity-20" aria-hidden="true"></div>
            <div class="relative">
                <h2 class="mx-auto max-w-xl text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                    Ready to grow your matrimony platform?
                </h2>
                <p class="mx-auto mt-4 max-w-lg text-base text-white/80">
                    Join platforms building the future of matchmaking in India. 14 days free, no credit card.
                </p>
                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <x-button href="{{ route('signup') }}" variant="light" size="lg" icon="arrow-right">
                        Create free account
                    </x-button>
                    <x-button href="{{ route('contact') }}" variant="light-outline" size="lg">
                        Talk to sales
                    </x-button>
                </div>
            </div>
        </div>
    </section>
</x-layout.guest>