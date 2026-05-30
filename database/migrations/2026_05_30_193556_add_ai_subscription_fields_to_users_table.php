<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('ai_plan')->nullable()->after('role');
            $table->integer('ai_remaining_requests')->default(0)->after('ai_plan');
            $table->timestamp('ai_subscription_expires_at')->nullable()->after('ai_remaining_requests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ai_plan', 'ai_remaining_requests', 'ai_subscription_expires_at']);
        });
    }
};
