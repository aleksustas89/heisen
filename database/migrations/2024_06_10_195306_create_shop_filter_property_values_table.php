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
        if (!Schema::hasTable('shop_filter_property_values')) {
            Schema::create('shop_filter_property_values', function (Blueprint $table) {
                $table->id();
                $table->integer('property_id')->index();
                $table->integer('shop_group_id')->index();
                $table->integer('value')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_filter_property_values');
    }
};
