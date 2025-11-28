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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null');
            $table->json('warehouse_info')->nullable();

            $table->foreignId('logistic_id')->nullable()->constrained('logistics')->onDelete('set null');
            $table->json('logistic_info')->nullable();

            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

            $table->foreignId('good_id')->nullable()->constrained('goods')->onDelete('set null');
            $table->json('good_info')->nullable();
            
            $table->unsignedBigInteger('damage')->default(0);
            $table->unsignedBigInteger('price')->nullable();
            
            $table->foreignId('order_type_id')->nullable()->constrained('order_types')->onDelete('set null');
            $table->json('order_type_info')->nullable();
            

            $table->foreignId('referrer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('referrer_info')->nullable();

            $table->datetime('started_at');
            $table->datetime('ended_at');
            
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_items');
    }
};
