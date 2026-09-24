<?php

namespace Tests\Support;

use Illuminate\Contracts\Broadcasting\Broadcaster;

/**
 * Test broadcaster that records every `broadcast()` call in process memory.
 *
 * Registered as the `recording` driver via `Broadcast::extend()`. `onBroadcast`
 * lets a test run assertions AT broadcast time (e.g. "the message row already
 * exists in MySQL") — proving persistence-before-broadcast ordering inside the
 * real Laravel dispatch pipeline.
 */
class RecordingBroadcaster implements Broadcaster
{
    /**
     * @var array<int, array{channels: array<int, string>, event: string, payload: array<string, mixed>}>
     */
    public static array $broadcasts = [];

    /**
     * @var \Closure|null
     */
    public static $onBroadcast = null;

    /**
     * @var \Throwable|null
     */
    public static $throw = null;

    public static function reset(): void
    {
        self::$broadcasts = [];
        self::$onBroadcast = null;
        self::$throw = null;
    }

    /**
     * @return mixed
     */
    public function auth($request)
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function validAuthenticationResponse($request, $result)
    {
        return $result;
    }

    /**
     * @param  array<int, string>  $channels
     * @param  array<string, mixed>  $payload
     */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        if (self::$throw instanceof \Throwable) {
            throw self::$throw;
        }

        if (self::$onBroadcast !== null) {
            (self::$onBroadcast)($channels, (string) $event, $payload);
        }

        self::$broadcasts[] = [
            'channels' => array_map('strval', array_values($channels)),
            'event' => (string) $event,
            'payload' => $payload,
        ];
    }
}
