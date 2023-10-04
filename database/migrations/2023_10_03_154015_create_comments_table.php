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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id');
            $table->string('subject')->nullable();
            $table->mediumText('text')->nullable();
            $table->string('author')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->tinyInteger('grade')->default(0)->nullable();
            $table->integer('client_id')->default(0)->nullable();
            $table->integer('user_id')->default(0)->nullable();
            $table->tinyInteger('active')->default(0);
             
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
