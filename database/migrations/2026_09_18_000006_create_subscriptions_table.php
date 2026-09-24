<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the `subscriptions` table (T6).
     *
     * 1:1 with docs/phase-2a-schema-plan.md — `subscriptions` (Service & Subscription lifecycle).
     * Status-driven, no soft delete; platform-scoped. Single-owner/`active`-per-pair enforced in
     * service layer (ADR-012 open decision).
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->char('public_id', 26)->unique()->comment('ULID. Public subscription ID (public_id §6)');
            $table->unsignedBigInteger('platform_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('plan_id');
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled', 'suspended'])->default('pending');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('next_billing_at')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->timestamps();
            $table->engine('InnoDB');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreign('platform_id')->references('id')->on('platforms');
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('plan_id')->references('id')->on('plans');
            $table->index(['platform_id', 'status'], 'subscriptions_platform_status_index');
            $table->index(['platform_id', 'service_id'], 'subscriptions_platform_service_index');
            $table->index(['platform_id', 'service_id', 'status'], 'subscriptions_scope_status_index');
            $table->index('ends_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
