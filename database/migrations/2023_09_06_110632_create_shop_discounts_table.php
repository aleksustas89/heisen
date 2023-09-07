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
        Schema::create('shop_discounts', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->text("description")->nullable();
            $table->dateTime("start_datetime")->useCurrent();
            $table->dateTime("end_datetime")->useCurrent();
            $table->tinyInteger('active')->default(1);
            $table->decimal('value', 12, 2)->default("0.00");
            $table->tinyInteger('type')->default(0);
             
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_discounts');
    }
};
