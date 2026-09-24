<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the `services` table (MyVivahAI product catalog).
     *
     * 1:1 with docs/phase-2a-schema-plan.md — T4 `services`. Seeded in a later approved step
     * (Phase 2B seeders) with `realtime_chat` (ADI/docs seed contract) — no seed runs here.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique()->comment('e.g. realtime_chat');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->engine('InnoDB');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
