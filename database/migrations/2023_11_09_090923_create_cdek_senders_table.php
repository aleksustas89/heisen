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
        Schema::create('cdek_senders', function (Blueprint $table) {
            $table->id();
            $table->integer("cdek_region_id");
            $table->integer("cdek_city_id");
            $table->tinyInteger("type")->default(0);
            $table->integer("cdek_office_id")->default(0);
            $table->string("address", 255);
            $table->string("name", 255);
            $table->string("tariff_code", 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cdek_senders');
    }
};
