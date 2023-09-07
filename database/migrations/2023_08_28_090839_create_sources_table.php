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
        if (!Schema::hasTable('sources')) {
            Schema::create('sources', function (Blueprint $table) {
                $table->id();
                $table->string('service', 100)->nullable()->default('');
                $table->string('campaign', 100)->nullable()->default('');
                $table->string('ad', 100)->nullable()->default('');
                $table->string('source', 100)->nullable()->default('');
                $table->string('medium', 100)->nullable()->default('');
                $table->string('content', 20)->nullable()->default('');
                $table->string('term', 100)->nullable()->default('');
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('source');
    }
};
