<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the `plans` table (Phase 2A T5).
     *
     * Service-specific, dynamic plan catalog (ADR-012). Internal: no payments gateway
     * config at MVP (ADR-011). Plans are seeded in a later step as demo data.
     * Uses ULID CHAR(26) public_id + CHAR(26) service public_id FK for client contract.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->char('public_id', 26)->unique()->comment('ULID. Public plan key for plans config');
            $table->unsignedBigInteger('service_id');
            $table->char('service_public_id', 33)->nullable()->comment('Reserved — service public_id contains service FK at client contract');
            $table->string('plan_key', 50);
            $table->string('name', 255);
            $table->string('description', 500)->nullable();
            $table->decimal('price', 12, 2)->default(0.00);
            $table->char('currency', 3)->default('INR');
            $table->enum('billing_period', ['monthly', 'yearly', 'custom'])->default('monthly');
            $table->integer('billing_interval')->nullable();
            $table->boolean('is_active')->default(false);
            $table->json('features')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['service_id', 'plan_key'], 'plans_service_key_unique');
            $table->index(['service_id', 'is_active']);
            $table->index('is_active');
            $table->engine('InnoDB');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
