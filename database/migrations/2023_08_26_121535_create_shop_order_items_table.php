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
        Schema::create('shop_order_items', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_item_id')->default(0);
            $table->integer('shop_order_id')->default(0);
            $table->integer('shop_currency_id')->default(0);
            $table->string('name')->default('');
            $table->decimal('quantity', 12, 2)->default('0.00');
            $table->decimal('price', 12, 2)->default('0.00');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_order_items');
    }
};
