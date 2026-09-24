@props(['tone' => 'success', 'dismissible' => true])

@php
    $tones = [
        'success' => ['border-emerald-200 bg-emerald-50 text-emerald-800', 'check-circle'],
        'error' => ['border-brand-200 bg-brand-50 text-brand-800', 'alert'],
        'warning' => ['border-amber-200 bg-amber-50 text-amber-800', 'alert'],
        'info' => ['border-accent-200 bg-accent-50 text-accent-800', 'info'],
        'neutral' => ['border-ink/10 bg-ink/5 text-ink-soft', 'info'],
    ];

    [$classes, $icon] = $tones[$tone] ?? $tones['neutral'];
@endphp

<div data-alert {{ $attributes->class(['flex items-start gap-3 rounded-xl border px-4 py-3 text-sm transition-all duration-300', $classes]) }}>
    <x-icon :name="$icon" class="mt-0.5 h-5 w-5 shrink-0" />
    <div class="flex-1">{{ $slot }}</div>
    @if ($dismissible)
        <button type="button" data-dismiss class="shrink-0 rounded-lg p-0.5 opacity-60 transition hover:opacity-100" aria-label="Dismiss">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    @endif
</div>