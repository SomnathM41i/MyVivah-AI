<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversation>
 */
class ConversationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform_id' => Platform::factory(),
            'participant_key' => (string) fake()->unique()->numberBetween(1_000_000, 9_999_999_999),
            'status' => Conversation::STATUS_ACTIVE,
            'last_message_id' => null,
            'last_message_at' => null,
            'last_message_excerpt' => null,
            'last_message_sender_external_user_map_id' => null,
        ];
    }

    /**
     * Attach a specific platform (keeps every child row on the same tenant).
     */
    public function forPlatform(Platform $platform): static
    {
        return $this->state(fn (array $attributes) => [
            'platform_id' => $platform->id,
        ]);
    }
}
