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
        Schema::create('cdek_orders', function (Blueprint $table) {
            $table->id();
            $table->integer("shop_order_id")->index();
            $table->string("uuid", 100);
            $table->string("receipt_uuid", 100);
            $table->string("url", 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cdek_orders');
    }
};
