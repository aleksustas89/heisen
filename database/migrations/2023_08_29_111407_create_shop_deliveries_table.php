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
        Schema::create('shop_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->integer('sorting')->nullable()->default(0);
            $table->text('description')->nullable();
            $table->string("color", 50)->nullable()->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_deliveries');
    }
};
