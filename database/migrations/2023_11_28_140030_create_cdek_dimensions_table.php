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
        if (!Schema::hasTable('cdek_dimensions')) {
            Schema::create('cdek_dimensions', function (Blueprint $table) {
                $table->id();
                $table->decimal("weight", 12, 2);
                $table->decimal("length", 12, 2);
                $table->decimal("width", 12, 2);
                $table->decimal("height", 12, 2);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cdek_dimensions');
    }
};
