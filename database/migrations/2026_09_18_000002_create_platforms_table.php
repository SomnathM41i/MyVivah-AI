<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the `platforms` table.
     *
     * 1:1 with docs/phase-2a-schema-plan.md — T2 `platforms` (Core Platform / isolation root).
     */
    public function up(): void
    {
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->char('public_id', 26)->unique()->comment('ULID platform key used in widget config + client URLs');
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->string('website_url', 2048)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'active', 'suspended', 'deactivated'])->default('pending');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users');
            $table->index('created_by');
            $table->index('status');
            $table->engine('InnoDB');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platforms');
    }
};
