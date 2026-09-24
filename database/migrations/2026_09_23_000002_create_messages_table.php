<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 3D — `messages`: a single chat entry (docs/database.md).
     *
     * Contract:
     *   - STRICTLY platform-scoped: `platform_id` on every row plus
     *     `conversation_id` (which is itself platform-scoped). Cross-platform
     *     access is structurally impossible.
     *   - Idempotency: UNIQUE(platform_id, conversation_id, client_message_id) —
     *     the client id is scoped to the platform AND the conversation so the
     *     same key can never collide across conversations/platforms; the widget's
     *     retry of a failed send converges on the original row.
     *   - Soft delete (`deleted_at`) reserved for a future recall scenario
     *     (database.md notes soft delete for messages); nothing deletes at MVP.
     *   - `sender_external_user_map_id` FK is SET NULL on identity removal so
     *     chat history always survives (the map holds no PII to purge).
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->char('public_id', 26)->unique();
            $table->foreignId('platform_id')->constrained('platforms')->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->foreignId('sender_external_user_map_id')
                ->nullable()
                ->constrained('platform_external_user_map')
                ->nullOnDelete();
            $table->enum('type', ['text', 'image', 'file', 'system'])->default('text');
            $table->enum('status', ['sent', 'delivered', 'read'])->default('sent');
            $table->text('content');
            $table->char('client_message_id', 64)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(
                ['platform_id', 'conversation_id', 'client_message_id'],
                'messages_platform_conversation_client'
            );
            $table->index(['conversation_id', 'id'], 'messages_conversation_cursor');
            $table->index(['conversation_id', 'created_at'], 'messages_conversation_created');
            $table->index('sender_external_user_map_id', 'messages_sender_map');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
