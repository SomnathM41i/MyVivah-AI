@props(['compact' => false])

@php
    $user = auth()->user();
    $platform = $user?->platforms()->latest('id')->first();
@endphp

<div class="flex items-center gap-3 rounded-xl border border-ink/8 bg-white p-3">
    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-brand-600 to-accent-600 text-sm font-semibold text-white">
        {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
    </span>
    <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-medium text-ink">{{ $user?->name }}</p>
        <p class="truncate text-xs text-ink/50">{{ $user?->email }}</p>
    </div>
    @if (! $compact)
        <form method="POST" action="{{ route('logout') }}" data-submit-guard>
            @csrf
            <button
                type="submit"
                class="grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-ink/10 text-ink/60 transition-colors hover:bg-brand-50 hover:text-brand-700"
                aria-label="Logout"
                title="Logout"
            >
                <x-icon name="log-out" class="h-4 w-4" />
            </button>
        </form>
    @endif
</div>

@if ($compact && $platform)
    <p class="mt-3 px-1 text-xs text-ink/50">Platform: <span class="font-medium text-ink/70">{{ $platform->name }}</span></p>
@endif