@php
    $links = [
        ['label' => 'Home', 'href' => route('home'), 'route' => 'home'],
        ['label' => 'About', 'href' => route('about'), 'route' => 'about'],
        ['label' => 'Services', 'href' => route('services.index'), 'route' => 'services.index'],
        ['label' => 'Plans', 'href' => route('plans.index'), 'route' => 'plans.index'],
        ['label' => 'Contact', 'href' => route('contact'), 'route' => 'contact'],
    ];
@endphp

<header class="sticky top-0 z-40 border-b border-ink/8 bg-paper/90 backdrop-blur-md">
    <div class="container-x flex h-16 items-center justify-between gap-4">
        <x-logo />

        <nav class="hidden items-center gap-1 lg:flex" aria-label="Primary">
            @foreach ($links as $link)
                <a
                    href="{{ $link['href'] }}"
                    class="rounded-lg px-3 py-2 text-sm font-medium transition-colors hover:bg-ink/5 {{ request()->routeIs($link['route']) ? 'text-brand-700' : 'text-ink/70 hover:text-ink' }}"
                >
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-2 lg:flex">
            @auth
                <x-button href="{{ route('dashboard') }}" variant="dark" size="sm" icon="grid">
                    Dashboard
                </x-button>
            @else
                <x-button href="{{ route('login') }}" variant="ghost" size="sm">
                    Sign in
                </x-button>
                <x-button href="{{ route('signup') }}" variant="primary" size="sm" icon="arrow-right">
                    Get Started
                </x-button>
            @endauth
        </div>

        <button
            type="button"
            data-mobile-toggle="marketing-mobile-menu"
            aria-expanded="false"
            aria-controls="marketing-mobile-menu"
            class="grid h-10 w-10 place-items-center rounded-xl border border-ink/10 text-ink lg:hidden"
        >
            <x-icon name="menu" class="h-5 w-5" />
        </button>
    </div>

    {{-- Mobile menu --}}
    <div id="marketing-mobile-menu" class="hidden border-t border-ink/8 bg-paper px-4 pb-5 pt-2 lg:hidden">
        <nav class="flex flex-col gap-1" aria-label="Mobile">
            @foreach ($links as $link)
                <a
                    href="{{ $link['href'] }}"
                    class="rounded-lg px-3 py-2.5 text-sm font-medium text-ink/80 transition-colors hover:bg-ink/5 {{ request()->routeIs($link['route']) ? 'text-brand-700' : '' }}"
                >
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>
        <div class="mt-4 flex flex-col gap-2 border-t border-ink/8 pt-4">
            @auth
                <x-button href="{{ route('dashboard') }}" variant="dark" icon="grid" class="w-full">
                    Open Dashboard
                </x-button>
            @else
                <x-button href="{{ route('login') }}" variant="secondary" class="w-full">
                    Sign in
                </x-button>
                <x-button href="{{ route('signup') }}" variant="primary" icon="arrow-right" class="w-full">
                    Get Started
                </x-button>
            @endauth
        </div>
    </div>
</header>