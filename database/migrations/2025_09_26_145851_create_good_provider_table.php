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
        Schema::create('good_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('good_id')->constrained('goods')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('ownership_percent')->default(100); 
            $table->timestamps();
            
            
            // Unique constraint to prevent duplicate provider-good combinations
            $table->unique(['good_id', 'provider_id']);
            
            // Index for better performance
            $table->index(['good_id', 'provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('good_provider');
    }
};
