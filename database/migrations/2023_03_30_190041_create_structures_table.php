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
        if (!Schema::hasTable('structures')) {
            Schema::create('structures', function (Blueprint $table) {
                $table->id();
                $table->integer('parent_id')->default(0);
                $table->integer('structure_menu_id')->default(0)->nullable();
                $table->string('name')->nullable();
                $table->text('text')->nullable();
                $table->string('seo_title')->nullable();
                $table->string('seo_description')->nullable();
                $table->string('seo_keywords')->nullable();
                $table->string('path');
                $table->integer('active')->default(1);
                $table->integer('indexing')->default(1);
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
        Schema::dropIfExists('structures');
    }
};
