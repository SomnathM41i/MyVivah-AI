<x-layout.dashboard title="Services" :breadcrumbs="$breadcrumbs">
    @include('partials.dashboard.platform-summary', ['platform' => $platform])

    @php
        $availableServices = \App\Models\Service::query()->where('is_active', true)->orderBy('name')->get();
    @endphp

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($availableServices as $service)
            @php
                $granted = $serviceAccess->first(fn ($access) => (int) $access->service_id === (int) $service->id);
            @endphp
            <x-card :class="$granted?->has_access ? 'border-emerald-200' : ''">
                <div class="flex items-start justify-between">
                    <span class="grid h-12 w-12 place-items-center rounded-xl bg-brand-50 text-brand-700">
                        <x-icon :name="in_array($service->key, ['realtime_chat', 'chat'], true) ? 'chat' : 'sparkles'" class="h-6 w-6" />
                    </span>
                    @if ($granted?->has_access)
                        <x-badge tone="success" dot>Enabled</x-badge>
                    @else
                        <x-badge tone="neutral">Not enabled</x-badge>
                    @endif
                </div>
                <h3 class="mt-5 text-lg font-semibold text-ink">{{ $service->name }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-ink/60">{{ $service->description }}</p>
                <p class="mt-4 text-xs text-ink/45">Service key: <code class="rounded bg-ink/5 px-1.5 py-0.5 font-mono">{{ $service->key }}</code></p>
            </x-card>
        @empty
            <x-card class="sm:col-span-2 lg:col-span-3">
                <p class="flex items-center gap-3 text-sm text-ink/60">
                    <x-icon name="info" class="h-5 w-5" /> No live services yet — check back soon.
                </p>
            </x-card>
        @endforelse
    </div>

    <div class="mt-8 grid gap-6 md:grid-cols-2">
        <x-card>
            <h3 class="font-semibold text-ink">How entitlements work</h3>
            <p class="mt-2 text-sm leading-relaxed text-ink/60">
                Services shown as <strong class="font-medium">Enabled</strong> already grant your platform API access.
                Others become available once a subscription activates them. Nothing here changes your
                existing setup — entitlements are additive and scoped to your platform.
            </p>
        </x-card>

        <x-card>
            <h3 class="font-semibold text-ink">Explore the catalog</h3>
            <p class="mt-2 text-sm leading-relaxed text-ink/60">
                See every current and planned service on the public catalog page.
            </p>
            <x-button href="{{ route('services.index') }}" variant="secondary" size="sm" class="mt-4">
                Browse services
            </x-button>
        </x-card>
    </div>
</x-layout.dashboard>