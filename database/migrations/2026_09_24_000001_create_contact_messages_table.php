<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the `contact_messages` table (Phase 5A — public site contact form).
     *
     * Public-contact submissions are stored (rather than only emailed) so they are
     * never lost while a support/mail inbox is not yet wired up, and so a future
     * admin panel can read them. Privacy-minimal by design (matches `api_audit_logs`):
     * only a one-way SHA-256 hash of the submitter IP is retained, never the IP itself.
     */
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('company', 255)->nullable();
            $table->string('email', 255);
            $table->string('phone', 32)->nullable();
            $table->text('message');
            $table->char('ip_hash', 64)->nullable()->comment('SHA-256 of submitter IP — never the raw IP');
            $table->string('source_url', 2048)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->engine('InnoDB');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
