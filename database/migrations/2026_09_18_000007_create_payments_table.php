<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the `payments` table (T7).
     *
     * 1:1 with docs/phase-2a-schema-plan.md — `payments` (Payment & Billing, manual/ADR-011 MVP).
     * Financial records retained — no soft delete. Platform-scoped.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('platform_id');
            $table->string('gateway', 50)->nullable()->comment('e.g. `manual`; no online gateway at MVP (ADR-011)');
            $table->string('gateway_transaction_id', 255)->nullable()->unique();
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('INR');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->engine('InnoDB');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
            $table->foreign('platform_id')->references('id')->on('platforms');
            $table->index(['subscription_id'], 'payments_subscription_index');
            $table->index(['platform_id', 'status'], 'payments_platform_status_index');
            $table->index(['platform_id', 'paid_at'], 'payments_platform_paid_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
