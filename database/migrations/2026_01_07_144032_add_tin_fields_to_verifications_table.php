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
        Schema::table('verifications', function (Blueprint $table) {
            $table->string('tax_id')->nullable()->after('idno');
            $table->string('tax_residency')->nullable()->after('tax_id');
            $table->decimal('amount', 10, 2)->default(0)->after('tax_residency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verifications', function (Blueprint $table) {
             $table->dropColumn(['tax_id', 'tax_residency', 'amount']);
        });
    }
};
