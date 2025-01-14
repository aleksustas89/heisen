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
        if (!Schema::hasTable('boxberry_orders')) {
            Schema::create('boxberry_orders', function (Blueprint $table) {
                $table->id();
                $table->integer("shop_order_id")->index();
                $table->string("url", 255);
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boxberry_orders');
    }
};
