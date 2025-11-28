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
        Schema::create('order_item_incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_rule_id')->nullable()->constrained('income_price_rules')->onDelete('set null');
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->unsignedInteger('credit');
            $table->unsignedInteger('debit');
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('received_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_incomes');
    }
};
