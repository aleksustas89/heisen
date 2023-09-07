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
        if (!Schema::hasTable('shops')) {
            Schema::create('shops', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('email')->nullable();
                $table->tinyInteger('active');
                $table->string('path');
                $table->integer('items_on_page')->nullable();
                $table->integer('currency_id')->nullable()->default(0);
                $table->string('seo_title')->nullable();
                $table->string('seo_description')->nullable();
                $table->string('seo_keywords')->nullable();
                $table->string('seo_group_title_template')->nullable();
                $table->string('seo_group_description_template')->nullable();
                $table->string('seo_group_keywords_template')->nullable();
                $table->string('seo_item_title_template')->nullable();
                $table->string('seo_item_description_template')->nullable();
                $table->string('seo_item_keywords_template')->nullable();
                $table->integer('image_small_max_width')->default(0);
                $table->integer('image_small_max_height')->default(0);
                $table->integer('image_large_max_width')->default(0);
                $table->integer('image_large_max_height')->default(0);
                $table->integer('group_image_small_max_width')->default(0);
                $table->integer('group_image_small_max_height')->default(0);
                $table->integer('group_image_large_max_width')->default(0);
                $table->integer('group_image_large_max_height')->default(0);
                $table->tinyInteger('preserve_aspect_ratio')->default(1)->nullable();
                $table->tinyInteger('preserve_aspect_ratio_small')->default(1)->nullable();
                $table->tinyInteger('preserve_aspect_ratio_group')->default(1)->nullable();
                $table->tinyInteger('preserve_aspect_ratio_group_small')->default(1)->nullable();
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
