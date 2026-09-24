<?php

namespace App\Chat;

use App\Exceptions\ApiException;
use App\Models\ExternalUserMap;
use App\Models\Platform;
use App\Services\ChatConversationService;

/**
 * Server-side gate for realtime channel subscriptions (Phase 3E).
 *
 * A subscription is authorized ONLY when BOTH hold:
 *   - the verified PASETO token belongs to the platform that owns the resource, and
 *   - the `X-External-User-Id` actor is mapped inside THAT platform and is an
 *     actual participant / platform member for the channel it asks for.
 *
 * Channels are signed (Pusher protocol) with the server-only `app_secret`;
 * the returned `auth` string is what the realtime server re-verifies when the
 * client connects, so this endpoint and the server share one secret that is
 * NEVER transferred to browsers. A non-participant or cross-platform caller is
 * indistinguishable from an unknown thread: 403 instead of 404 so channel
 * existence is not disclosed over a channel that shouldn't exist anyway.
 */
class ChatChannelAuthorizer
{
    public function __construct(
        private readonly ChatConversationService $conversations,
    ) {}

    /**
     * @return array{auth: string, channel_data: string|null}
     *
     * @throws ApiException CHANNEL_DENIED (403)
     */
    public function authorize(Platform $platform, ExternalUserMap $actor, string $socketId, string $channel): array
    {
        $conversationPublicId = ChatChannels::parseConversation($channel);

        if ($conversationPublicId !== null) {
            return $this->authorizeConversation($platform, $actor, $socketId, $conversationPublicId, $channel);
        }

        $platformPublicId = ChatChannels::parsePresence($channel);

        if ($platformPublicId !== null) {
            return $this->authorizePresence($platform, $actor, $socketId, $platformPublicId, $channel);
        }

        throw $this->denied();
    }

    /**
     * private-chat.{conversation_public_id} — actor must be a participant.
     *
     * @return array{auth: string, channel_data: string|null}
     */
    private function authorizeConversation(
        Platform $platform,
        ExternalUserMap $actor,
        string $socketId,
        string $conversationPublicId,
        string $channel,
    ): array {
        $conversation = $platform->conversations()
            ->where('public_id', $conversationPublicId)
            ->first();

        if ($conversation === null
            || $this->conversations->participant($conversation, (int) $actor->id) === null) {
            throw $this->denied();
        }

        return [
            'auth' => $this->signature($socketId, $channel),
            'channel_data' => null,
        ];
    }

    /**
     * presence-chat.{platform_public_id} — actor must be a mapped user of that
     * platform (platform public_id must equal the token's own platform).
     *
     * @return array{auth: string, channel_data: string|null}
     */
    private function authorizePresence(
        Platform $platform,
        ExternalUserMap $actor,
        string $socketId,
        string $platformPublicId,
        string $channel,
    ): array {
        if ($platform->public_id !== $platformPublicId) {
            throw $this->denied();
        }

        $channelData = (string) json_encode([
            'user_id' => (string) $actor->id,
            'user_info' => [
                'external_user_id' => $actor->external_user_id,
                'presence_status' => $actor->presence_status ?? ExternalUserMap::PRESENCE_OFFLINE,
            ],
        ]);

        return [
            'auth' => $this->signature($socketId, $channel, $channelData),
            'channel_data' => $channelData,
        ];
    }

    /**
     * Pusher-protocol subscription signature:
     *   auth = "<app_key>:<hmac-sha256(socket_id:channel[:channel_data], app_secret)>"
     * Verified verbatim by any Pusher-protocol server (Reverb / Soketi / Pusher).
     */
    private function signature(string $socketId, string $channel, ?string $channelData = null): string
    {
        $key = (string) config('chat.realtime.app_key', '');
        $secret = (string) config('chat.realtime.app_secret', '');

        $signingString = $channelData !== null
            ? $socketId.':'.$channel.':'.$channelData
            : $socketId.':'.$channel;

        return $key.':'.hash_hmac('sha256', $signingString, $secret);
    }

    private function denied(): ApiException
    {
        return new ApiException('CHANNEL_DENIED', 'Not authorized to subscribe to this channel.', 403);
    }
}
