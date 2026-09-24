@props([
    'eyebrow' => null,
    'title' => null,
    'description' => null,
    'align' => 'center',
])

<div {{ $attributes->class([
    'max-w-2xl',
    $align === 'center' ? 'mx-auto text-center' : 'text-left',
]) }}>
    @if ($eyebrow)
        <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-brand-600">{{ $eyebrow }}</p>
    @endif

    @if ($title)
        <h2 class="text-3xl font-semibold tracking-tight text-ink sm:text-4xl">{{ $title }}</h2>
    @endif

    @if ($description)
        <p class="mt-4 text-base leading-relaxed text-ink/60">{{ $description }}</p>
    @endif
</div>