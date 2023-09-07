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
        if (!Schema::hasTable('shop_item_images')) {
            Schema::create('shop_item_images', function (Blueprint $table) {
                $table->id();
                $table->integer('shop_item_id')->default(0)->index();
                $table->string('image_original')->nullable();
                $table->string('image_large')->nullable();
                $table->string('image_small')->nullable();
                $table->integer('sorting')->default(0);
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_item_images');
    }
};
