<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 3D — `conversations`: platform-scoped message threads
     * (docs/database.md Real-Time Chat Domain; docs/realtime-chat.md).
     *
     * Contract:
     *   - `participant_key` = normalized sorted pair of external-user-map ids
     *     ("<lower>:<higher>"), UNIQUE per platform → deterministic resolve for
     *     the same pair, regardless of request order (dedup at DB level).
     *   - `last_message_*` is DENORMALIZED list/sort metadata updated inside the
     *     message-send transaction; `last_message_id` is intentionally a plain
     *     column WITHOUT a FK because `conversations` ↔ `messages` is circular
     *     (messages.conversation_id → conversations, conversations.last_message_id
     *     → messages) — messages are never deleted at MVP, app-side integrity is
     *     enforced transactionally.
     *   - Lifecycle is status-driven (active/archived/closed); NO soft delete so a
     *     soft-deleted row can never shadow the (platform, participant_key) dedup.
     */
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->char('public_id', 26)->unique();
            $table->foreignId('platform_id')->constrained('platforms')->cascadeOnDelete();
            $table->char('participant_key', 64);
            $table->enum('status', ['active', 'archived', 'closed'])->default('active');
            $table->unsignedBigInteger('last_message_id')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->string('last_message_excerpt', 255)->nullable();
            $table->unsignedBigInteger('last_message_sender_external_user_map_id')->nullable();
            $table->timestamps();

            $table->unique(['platform_id', 'participant_key'], 'conversations_platform_pair');
            $table->index(['platform_id', 'last_message_at'], 'conversations_platform_recent');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
