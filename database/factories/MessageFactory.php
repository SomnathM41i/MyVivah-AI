<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\ExternalUserMap;
use App\Models\Message;
use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform_id' => Platform::factory(),
            'conversation_id' => Conversation::factory(),
            'sender_external_user_map_id' => ExternalUserMap::factory(),
            'type' => Message::TYPE_TEXT,
            'status' => Message::STATUS_SENT,
            'content' => fake()->sentence,
            'client_message_id' => null,
        ];
    }

    /**
     * Align every FK + the tenant scope with a real conversation and sender map
     * (the map MUST belong to the conversation's platform).
     */
    public function forConversation(Conversation $conversation, ExternalUserMap $sender): static
    {
        return $this->state(fn (array $attributes) => [
            'platform_id' => $conversation->platform_id,
            'conversation_id' => $conversation->id,
            'sender_external_user_map_id' => $sender->id,
        ]);
    }
}
