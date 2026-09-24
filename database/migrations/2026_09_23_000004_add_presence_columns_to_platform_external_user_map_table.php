<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 3E — presence columns on `platform_external_user_map`.
     *
     * Basic presence (online / offline / last_seen) with NO Redis dependency:
     * state lives here (DB is the source of truth), `presence_seen_at` is the
     * last heartbeat timestamp, and staleness is ALWAYS computed (a seen_at older
     * than config('chat.realtime.presence_offline_after_seconds') reads as
     * offline). `chat:presence-sweep` lazily flips stale rows offline.
     */
    public function up(): void
    {
        Schema::table('platform_external_user_map', function (Blueprint $table) {
            $table->enum('presence_status', ['online', 'offline'])
                ->default('offline')
                ->after('last_seen_at');
            $table->timestamp('presence_seen_at')->nullable()->after('presence_status');

            $table->index(['platform_id', 'presence_status'], 'external_user_map_presence_platform');
        });
    }

    public function down(): void
    {
        Schema::table('platform_external_user_map', function (Blueprint $table) {
            $table->dropIndex('external_user_map_presence_platform');
            $table->dropColumn(['presence_seen_at', 'presence_status']);
        });
    }
};
