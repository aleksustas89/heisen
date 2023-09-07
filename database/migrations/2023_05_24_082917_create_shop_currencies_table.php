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
        if (!Schema::hasTable('shop_currencies')) {
            Schema::create('shop_currencies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('shop_id')->nullable()->default(0);
                $table->char('code', 3);
                $table->decimal('exchange_rate', 16, 6)->default(0.000000); 
                $table->tinyInteger('default')->default(0);
                $table->smallInteger('sorting')->default(0);
    
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_currencies');
    }
};
