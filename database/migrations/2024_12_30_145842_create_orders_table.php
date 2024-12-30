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
        Schema::create('orders', static function (Blueprint $table) {
            $table->id();

            $table->integer('channel_id');
            $table->integer('global_channel_id');
            $table->string('channel_order_no')->unique();
            $table->string('status');
            $table->boolean('is_business_order')->default(false);

            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('phone')->nullable();
            $table->text('email')->nullable();
            $table->string('currency_code')->nullable();
            $table->string('order_date')->nullable();

            $table->longText('json')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
