@props(['class' => 'h-8 w-auto'])

<a href="{{ route('home') }}" class="inline-flex shrink-0 items-center gap-2 {{ $class }}" aria-label="MyVivahAI home">
    <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-brand-600 to-accent-600 text-white shadow-sm">
        <x-icon name="heart" class="h-5 w-5" />
    </span>
    <span class="text-lg font-semibold tracking-tight text-ink">
        MyVivah<span class="text-gradient">AI</span>
    </span>
</a>