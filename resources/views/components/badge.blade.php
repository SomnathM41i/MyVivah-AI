@props(['tone' => 'neutral', 'dot' => false])

@php
    $tones = [
        'neutral' => 'border-ink/10 bg-ink/5 text-ink-soft',
        'brand' => 'border-brand-200 bg-brand-50 text-brand-700',
        'accent' => 'border-accent-200 bg-accent-50 text-accent-700',
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-700',
        'danger' => 'border-brand-200 bg-brand-50 text-brand-700',
    ];

    $dots = [
        'success' => 'bg-emerald-500',
        'warning' => 'bg-amber-500',
        'danger' => 'bg-brand-500',
        'accent' => 'bg-accent-500',
        'brand' => 'bg-brand-500',
        'neutral' => 'bg-ink/40',
    ];
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-medium', $tones[$tone] ?? $tones['neutral']]) }}>
    @if ($dot)
        <span class="h-1.5 w-1.5 rounded-full {{ $dots[$tone] ?? $dots['neutral'] }}"></span>
    @endif
    {{ $slot }}
</span>