<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 1:1 with docs/phase-2a-schema-plan.md — T1 `users` (Core Platform additions only;
     * framework scaffold columns remain untouched).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->char('public_id', 26)->after('id')->unique()->comment('ULID. Public account ID');
            $table->string('phone', 32)->nullable()->unique()->after('email');
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
            $table->enum('status', ['active', 'suspended', 'deactivated'])->default('active')->after('email_verified_at');
            $table->string('timezone', 64)->default('UTC')->after('status');
            $table->string('locale', 16)->default('en')->after('timezone');
            $table->timestamp('last_login_at')->nullable()->after('locale');
        });

        if (! Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropUnique(['public_id']);
            $table->dropColumn(['last_login_at', 'locale', 'timezone', 'status', 'phone_verified_at', 'phone', 'public_id']);
        });
    }
};
