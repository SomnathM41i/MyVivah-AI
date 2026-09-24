@props(['platform'])

@if ($platform)
    <div class="mb-6 flex flex-col gap-4 rounded-2xl border border-ink/8 bg-white p-5 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex min-w-0 items-center gap-4">
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-brand-600 to-accent-600 text-lg font-semibold text-white">
                {{ strtoupper(substr($platform->name, 0, 1)) }}
            </span>
            <div class="min-w-0">
                <p class="truncate text-base font-semibold text-ink">{{ $platform->name }}</p>
                <p class="truncate text-sm text-ink/50">{{ $platform->slug }}</p>
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            @if ($platform->website_url)
                <a href="{{ $platform->website_url }}" target="_blank" rel="noopener noreferrer"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-brand-700 hover:text-brand-800">
                    <x-icon name="globe" class="h-4 w-4" /> Visit site
                </a>
            @endif
            <x-badge
                :tone="$platform->status === 'active' ? 'success' : ($platform->status === 'pending' ? 'warning' : 'danger')"
                dot
            >
                {{ ucfirst($platform->status) }}
            </x-badge>
        </div>
    </div>
@endif