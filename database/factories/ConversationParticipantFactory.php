<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\ExternalUserMap;
use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConversationParticipant>
 */
class ConversationParticipantFactory extends Factory
{
    /**
     * Standalone default; prefer forSeat() so conversation/platform/map are
     * guaranteed to sit on the same tenant (isolation invariant).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'platform_id' => Platform::factory(),
            'external_user_map_id' => ExternalUserMap::factory(),
            'last_read_message_id' => null,
            'last_read_at' => null,
            'unread_count' => 0,
            'joined_at' => now(),
            'left_at' => null,
        ];
    }

    /**
     * Seat a specific external user in a specific conversation. The map MUST
     * belong to the conversation's platform (callers derive it from the same
     * verified token context), keeping every FK and the isolation scope aligned.
     */
    public function forSeat(Conversation $conversation, ExternalUserMap $map): static
    {
        return $this->state(fn (array $attributes) => [
            'conversation_id' => $conversation->id,
            'platform_id' => $conversation->platform_id,
            'external_user_map_id' => $map->id,
        ]);
    }
}
