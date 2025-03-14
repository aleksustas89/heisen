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
        if (!Schema::hasTable('boxberry_senders')) {
            Schema::create('boxberry_senders', function (Blueprint $table) {
                $table->id();
                $table->string("boxberry_office_id", 10);
                $table->string("name")->nullable();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boxberry_senders');
    }
};
