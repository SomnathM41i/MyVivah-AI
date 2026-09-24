@props([
    'title' => 'Dashboard',
    'breadcrumbs' => [],
])

@php
    $nav = [
        ['label' => 'Dashboard', 'href' => route('dashboard'), 'icon' => 'grid', 'route' => 'dashboard', 'exact' => true],
        ['label' => 'Services', 'href' => route('dashboard.services'), 'icon' => 'layers', 'route' => 'dashboard.services'],
        ['label' => 'Integrations', 'href' => route('dashboard.integrations'), 'icon' => 'plug', 'route' => 'dashboard.integrations'],
        ['label' => 'Plan & Subscription', 'href' => route('dashboard.subscription'), 'icon' => 'credit-card', 'route' => 'dashboard.subscription'],
        ['label' => 'Payments', 'href' => route('dashboard.payments'), 'icon' => 'activity', 'route' => 'dashboard.payments'],
        ['label' => 'Settings', 'href' => route('dashboard.settings'), 'icon' => 'settings', 'route' => 'dashboard.settings'],
    ];

    $isActive = fn (array $item): bool => request()->routeIs($item['route'])
        || (($item['exact'] ?? false) === false && str_starts_with((string) request()->route()->getName(), $item['route'].'.'));
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }} — MyVivahAI Dashboard</title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f6f5f7]">
    <div class="min-h-screen lg:flex">
        {{-- Sidebar (desktop) --}}
        <aside class="sticky top-0 hidden h-screen w-64 shrink-0 flex-col border-r border-ink/8 bg-white lg:flex">
            <div class="flex h-16 items-center border-b border-ink/8 px-5">
                <x-logo />
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5" aria-label="Dashboard">
                @foreach ($nav as $item)
                    <a
                        href="{{ $item['href'] }}"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ $isActive($item) ? 'bg-brand-50 text-brand-700' : 'text-ink/60 hover:bg-ink/5 hover:text-ink' }}"
                    >
                        <x-icon :name="$item['icon']" class="h-5 w-5" />
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-ink/8 p-3">
                @include('partials.dashboard-user-card')
            </div>
        </aside>

        {{-- Mobile sidebar overlay --}}
        <div id="dashboard-mobile-menu" class="fixed inset-0 z-50 hidden lg:hidden">
            <div class="absolute inset-0 bg-ink/40 backdrop-blur-sm" data-mobile-toggle="dashboard-mobile-menu" aria-hidden="true"></div>

            <aside class="relative flex h-full w-72 max-w-[85vw] flex-col border-r border-ink/8 bg-white">
                <div class="flex h-16 items-center justify-between border-b border-ink/8 px-5">
                    <x-logo />
                    <button type="button" data-mobile-toggle="dashboard-mobile-menu" class="grid h-9 w-9 place-items-center rounded-lg border border-ink/10 text-ink" aria-label="Close menu">
                        <x-icon name="x" class="h-5 w-5" />
                    </button>
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5" aria-label="Dashboard mobile">
                    @foreach ($nav as $item)
                        <a
                            href="{{ $item['href'] }}"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors {{ $isActive($item) ? 'bg-brand-50 text-brand-700' : 'text-ink/60 hover:bg-ink/5 hover:text-ink' }}"
                        >
                            <x-icon :name="$item['icon']" class="h-5 w-5" />
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="border-t border-ink/8 p-3">
                    @include('partials.dashboard-user-card', ['compact' => true])
                </div>
            </aside>
        </div>

        {{-- Main column --}}
        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 border-b border-ink/8 bg-white/90 backdrop-blur-md">
                <div class="flex h-16 items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button
                            type="button"
                            data-mobile-toggle="dashboard-mobile-menu"
                            class="grid h-10 w-10 place-items-center rounded-xl border border-ink/10 text-ink lg:hidden"
                            aria-label="Open menu"
                        >
                            <x-icon name="menu" class="h-5 w-5" />
                        </button>

                        <div class="min-w-0">
                            <h1 class="truncate text-lg font-semibold tracking-tight text-ink">{{ $title }}</h1>
                            @if (count($breadcrumbs))
                                <nav class="hidden items-center gap-1.5 text-xs text-ink/50 sm:flex" aria-label="Breadcrumb">
                                    @foreach ($breadcrumbs as $crumb)
                                        @if ($loop->last)
                                            <span class="font-medium text-ink/70">{{ $crumb }}</span>
                                        @else
                                            <span>{{ $crumb }}</span>
                                            <x-icon name="chevron-right" class="h-3 w-3" />
                                        @endif
                                    @endforeach
                                </nav>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <form method="POST" action="{{ route('logout') }}" data-submit-guard>
                            @csrf
                            <x-button type="submit" variant="ghost" size="sm" icon="log-out" class="hidden sm:inline-flex">
                                Logout
                            </x-button>
                        </form>

                        <div class="flex items-center gap-3 border-l border-ink/8 pl-4">
                            <div class="hidden text-right sm:block">
                                <p class="text-sm font-medium leading-tight text-ink">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-ink/50">{{ auth()->user()->platforms()->latest('id')->value('name') ?? 'MyVivahAI' }}</p>
                            </div>
                            <span class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br from-brand-600 to-accent-600 text-sm font-semibold text-white">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-4 py-8 sm:px-6 lg:px-8">
                <x-flash />
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>