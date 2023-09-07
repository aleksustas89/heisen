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
        if (!Schema::hasTable('shop_item_property_for_groups')) {
            Schema::create('shop_item_property_for_groups', function (Blueprint $table) {
                $table->id();
                $table->integer('shop_group_id')->index();
                $table->integer('shop_item_property_id')->index();
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_item_property_for_groups');
    }
};
