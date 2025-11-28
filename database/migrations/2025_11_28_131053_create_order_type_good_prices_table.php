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
        Schema::create('order_type_good_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_type_id')->constrained('order_types')->onDelete('cascade');
            $table->foreignId('good_id')->constrained('goods')->onDelete('cascade');
            $table->unsignedBigInteger('price')->nullable();
            $table->timestamps();

            $table->unique(['order_type_id', 'good_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_type_good_price_rules');
    }
};
