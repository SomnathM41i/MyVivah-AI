<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 3B — `api_audit_logs`: append-only security/integration audit trail
     * (phase-3a-api-integration-plan.md §5.2/§13; security.md).
     *
     * Contract:
     *   - Append-only: NO update, NO soft delete (audit retention = immutable).
     *   - Platform-scoped WHERE applicable: `platform_id`/`api_key_id` are nullable
     *     because pre-auth failures (malformed token, unknown client) have no
     *     resolved platform — those rows still get `event` + request metadata.
     *   - Privacy: raw bodies/PII/secrets NEVER stored — only `ip_hash`,
     *     `request_checksum`/`response_checksum` (sha256) + minimal `metadata`.
     *   - Every event constant lives on App\Models\ApiAuditLog.
     */
    public function up(): void
    {
        Schema::create('api_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->nullable()->constrained('platforms')->nullOnDelete();
            $table->unsignedBigInteger('api_key_id')->nullable();
            $table->string('event', 64);
            $table->char('ip_hash', 64)->nullable();          // sha256 of client IP
            $table->string('endpoint', 255)->nullable();
            $table->string('method', 10)->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->char('request_checksum', 64)->nullable(); // sha256 of redacted request
            $table->char('response_checksum', 64)->nullable(); // sha256 of response (no PII content)
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('api_key_id')->references('id')->on('platform_api_keys')->nullOnDelete();

            $table->index(['platform_id', 'created_at']);
            $table->index('event');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_audit_logs');
    }
};
