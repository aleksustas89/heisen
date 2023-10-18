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
        if (!Schema::hasTable('shop_items')) {
            Schema::create('shop_items', function (Blueprint $table) {
                $table->id();
                $table->integer('shop_group_id')->default(0)->index();
                $table->integer('modification_id')->default(0)->nullable();
                $table->tinyInteger('default_modification')->default(0);
                $table->integer('user_id')->default(0)->index();
                $table->string('name');
                $table->mediumText('description')->nullable();
                $table->mediumText('text')->nullable();
                $table->mediumText('seo_title')->nullable();
                $table->mediumText('seo_description')->nullable();
                $table->mediumText('seo_keywords')->nullable();
                $table->decimal('price', 12, 2)->default(0.00);
                $table->tinyInteger('discounts')->default(0);
                $table->integer('shop_currency_id')->default(0);
                $table->integer('sorting')->default(0);
                $table->string('marking', 50)->nullable();
                $table->integer('indexing')->default(1);
                $table->integer('active')->default(1);
                $table->string('path')->index();

                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_items');
    }
};
