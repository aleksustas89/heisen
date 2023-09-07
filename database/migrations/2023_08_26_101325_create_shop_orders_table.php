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
        Schema::create('shop_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_delivery_id')->default(0)->nullable();
            $table->integer('client_id')->default(0)->nullable();
            $table->integer('source_id')->default(0)->nullable();
            $table->integer('shop_payment_system_id')->default(0)->nullable();
            $table->integer('shop_currency_id');
            $table->dateTime('status_datetime')->nullable()->default(null);
            
            $table->string('name')->default('');
            $table->string('surname')->default('');
            $table->string('patronymic')->default('');
            $table->string('email')->default('');
            $table->string('phone')->default('');

            $table->string('city')->default('');
            $table->string('postcode', 50)->default('');

            $table->string('address')->default('');
            $table->string('house', 15)->default('');
            $table->string('flat', 15)->default('');

            $table->string('office')->default('');
            $table->tinyInteger('courier')->nullable()->default(0);

            $table->text('description')->nullable();
            $table->text('delivery_information')->nullable();

            $table->tinyInteger('canceled')->default(0);
            $table->string('guid', 100)->default('');


            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_orders');
    }
};
