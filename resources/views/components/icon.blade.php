@props(['name' => 'sparkles', 'class' => 'h-5 w-5'])

@php
    $paths = [
        'sparkles' => [
            '<path d="M12 3l1.9 4.6L18.5 9.5l-4.6 1.9L12 16l-1.9-4.6L5.5 9.5l4.6-1.9L12 3Z"/>',
            '<path d="M19 14l.8 2.2L22 17l-2.2.8L19 20l-.8-2.2L16 17l2.2-.8L19 14Z"/>',
            '<path d="M5 15l.6 1.6L7.2 17l-1.6.6L5 19l-.6-1.6L2.8 17l1.6-.4L5 15Z"/>',
        ],
        'bot' => [
            '<rect x="4" y="8" width="16" height="12" rx="3"/>',
            '<path d="M12 8V4"/>',
            '<circle cx="12" cy="3" r="1"/>',
            '<path d="M9.5 13.5h.01M14.5 13.5h.01"/>',
            '<path d="M9 16.5c.9.8 2.1 1.2 3 1.2s2.1-.4 3-1.2"/>',
        ],
        'chat' => [
            '<path d="M21 12a8 8 0 0 1-8 8H5.5L3 22V12a8 8 0 0 1 8-8h2a8 8 0 0 1 8 8Z"/>',
            '<path d="M8.5 11h7M8.5 14.5h4"/>',
        ],
        'zap' => ['<path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z"/>'],
        'code' => ['<path d="m8 6-6 6 6 6M16 6l6 6-6 6"/>'],
        'shield' => [
            '<path d="M12 3 5 6v6c0 4.4 3 7.4 7 9 4-1.6 7-4.6 7-9V6l-7-3Z"/>',
            '<path d="m9 12 2 2 4-4"/>',
        ],
        'lock' => [
            '<rect x="4.5" y="10.5" width="15" height="10" rx="2.5"/>',
            '<path d="M8 10.5V8a4 4 0 0 1 8 0v2.5"/>',
        ],
        'check' => ['<path d="m5 13 4 4L19 7"/>'],
        'check-circle' => [
            '<circle cx="12" cy="12" r="9"/>',
            '<path d="m8.5 12.5 2.5 2.5 5-5.5"/>',
        ],
        'arrow-right' => ['<path d="M5 12h14M13 6l6 6-6 6"/>'],
        'arrow-left' => ['<path d="M19 12H5M11 6l-6 6 6 6"/>'],
        'menu' => ['<path d="M4 7h16M4 12h16M4 17h16"/>'],
        'x' => ['<path d="M6 6l12 12M18 6 6 18"/>'],
        'mail' => [
            '<rect x="3" y="5" width="18" height="14" rx="2.5"/>',
            '<path d="m4 7 8 6 8-6"/>',
        ],
        'phone' => [
            '<path d="M6 3h3l1.8 5-2.3 1.5a12 12 0 0 0 5.9 5.9L16 13.2l5 1.8v3a2 2 0 0 1-2.2 2A17 17 0 0 1 4 5.2 2 2 0 0 1 6 3Z"/>',
        ],
        'globe' => [
            '<circle cx="12" cy="12" r="9"/>',
            '<path d="M3 12h18M12 3c2.5 2.6 3.8 5.7 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.7-3.8-9S9.5 5.6 12 3Z"/>',
        ],
        'settings' => [
            '<circle cx="12" cy="12" r="3"/>',
            '<path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"/>',
        ],
        'grid' => [
            '<rect x="3" y="3" width="7.5" height="7.5" rx="1.5"/><rect x="13.5" y="3" width="7.5" height="7.5" rx="1.5"/><rect x="3" y="13.5" width="7.5" height="7.5" rx="1.5"/><rect x="13.5" y="13.5" width="7.5" height="7.5" rx="1.5"/>',
        ],
        'layers' => ['<path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 13 9 5 9-5M3 17.5 12 22l9-4.5"/>'],
        'plug' => [
            '<path d="M9 3v5M15 3v5"/>',
            '<path d="M6.5 8h11V11a5.5 5.5 0 0 1-11 0V8Z"/>',
            '<path d="M12 16.5V21"/>',
        ],
        'credit-card' => [
            '<rect x="3" y="5" width="18" height="14" rx="2.5"/>',
            '<path d="M3 10h18M7 15h4"/>',
        ],
        'refresh' => ['<path d="M20 5v5h-5M4 19v-5h5"/><path d="M20 9A8 8 0 0 0 6.3 6.3L4 9M4 15a8 8 0 0 0 13.7 2.7L20 15"/>'],
        'log-out' => ['<path d="M9 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h4"/><path d="M15 8l4 4-4 4M9 12h10"/>'],
        'user' => [
            '<circle cx="12" cy="8" r="4"/>',
            '<path d="M4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/>',
        ],
        'users' => [
            '<circle cx="9" cy="8" r="3.5"/>',
            '<path d="M2.5 20c0-3.3 2.9-5.5 6.5-5.5s6.5 2.2 6.5 5.5"/>',
            '<path d="M16 5.2a3.5 3.5 0 0 1 0 5.6M17.5 14.7c2 .8 3.5 2.4 3.5 5.3"/>',
        ],
        'link' => ['<path d="M10 14a4 4 0 0 0 5.6 0l3-3a4 4 0 1 0-5.6-5.6l-1.2 1.2"/><path d="M14 10a4 4 0 0 0-5.6 0l-3 3a4 4 0 1 0 5.6 5.6l1.2-1.2"/>'],
        'heart' => ['<path d="M12 20.5C7 16.5 3.5 13.3 3.5 9.6 3.5 7 5.5 5 8 5c1.6 0 3.1.8 4 2.2C12.9 5.8 14.4 5 16 5c2.5 0 4.5 2 4.5 4.6 0 3.7-3.5 6.9-8.5 10.9Z"/>'],
        'clock' => ['<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>'],
        'activity' => ['<path d="M3 12h4l2.5-7 5 14 2.5-7h4"/>'],
        'database' => ['<ellipse cx="12" cy="5.5" rx="8" ry="3"/><path d="M4 5.5V12c0 1.7 3.6 3 8 3s8-1.3 8-3V5.5"/><path d="M4 12v6.5c0 1.7 3.6 3 8 3s8-1.3 8-3V12"/>'],
        'rocket' => [
            '<path d="M12 15c-1.5 0-3-3.5-3-6a3.5 3.5 0 0 1 7 0c0 2.5-1.5 6-3 6Z"/>',
            '<path d="M9 10.5H4.5L3 14l4-1.5M15 10.5h4.5L21 14l-4-1.5"/>',
            '<path d="M11 14.5 9.5 21l4-2 1-3"/>',
            '<circle cx="12" cy="9" r="1.3"/>',
        ],
        'briefcase' => [
            '<rect x="3" y="7" width="18" height="13" rx="2.5"/>',
            '<path d="M8.5 7V5.5A2.5 2.5 0 0 1 11 3h2a2.5 2.5 0 0 1 2.5 2.5V7M3 12.5h18"/>',
        ],
        'info' => ['<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8v.01"/>'],
        'alert' => ['<path d="M12 4 2.5 20h19L12 4Z"/><path d="M12 10v4M12 17v.01"/>'],
        'chevron-down' => ['<path d="m6 9 6 6 6-6"/>'],
        'chevron-right' => ['<path d="m9 6 6 6-6 6"/>'],
        'plus' => ['<path d="M12 5v14M5 12h14"/>'],
        'search' => ['<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>'],
        'eye' => ['<path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/>'],
        'eye-off' => ['<path d="M3 3l18 18M10.6 5.1A9.8 9.8 0 0 1 12 5c6 0 9.5 7 9.5 7a17.5 17.5 0 0 1-2.4 3.3M6.1 6.1C3.6 7.8 2.5 12 2.5 12S6 19 12 19a9.4 9.4 0 0 0 4.3-1"/>'],
        'nav' => ['<rect x="3" y="3" width="7.5" height="7.5" rx="1.5"/><rect x="13.5" y="3" width="7.5" height="7.5" rx="1.5"/><rect x="3" y="13.5" width="7.5" height="7.5" rx="1.5"/><rect x="13.5" y="13.5" width="7.5" height="7.5" rx="1.5"/>'],
        'docs' => ['<path d="M6 3h9l4 4v14H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/><path d="M14 3v4h4M9 13h6M9 17h4"/>'],
    ];
@endphp

<svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.8"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    {{ $attributes->merge(['class' => $class]) }}
>
    @foreach ($paths[$name] ?? $paths['sparkles'] as $path)
        {!! $path !!}
    @endforeach
</svg>