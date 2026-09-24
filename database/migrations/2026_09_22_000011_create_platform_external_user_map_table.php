<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 3B — `platform_external_user_map`: external→local user identity reference
     * (phase-3a-api-integration-plan.md §5.2/§8; AGENTS.md §15/§24).
     *
     * Data-minimization contract (external platform stays the source of truth):
     *   - Holds ONLY the mapping + sync timestamps + integration sync metadata.
     *   - NO profile mirroring (no name/email/phone/photo) — the full profile
     *     lives on the external platform and is fetched through its API.
     *   - `local_public_id` (nullable) is the optional MyVivahAI user ULID link —
     *     the "local reference" returned to integrations; NULL until linked.
     *   - UNIQUE(platform_id, external_user_id) is the identity key per platform;
     *     one external user may be referenced by DIFFERENT platforms (each with
     *     its own row) → strict platform isolation.
     */
    public function up(): void
    {
        Schema::create('platform_external_user_map', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained('platforms')->cascadeOnDelete();
            $table->string('external_user_id', 255);
            $table->char('local_public_id', 26)->nullable();
            $table->json('metadata')->nullable();        // integration sync metadata only
            $table->timestamp('synced_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['platform_id', 'external_user_id'], 'platform_external_user_identity');
            $table->index('local_public_id');

            $table->foreign('local_public_id')->references('public_id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_external_user_map');
    }
};
