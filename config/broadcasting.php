<?php

/*
|--------------------------------------------------------------------------
| Broadcasting Configuration (Phase 3E — realtime is OPTIONAL)
|--------------------------------------------------------------------------
|
| MyVivahAI targets shared hosting with no Redis, no long-lived worker and no
| dedicated realtime server as the DEFAULT. The default connection here is the
| `null` broadcaster — it is a no-op: every chat REST operation works, events
| are gated by config('chat.realtime.enabled') and broadcasts are dispatched
| synchronously (ShouldBroadcastNow) with failures rescued (ShouldRescue), so a
| missing/unreachable realtime server NEVER breaks or slows message persistence.
|
| Enable a realtime server later by pointing BROADCAST_CONNECTION at one of the
| pusher-protocol connections below and setting CHAT_REALTIME_ENABLED=true.
| Reverb / Soketi / Pusher all speak the same protocol, so these driver entry
| points only require the vendor HTTP client package at runtime:
|   composer require pusher/pusher-php-server
| (laravel/reverb additionally ships the `reverb` lane + the server binary.
|  The plain `pusher` driver against a Reverb host works without it.)
|
| Connection  | Protocol              | Needs
| ----------- | --------------------- | ------------------------------------------
| null        | no-op (default)       | nothing — shared-hosting REST-only fallback
| log         | writes to the log     | nothing — local debugging only
| reverb      | Pusher protocol       | laravel/reverb + pusher/pusher-php-server
| soketi      | Pusher protocol       | pusher/pusher-php-server + a Soketi server
| pusher      | Pusher protocol       | pusher/pusher-php-server + Pusher account
| redis       | Laravel redis channel  | a Redis server (optional enhancer)
|
*/

$pusherDefaults = [
    'driver' => 'pusher',
    'key' => env('REVERB_APP_KEY'),
    'secret' => env('REVERB_APP_SECRET'),
    'app_id' => env('REVERB_APP_ID'),
    'options' => [
        'cluster' => env('REVERB_APP_CLUSTER', 'mt1'),
        'host' => env('REVERB_HOST', '127.0.0.1'),
        'port' => env('REVERB_PORT', 8080),
        'scheme' => env('REVERB_SCHEME', 'http'),
        'useTLS' => env('REVERB_SCHEME', 'https') === 'https',
    ],
];

return [

    'default' => env('BROADCAST_CONNECTION', 'null'),

    'connections' => [

        'reverb' => $pusherDefaults,

        'pusher' => $pusherDefaults,

        'soketi' => array_merge($pusherDefaults, [
            'options' => array_merge($pusherDefaults['options'], [
                'host' => env('SOKETI_HOST', '127.0.0.1'),
                'port' => env('SOKETI_PORT', 6001),
            ]),
        ]),

        'ably' => [
            'driver' => 'ably',
            'key' => env('ABLY_KEY'),
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
