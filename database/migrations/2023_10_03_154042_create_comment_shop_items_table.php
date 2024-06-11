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
        if (!Schema::hasTable('comment_shop_items')) {
            Schema::create('comment_shop_items', function (Blueprint $table) {
                $table->id();
                $table->integer('comment_id')->index();
                $table->integer('shop_item_id')->index();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_shop_items');
    }
};
