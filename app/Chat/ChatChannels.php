<?php

namespace App\Chat;

use App\Models\Conversation;
use App\Models\Platform;

/**
 * Realtime channel naming (Phase 3E).
 *
 * Pusher convention: `private-*` = private channels, `presence-*` = presence
 * channels. MyVivahAI uses:
 *   private-chat.{conversation public_id}  — thread events (message.created,
 *                                            message.read, conversation.updated)
 *   presence-chat.{platform public_id}     — platform presence (user.online /
 *                                            user.offline)
 *
 * Channel names are public and intentionally carry ONLY public ids (ULIDs) —
 * never internal primary keys, secrets or profile data.
 */
final class ChatChannels
{
    /**
     * Client-visible private conversation channel, as the realtime server and
     * the subscription-auth endpoint see it: `private-chat.{public_id}`.
     */
    public static function conversation(Conversation $conversation): string
    {
        return self::privatePrefix().'.'.$conversation->public_id;
    }

    /**
     * Client-visible platform presence channel: `presence-chat.{public_id}`.
     */
    public static function presence(Platform $platform): string
    {
        return self::presencePrefix().'.'.$platform->public_id;
    }

    /**
     * Bare name for `new PrivateChannel(...)`. Laravel's PrivateChannel class
     * prepends `private-` itself, so the framework object must receive the name
     * WITHOUT that prefix or channels double-prefix (`private-private-chat.*`).
     */
    public static function conversationTransport(Conversation $conversation): string
    {
        return self::transport(self::privatePrefix()).'.'.$conversation->public_id;
    }

    /**
     * Bare name for `new PresenceChannel(...)` (Laravel prepends `presence-`).
     */
    public static function presenceTransport(Platform $platform): string
    {
        return self::transport(self::presencePrefix()).'.'.$platform->public_id;
    }

    /**
     * Detect a private conversation channel (client name) and return its public_id.
     */
    public static function parseConversation(string $channel): ?string
    {
        $prefix = self::privatePrefix().'.';

        return str_starts_with($channel, $prefix) && strlen($channel) > strlen($prefix)
            ? substr($channel, strlen($prefix))
            : null;
    }

    /**
     * Detect a presence channel (client name) and return its platform public_id.
     */
    public static function parsePresence(string $channel): ?string
    {
        $prefix = self::presencePrefix().'.';

        return str_starts_with($channel, $prefix) && strlen($channel) > strlen($prefix)
            ? substr($channel, strlen($prefix))
            : null;
    }

    public static function isKnown(string $channel): bool
    {
        return self::parseConversation($channel) !== null
            || self::parsePresence($channel) !== null;
    }

    private static function transport(string $prefix): string
    {
        if (str_starts_with($prefix, 'private-')) {
            return substr($prefix, strlen('private-'));
        }

        if (str_starts_with($prefix, 'presence-')) {
            return substr($prefix, strlen('presence-'));
        }

        return $prefix;
    }

    private static function privatePrefix(): string
    {
        return (string) config('chat.realtime.channel_names.private', 'private-chat');
    }

    private static function presencePrefix(): string
    {
        return (string) config('chat.realtime.channel_names.presence', 'presence-chat');
    }
}
