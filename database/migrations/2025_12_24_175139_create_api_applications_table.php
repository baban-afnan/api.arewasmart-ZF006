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
    Schema::create('api_applications', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('api_type'); 
        $table->string('website_link')->nullable();
        $table->text('business_description')->nullable();
        $table->string('business_nature')->nullable();
        $table->string('business_name')->nullable();
        $table->string('comment')->nullable();
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        
        // Explicitly define timestamps
        $table->timestamp('created_at')->useCurrent();
        $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_applications');
    }
};
