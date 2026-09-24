@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'type' => 'button',
])

@php
    $variants = [
        'primary' => 'bg-brand-600 text-white shadow-sm hover:bg-brand-700 focus-visible:outline-brand-600',
        'accent' => 'bg-accent-600 text-white shadow-sm hover:bg-accent-700 focus-visible:outline-accent-600',
        'secondary' => 'border border-ink/10 bg-white text-ink shadow-sm hover:border-ink/20 hover:bg-paper focus-visible:outline-ink',
        'ghost' => 'text-ink/80 hover:bg-ink/5 hover:text-ink focus-visible:outline-ink',
        'dark' => 'bg-ink text-white shadow-sm hover:bg-ink-soft focus-visible:outline-ink',
        'danger' => 'bg-brand-700 text-white shadow-sm hover:bg-brand-800 focus-visible:outline-brand-700',
        'light' => 'bg-white text-brand-700 shadow-sm hover:bg-brand-50 focus-visible:outline-white',
        'light-outline' => 'border border-white/40 text-white hover:bg-white/10 focus-visible:outline-white',
    ];

    $sizes = [
        'xs' => 'px-2.5 py-1.5 text-xs',
        'sm' => 'px-3.5 py-2 text-sm',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];

    $classes = 'inline-flex items-center justify-center gap-2 rounded-xl font-medium transition-all duration-150 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed disabled:opacity-60 '.
        ($variants[$variant] ?? $variants['primary']).' '.
        ($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @if ($icon)
            <x-icon :name="$icon" class="h-4 w-4 -ml-0.5" />
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        @if ($icon)
            <x-icon :name="$icon" class="h-4 w-4 -ml-0.5" />
        @endif
        {{ $slot }}
    </button>
@endif