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
        Schema::create('income_price_rules', function (Blueprint $table) {
            $table->id();
            $table->string('type'); //'warehouse_provider', 'good_provider', 'referrer_provider', 'delivery', 'logistics_provider'
            $table->unsignedTinyInteger('percentage')->nullable();
        // New Column: If the 'type' entity doesn't exist in the order item, give the money to this type instead.
            $table->string('fallback_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_price_rules');
    }
};
