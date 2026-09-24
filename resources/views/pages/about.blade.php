<x-layout.guest
    :title="$meta['title']"
    :description="$meta['description']"
>
    {{-- Header --}}
    <section class="relative overflow-hidden border-b border-ink/8">
        <div class="bg-grid absolute inset-0 opacity-70" aria-hidden="true"></div>
        <div class="container-x relative py-20 text-center sm:py-24">
            <p class="animate-fade-up inline-flex items-center gap-2 rounded-full border border-brand-200 bg-brand-50 px-4 py-1.5 text-xs font-semibold text-brand-700">
                <x-icon name="heart" class="h-4 w-4" />
                Our story
            </p>
            <h1 class="animate-fade-up mx-auto mt-6 max-w-3xl text-4xl font-semibold tracking-tight text-ink sm:text-5xl" style="animation-delay:80ms">
                We make matrimony platforms <span class="text-gradient">smarter, faster and safer</span>
            </h1>
            <p class="animate-fade-up mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-ink/60" style="animation-delay:160ms">
                MyVivahAI is an API-first platform that adds real-time chat, AI assistance and automation
                to matrimony websites — built specifically for the way matchmaking works in South Asia.
            </p>
        </div>
    </section>

    {{-- Mission --}}
    <section class="container-x grid items-center gap-12 py-20 lg:grid-cols-2 sm:py-24">
        <div>
            <x-section-heading
                align="left"
                eyebrow="Why we exist"
                title="Matchmaking is deeply human. Technology should stay out of the way."
                description=""
            />
            <div class="mt-4 space-y-4 text-base leading-relaxed text-ink/60">
                <p>
                    Every great match starts with a great conversation. Yet most matrimony platforms
                    still lean on gated contact forms, slow follow-ups and generic messaging that feels
                    nothing like a wedding ecosystem. Families mediate, languages mix, and trust matters.
                </p>
                <p>
                    We believe platform owners deserve better. Instead of rebuilding their entire product,
                    they should be able to plug in world-class chat and AI services — instantly, securely,
                    and at a price that scales with real usage.
                </p>
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            @php
                $values = [
                    ['icon' => 'heart', 'title' => 'Members first', 'desc' => 'Every design decision starts with respecting the people using your platform.'],
                    ['icon' => 'shield', 'title' => 'Privacy native', 'desc' => 'Data isolation, encryption and audit trails are defaults — not upgrades.'],
                    ['icon' => 'code', 'title' => 'Developer friendly', 'desc' => 'Clean REST APIs, excellent docs and a drop-in widget. No lock-in.'],
                    ['icon' => 'globe', 'title' => 'Bilingual by default', 'desc' => 'Conversations happen in Hindi and English — our AI understands both.'],
                ];
            @endphp
            @foreach ($values as $v)
                <div class="rounded-2xl border border-ink/8 bg-white p-6">
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-brand-50 text-brand-700">
                        <x-icon :name="$v['icon']" class="h-5 w-5" />
                    </span>
                    <h3 class="mt-4 font-semibold text-ink">{{ $v['title'] }}</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-ink/60">{{ $v['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Approach --}}
    <section class="border-y border-ink/8 bg-white">
        <div class="container-x py-20 sm:py-24">
            <x-section-heading
                eyebrow="Our approach"
                title="API-first. Isolated. Honest."
                description="Three principles guide every service we ship."
            />

            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @php
                    $principles = [
                        ['icon' => 'plug', 'title' => 'API-first', 'desc' => 'No proprietary SDKs, no lock-in conventions. If you can call a REST endpoint, you can use MyVivahAI — in PHP, Node, Python or whatever your stack is.'],
                        ['icon' => 'lock', 'title' => 'Platform isolation', 'desc' => 'Each matrimony platform operates in its own sealed tenant. Your members and messages are invisible to everyone else — including our other clients.'],
                        ['icon' => 'clock', 'title' => 'Practical AI', 'desc' => 'We launch AI only where it genuinely helps — moderation, recommendations, member support — and never at the cost of the human matchmaking experience.'],
                    ];
                @endphp
                @foreach ($principles as $p)
                    <x-card>
                        <x-icon :name="$p['icon']" class="h-7 w-7 text-accent-600" />
                        <h3 class="mt-5 text-lg font-semibold text-ink">{{ $p['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink/60">{{ $p['desc'] }}</p>
                    </x-card>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="container-x py-20 text-center sm:py-24">
        <h2 class="mx-auto max-w-xl text-3xl font-semibold tracking-tight text-ink sm:text-4xl">
            Build the future of matchmaking with us
        </h2>
        <p class="mx-auto mt-4 max-w-lg text-base text-ink/60">
            Whether you run a growing matrimony start-up or a national platform, there's a plan for you.
        </p>
        <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <x-button href="{{ route('signup') }}" variant="primary" size="lg" icon="arrow-right">
                Get started free
            </x-button>
            <x-button href="{{ route('contact') }}" variant="secondary" size="lg">
                Contact us
            </x-button>
        </div>
    </section>
</x-layout.guest>