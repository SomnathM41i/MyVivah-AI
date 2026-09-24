<?php

namespace App\Services;

/**
 * Realtime connection coordinates for the widget browser client (Phase 4).
 *
 * Everything here is PUBLIC-safe (app key, host, port, path, scheme). The
 * symmetric app secret NEVER leaves the server — the browser subscribes through
 * the existing socket/auth endpoint which requires a valid widget session token.
 */
class WidgetRealtimeConfig
{
    private const BANNED_CONNECTION_MODE = 'null';

    private const CONNECTION_MODES = ['reverb', 'pusher', 'soketi'];

    /**
     * Whether the widget browser client should attempt a live socket.
     */
    public static function enabled(): bool
    {
        return (bool) config('chat.realtime.enabled', false)
            && in_array(self::connectionMode(), self::CONNECTION_MODES, true)
            && self::appKey() !== '';
    }

    public static function connectionMode(): string
    {
        $mode = strtolower((string) config('chat.realtime.connection', 'reverb'));

        if (in_array($mode, self::CONNECTION_MODES, true)) {
            return $mode;
        }

        $fallback = strtolower((string) config('broadcasting.default', self::BANNED_CONNECTION_MODE));
        $fallback = $fallback === 'pusher' ? 'pusher' : $fallback;

        return in_array($fallback, self::CONNECTION_MODES, true) ? $fallback : self::BANNED_CONNECTION_MODE;
    }

    public static function appKey(): string
    {
        return (string) config('chat.realtime.app_key', config('broadcasting.connections.reverb.key', ''));
    }

    /**
     * Public, browser-safe realtime coordinates (never the app secret).
     *
     * @return array{
     *     enabled: bool,
     *     connection: string,
     *     app_key: string,
     *     scheme: string,
     *     host: string,
     *     port: int,
     *     path: string,
     * }
     */
    public static function for(): array
    {
        if (! self::enabled()) {
            return [
                'enabled' => false,
                'connection' => 'none',
                'app_key' => '',
                'scheme' => 'wss',
                'host' => '',
                'port' => 0,
                'path' => '/app/',
            ];
        }

        $scheme = (string) config('chat.realtime.scheme', 'wss');
        $port = (int) config('chat.realtime.port', 443);

        if (self::connectionMode() === 'pusher') {
            $cluster = (string) config('chat.realtime.pusher_cluster', 'mt1');
            $host = 'ws-'.$cluster.'.pusher.com';
            $port = 443;
            $path = '/app/'.self::appKey();
        } else {
            $host = rtrim((string) config('chat.realtime.host', '127.0.0.1'), '/');
            $path = '/app/'.self::appKey();
        }

        return [
            'enabled' => true,
            'connection' => self::connectionMode(),
            'app_key' => self::appKey(),
            'scheme' => $scheme,
            'host' => $host,
            'port' => $port,
            'path' => $path,
        ];
    }
}
