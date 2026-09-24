@props([
    'title' => null,
    'subtitle' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ filled($title) ? $title.' — MyVivahAI' : 'MyVivahAI' }}</title>

    <meta name="robots" content="noindex, nofollow">
    <link rel="canonical" href="{{ request()->url() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-paper">
    <div class="bg-grid absolute inset-0 opacity-60" aria-hidden="true"></div>

    <nav class="relative z-10">
        <div class="container-x flex h-20 items-center justify-between">
            <x-logo />
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-ink/60 transition-colors hover:text-ink">
                <x-icon name="arrow-left" class="h-4 w-4" /> Back to site
            </a>
        </div>
    </nav>

    <main class="relative z-10 flex flex-1 items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <div class="animate-fade-up rounded-2xl border border-ink/8 bg-white p-8 shadow-sm sm:p-10">
                @if ($title)
                    <h1 class="text-2xl font-semibold tracking-tight text-ink">{{ $title }}</h1>
                @endif

                @if ($subtitle)
                    <p class="mt-2 text-sm leading-relaxed text-ink/60">{{ $subtitle }}</p>
                @endif

                <div class="mt-6 space-y-5">
                    <x-flash />
                    {{ $slot }}
                </div>
            </div>

            {{ $footer ?? '' }}
        </div>
    </main>

    <p class="relative z-10 pb-8 text-center text-xs text-ink/40">
        &copy; {{ date('Y') }} MyVivahAI &middot; AI &amp; API platform for matrimony websites
    </p>
</body>
</html>