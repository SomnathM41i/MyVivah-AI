<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 3B — T9 `platform_integrations`: platform registration + integration config
     * (api-integration-plan.md §5.2 / §6). 1:1 registration contract — one integration row per
     * platform (UNIQUE platform_id), the authentication+authorization root for that platform's
     * API access (platform-isolation.md §3 / §5).
     *
     * Columns:
     *   - platform_id    FK → platforms (UNIQUE — 1:1). Integration is the platform's API root.
     *   - status         ENUM(pending,active,suspended,deactivated) default `pending` — gate for
     *                    token issuance/validation (mirrors platforms.status; integration lifecycle).
     *   - paseto_version VARCHAR(16) default `v4.local` — locked at MVP (ADR-013). Reserved so the
     *                    column exists before any future version bump; rejected values validated in
     *                    the request layer.
     *   - token_ttl_seconds  INT UNSIGNED default 3600 — max token lifetime (≤1h MVP, §6.3).
     *   - rate_limit_per_minute SMALLINT UNSIGNED default 60 — per-platform throttle (config §15).
     *   - base_domain    VARCHAR(255) nullable — external platform's own origin (CORS/widget null).
     *   - allowed_origins JSON nullable — CORS allowlist (never `*` with credentials).
     *   - last_active_at DATETIME nullable — last successful authenticated call.
     *   - revoked_at     DATETIME nullable — hard integration revocation (also revokes all keys).
     *
     * Conventions (docs/database.md / platform-isolation.md):
     *   - ULID `public_id` via HasUlids on the model (ADR-002 matrix §6 — external-facing root).
     *   - InnoDB + utf8mb4/utf8mb4_unicode_ci; soft-deletes NOT used (integration rows are
     *     lifecycle-managed via status/revoked_at; audit retention concerns — no resurrection).
     */
    public function up(): void
    {
        Schema::create('platform_integrations', function (Blueprint $table) {
            $table->id();
            $table->char('public_id', 26)->unique();
            $table->foreignId('platform_id')->unique()->constrained('platforms')->cascadeOnDelete();
            $table->enum('status', ['pending', 'active', 'suspended', 'deactivated'])->default('pending');
            $table->string('paseto_version', 16)->default('v4.local');
            $table->unsignedInteger('token_ttl_seconds')->default(3600);
            $table->unsignedSmallInteger('rate_limit_per_minute')->default(60);
            $table->string('base_domain', 255)->nullable();
            $table->json('allowed_origins')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('platform_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_integrations');
    }
};
