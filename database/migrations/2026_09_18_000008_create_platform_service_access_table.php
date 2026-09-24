<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the `platform_service_access` table (T8).
     *
     * 1:1 with docs/phase-2a-schema-plan.md — `platform_service_access` (entitlement cache).
     * Maintained via subscription events (open decision: cache-over-compute); no soft delete.
     * Platform-scoped; UNIQUE (platform_id, service_id).
     */
    public function up(): void
    {
        Schema::create('platform_service_access', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('platform_id');
            $table->unsignedBigInteger('service_id');
            $table->boolean('has_access')->default(false);
            $table->timestamp('effective_until')->nullable();
            $table->timestamp('synced_at')->useCurrent();
            $table->timestamps();
            $table->engine('InnoDB');
        });

        Schema::table('platform_service_access', function (Blueprint $table) {
            $table->foreign('platform_id')->references('id')->on('platforms');
            $table->foreign('service_id')->references('id')->on('services');
            $table->unique(['platform_id', 'service_id'], 'platform_service_access_platform_service_unique');
            $table->index('platform_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_service_access');
    }
};
