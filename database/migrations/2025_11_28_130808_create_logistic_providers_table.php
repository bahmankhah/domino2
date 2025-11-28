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
        Schema::create('logistic_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('logistic_id')->constrained('logistics')->onDelete('cascade');
            $table->unsignedTinyInteger('ownership_percent')->default(100); // e.g., 25.50%
            $table->timestamps();
            
            // Unique constraint to prevent duplicate provider-logistic combinations
            $table->unique(['provider_id', 'logistic_id']);
            
            // Index for better performance
            $table->index(['provider_id', 'logistic_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistic_providers');
    }
};
