<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 3D — `conversation_participants`: membership + per-user read state
     * (docs/database.md; read cursor per realtime-chat.md §Unread Counts).
     *
     * Contract:
     *   - UNIQUE(conversation_id, external_user_map_id): one seat per user per
     *     conversation; the pair maps a conversation to exactly two rows (MVP).
     *   - Read state per participant: `last_read_message_id` (cursor into
     *     messages.id, FK SET NULL if the message is ever removed) +
     *     `last_read_at` (when the cursor was advanced) + denormalized
     *     `unread_count` fast-path for the conversation list. Unread is advanced
     *     in the message-send transaction and reset in the same transaction as a
     *     read — it never drifts.
     *   - INDEX(platform_id, external_user_map_id) serves "list my conversations".
     */
    public function up(): void
    {
        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->foreignId('platform_id')->constrained('platforms')->cascadeOnDelete();
            $table->foreignId('external_user_map_id')
                ->constrained('platform_external_user_map')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('last_read_message_id')->nullable()->index();
            $table->timestamp('last_read_at')->nullable();
            $table->unsignedInteger('unread_count')->default(0);
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['conversation_id', 'external_user_map_id'],
                'conversation_participants_seat'
            );
            $table->index(
                ['platform_id', 'external_user_map_id'],
                'conversation_participants_user_convs'
            );
            $table->index(['conversation_id', 'last_read_message_id'], 'conversation_participants_read');

            $table->foreign('last_read_message_id')
                ->references('id')
                ->on('messages')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_participants');
    }
};
