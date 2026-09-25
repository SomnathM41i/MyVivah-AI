<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_integrations', function (Blueprint $table): void {
            $table->string('user_search_endpoint', 2048)->nullable();
            $table->string('user_search_auth_type', 20)->nullable();
            $table->string('user_search_auth_header', 100)->nullable();
            $table->text('user_search_auth_secret')->nullable();
            $table->json('user_search_allowed_hosts')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('platform_integrations', function (Blueprint $table): void {
            $table->dropColumn([
                'user_search_endpoint', 'user_search_auth_type',
                'user_search_auth_header', 'user_search_auth_secret',
                'user_search_allowed_hosts',
            ]);
        });
    }
};
