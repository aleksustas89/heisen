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
        if (!Schema::hasTable('shop_item_properties')) {
            Schema::create('shop_item_properties', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('type');
                $table->tinyInteger('multiple')->default(0);
                $table->integer('shop_item_list_id')->default(0)->index();
                $table->integer('sorting')->default(0);
                $table->tinyInteger('show_in_item')->default(0);
                $table->tinyInteger('show_in_filter')->default(0);
                $table->tinyInteger('multiple')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_item_properties');
    }
};
