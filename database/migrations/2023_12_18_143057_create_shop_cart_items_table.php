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
        if (!Schema::hasTable('shop_cart_items')) {
            Schema::create('shop_cart_items', function (Blueprint $table) {
                $table->id();
                $table->integer("cart_id");
                $table->integer("shop_item_id");
                $table->integer("count")->default(1);
                $table->decimal("price", 12, 2)->default(0);
                $table->decimal("old_price", 12, 2)->default(0);
                $table->text("description")->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_cart_items');
    }
};
