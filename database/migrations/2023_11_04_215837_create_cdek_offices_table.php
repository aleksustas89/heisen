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
        Schema::create('cdek_offices', function (Blueprint $table) {
            $table->id();
            $table->string('code', 16);
            $table->string('name');
            $table->string('uuid', 100);
            $table->string('work_time');
            $table->integer('cdek_region_id')->index();
            $table->integer('cdek_city_id')->index();
            $table->float('longitude')->default(0);
            $table->float('latitude')->default(0);
            $table->float('weight_min')->default(0);
            $table->float('weight_max')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cdek_offices');
    }
};
