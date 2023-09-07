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

        if (!Schema::hasTable('shop_item_list_items')) {
            Schema::create('shop_item_list_items', function (Blueprint $table) {
                $table->id();
                $table->integer('shop_item_list_id')->index();
                $table->string('value');
                $table->smallInteger('sorting')->default(0);
                $table->text('description')->nullable()->default('NULL');
                $table->string('color')->nullable();
                $table->integer('active')->default(1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_item_list_items');
    }
};
