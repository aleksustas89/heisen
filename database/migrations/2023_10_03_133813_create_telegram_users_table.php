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
        if (!Schema::hasTable('telegram_users')) {
            Schema::create('telegram_users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('telegram_chat_id')->index();
                $table->integer('user_id')->index();
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_users');
    }
};
