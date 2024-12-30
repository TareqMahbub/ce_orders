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
        Schema::create('products', static function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active');
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('brand')->nullable();
            $table->string('merchant_product_no')->nullable();
            $table->string('manufacturer_product_number')->nullable();
            $table->string('ean')->nullable();

            $table->longText('json')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
