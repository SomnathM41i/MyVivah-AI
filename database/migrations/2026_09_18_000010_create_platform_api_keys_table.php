<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 3B — T10 `platform_api_keys`: PASETO v4.local key lifecycle (generation · rotation ·
     * revocation · rotation grace). 1:1 with phase-3a plan §5.2 / §6.2 — every integration has
     * exactly ONE `is_primary` symmetric key at a time (UNIQUE(platform_integration_id, is_primary)
     * is a partial-guarantee via filtering index below; enforcement also lives in the key service
     * — ADR for key rotation §6.3).
     *
     * Security contract (security.md):
     *   - Random 32-byte v4.local key, stored **encrypted at rest** (`key_encrypted` — openssl
     *     AES-256-CBC keyed from APP_KEY; NEVER plaintext; key material never returned after
     *     creation except one-time on issue via `KmsRotateResponse`). TTL 1h max (plan §6.2).
     *   - Rotation: `is_primary=0` grace copy carries `backup_of_id`, `rotated_at`; old primary
     *     stays valid ≤ grace window (default 24h, config `paseto.rotation_grace_seconds`), then
     *     hard-revoke.
     *   - Revocation: `revoked_at` set → middleware + service reject immediately (`ValidateApiToken`).
     *   - `key_fingerprint` = sha256(key) hex → identifies key in token `kid`
     *     (PASETO footer) WITHOUT exposing key material; lookup + rotation use it.
     *   - `status` string `active|rotated|revoked` default `active` — explicit lifecycle state
     *     (phase-3a §6.3): `active` = usable primary; `rotated` = demoted key inside its rotation
     *     grace window (still valid for presented tokens); `revoked` = hard-revoked (never usable).
     *   - `is_primary` boolean — semantic flag (primary vs backup/rotated).
     *   - `primary_token` CHAR(7) nullable — the DB-level "one primary" guarantee:
     *     the primary key row carries `PRIMARY` here, backups carry NULL. MySQL/SQLite
     *     permit unbounded NULLs in a unique index, so UNIQUE(platform_integration_id,
     *     primary_token) enforces "only one primary per integration" while allowing the
     *     rotation grace chain (many backup rows) — impossible with a plain composite
     *     unique on (platform_integration_id, is_primary) which would cap backups at 1.
     *
     * Instrumented columns (from phase-3a plan §5.3 config surface; CA/LARAVEL floats none):
     */
    public function up(): void
    {
        Schema::create('platform_api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_integration_id')->constrained('platform_integrations')->cascadeOnDelete();
            $table->string('key_fingerprint', 40)->unique();  // sha256 hex of raw key
            $table->text('key_encrypted');                     // openssl AES-256-CBC ciphertext (never raw)
            $table->boolean('is_primary')->default(true);
            $table->char('primary_token', 7)->nullable();
            $table->string('status', 16)->default('active');
            $table->unsignedBigInteger('backup_of_id')->nullable();
            $table->unsignedInteger('token_ttl_seconds')->default(3600);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('rotated_at')->nullable();
            $table->timestamp('revoked_at')->nullable();

            // Only one primary per integration at any time (NULLs exempt → unlimited backups).
            $table->unique(['platform_integration_id', 'primary_token'], 'platform_api_keys_one_primary');
            $table->index(['platform_integration_id', 'is_primary']);
            $table->index(['platform_integration_id', 'status']);
            // Backup-of self-FK (grace chain). Soft deletes NOT used (financial/crypto key rows —
            // AGENTS.md matrix: keys are hard-lifecycle records).
            $table->foreign('backup_of_id')->references('id')->on('platform_api_keys')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_api_keys');
    }
};
