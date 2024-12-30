<?php

use App\Models\Order;
use App\Models\Product;
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
        Schema::create('order_lines', static function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Order::class)
                ->constrained('orders')
                ->onDelete('cascade');

            $table->foreignIdFor(Product::class)
                ->constrained('products')
                ->onDelete('cascade');

            $table->string('merchant_product_no');
            $table->string('gtin');
            $table->integer('quantity');

            $table->longText('description');

            $table->integer('ce_order_line_id');
            $table->string('channel_order_line_no');
            $table->boolean('is_fulfillment_by_marketplace');
            $table->integer('channel_product_no');
            $table->integer('stock_location_id');

            $table->longText('json')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_lines');
    }
};
