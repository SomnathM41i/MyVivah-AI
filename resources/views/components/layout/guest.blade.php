@props([
    'title' => null,
    'description' => 'API-first AI platform for matrimony websites — real-time chat, AI agents, automation and security services you can integrate into your existing platform.',
])

@php
    $pageTitle = filled($title)
        ? $title.' — MyVivahAI'
        : 'MyVivahAI — AI & API Platform for Matrimony Websites';

    $canonical = request()->url();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $description }}">

    {{-- Canonical + search robots (public marketing pages are indexable). --}}
    <link rel="canonical" href="{{ $canonical }}">
    <meta name="robots" content="index, follow">

    {{-- Open Graph / Twitter --}}
    <meta property="og:site_name" content="MyVivahAI">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $description }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">
    {{-- Top brand bar on non-home pages --}}
    @include('partials.marketing-nav')

    <main class="flex-1">
        <div class="container-x pt-6">
            <x-flash />
        </div>
        {{ $slot }}
    </main>

    @include('partials.marketing-footer')

    <noscript>
        <div class="fixed inset-x-0 bottom-0 z-50 border-t border-ink/10 bg-white px-4 py-3 text-center text-sm text-ink/70">
            MyVivahAI works best with JavaScript enabled.
        </div>
    </noscript>
</body>
</html>