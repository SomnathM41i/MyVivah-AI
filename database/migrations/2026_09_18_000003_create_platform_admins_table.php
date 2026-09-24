<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the `platform_admins` table.
     *
     * 1:1 with docs/phase-2a-schema-plan.md — T3 `platform_admins` (platform ↔ user role assignment).
     * Exactly one owner per platform is enforced in the service layer (MySQL 8.0.22 lacks partial
     * unique indexes via the migration API) — documented in database.md / phase-2a-schema-plan.md §T3.
     */
    public function up(): void
    {
        Schema::create('platform_admins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('platform_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('role', ['owner', 'admin', 'developer'])->default('admin');
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->foreign('platform_id')->references('id')->on('platforms');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(['platform_id', 'user_id'], 'platform_admins_platform_user_unique');
            $table->index('user_id');
            $table->index('platform_id');
            $table->engine('InnoDB');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_admins');
    }
};
