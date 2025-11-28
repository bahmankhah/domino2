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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); // BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // user_id instead of wallet_id
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->string('type')->nullable(); // VARCHAR(255) DEFAULT NULL
            $table->string('description')->nullable(); // VARCHAR(255) DEFAULT NULL
            $table->unsignedBigInteger('credit')->default(0); // BIGINT(20) UNSIGNED DEFAULT NULL
            $table->unsignedBigInteger('debit')->default(0); // BIGINT(20) UNSIGNED DEFAULT NULL
            $table->bigInteger('remain')->default(0); // BIGINT(20) UNSIGNED DEFAULT 0
            $table->json('params')->nullable(); // JSON DEFAULT NULL
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
