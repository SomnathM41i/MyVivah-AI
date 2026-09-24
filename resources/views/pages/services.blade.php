<x-layout.guest
    :title="$meta['title']"
    :description="$meta['description']"
>
    <section class="relative overflow-hidden border-b border-ink/8">
        <div class="bg-grid absolute inset-0 opacity-70" aria-hidden="true"></div>
        <div class="container-x relative py-20 text-center sm:py-24">
            <p class="animate-fade-up inline-flex items-center gap-2 rounded-full border border-brand-200 bg-brand-50 px-4 py-1.5 text-xs font-semibold text-brand-700">
                <x-icon name="layers" class="h-4 w-4" />
                Service catalog
            </p>
            <h1 class="animate-fade-up mx-auto mt-6 max-w-3xl text-4xl font-semibold tracking-tight text-ink sm:text-5xl" style="animation-delay:80ms">
                Services that grow <span class="text-gradient">with your platform</span>
            </h1>
            <p class="animate-fade-up mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-ink/60" style="animation-delay:160ms">
                Every service is API-first, platform-isolated and ready to integrate today — or listed
                honestly as on our roadmap.
            </p>
        </div>
    </section>

    {{-- Live services --}}
    <section class="container-x py-20 sm:py-24">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight text-ink sm:text-3xl">Available now</h2>
                <p class="mt-2 text-sm text-ink/55">Backed by the live entitlement engine — the same rules that gate the API.</p>
            </div>
            <x-badge tone="success" dot>Live</x-badge>
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($available as $service)
                <x-card class="relative flex flex-col">
                    <div class="flex items-start justify-between">
                        <span class="grid h-12 w-12 place-items-center rounded-xl bg-brand-50 text-brand-700">
                            <x-icon name="{{ in_array($service->key, ['realtime_chat', 'chat'], true) ? 'chat' : 'sparkles' }}" class="h-6 w-6" />
                        </span>
                        <x-badge tone="success" dot>Available</x-badge>
                    </div>
                    <h3 class="mt-5 text-lg font-semibold text-ink">{{ $service->name }}</h3>
                    <p class="mt-2 flex-1 text-sm leading-relaxed text-ink/60">{{ $service->description }}</p>
                    <a href="{{ route('plans.index') }}" class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700 transition-colors hover:text-brand-800">
                        View plans <x-icon name="chevron-right" class="h-4 w-4" />
                    </a>
                </x-card>
            @empty
                <x-card class="md:col-span-2 lg:col-span-3">
                    <div class="flex items-center gap-3 text-sm text-ink/60">
                        <x-icon name="info" class="h-5 w-5" />
                        We're preparing the first live service catalog. Check back shortly.
                    </div>
                </x-card>
            @endforelse
        </div>
    </section>

    {{-- Roadmap --}}
    <section class="border-y border-ink/8 bg-white">
        <div class="container-x py-20 sm:py-24">
            <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h2 class="text-2xl font-semibold tracking-tight text-ink sm:text-3xl">On the roadmap</h2>
                    <p class="mt-2 text-sm text-ink/55">Shipped in upcoming phases — we only mark things live when they truly are.</p>
                </div>
                <x-badge tone="neutral">Coming soon</x-badge>
            </div>

            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($roadmap as $item)
                    <div class="rounded-2xl border border-dashed border-ink/15 bg-paper/60 p-6">
                        <span class="block text-ink/40">
                            <x-icon :name="$item['icon']" class="h-6 w-6" />
                        </span>
                        <h3 class="mt-4 font-semibold text-ink">{{ $item['name'] }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-ink/60">{{ $item['description'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-12 rounded-2xl border border-brand-200 bg-brand-50 p-8 text-center">
                <h3 class="text-xl font-semibold text-ink">Want a service built first?</h3>
                <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-ink/60">
                    Tell us what your members need — we prioritise the roadmap around real platform demand.
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    <x-button href="{{ route('contact') }}" variant="primary" icon="mail">
                        Request a service
                    </x-button>
                </div>
            </div>
        </div>
    </section>
</x-layout.guest>