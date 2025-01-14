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
        if (!Schema::hasTable('shop_filters')) {
            Schema::create('shop_filters', function (Blueprint $table) {
                $table->id();
                $table->string("seo_title");
                $table->text("seo_description");
                $table->string("seo_keywords");
                $table->text("text");
                $table->integer("sorting");
                $table->string("url");
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_filters');
    }
};
