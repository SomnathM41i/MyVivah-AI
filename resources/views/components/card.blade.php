@props(['padding' => true])

<div {{ $attributes->class(['rounded-2xl border border-ink/8 bg-white shadow-sm', $padding ? 'p-6' : '']) }}>
    {{ $slot }}
</div>