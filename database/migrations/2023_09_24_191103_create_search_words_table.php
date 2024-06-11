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
        if (!Schema::hasTable('search_words')) {
            Schema::create('search_words', function (Blueprint $table) {
                $table->id();
                $table->integer('hash')->index('hash')->default(0);
                $table->integer('search_page_id')->index('search_page_id')->default(0);
                $table->float('weight')->default(0); 
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_words');
    }
};
