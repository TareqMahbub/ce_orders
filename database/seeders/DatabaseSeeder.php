<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Random\RandomException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * @throws RandomException
     */
    public function run(): void
    {
        if (!Order::query()->exists()) {
            $orders = Order::factory()->count(50)->create();
        } else {
            $orders = Order::all();
        }

        if (!Product::query()->exists()) {
            $products = Product::factory()->count(20)->create();
        } else {
            $products = Product::all();
        }

        if (!OrderLine::query()->exists()) {
            $products->each(
                function ($product) use ($orders) {
                    $orders
                        ->random(5)
                        ->each(
                            function ($order) use($product) {
                                $order = Order::safeInstance($order);
                                $product = Product::safeInstance($product);

                                OrderLine::query()->create([
                                    'order_id' => $order->id,
                                    'product_id' => $product->id,
                                    'merchant_product_no' => $product->merchant_product_no,
                                    'gtin' => $product->ean,
                                    'description' => $product->description,
                                    'quantity' => random_int(3, 12),
                                    'ce_order_line_id' => random_int(1000, 9999),
                                    'channel_order_line_no' => 'CHN' . random_int(1000, 9999),
                                    'is_fulfillment_by_marketplace' => (bool)random_int(0, 1),
                                    'channel_product_no' => random_int(1000, 9999),
                                    'stock_location_id' => random_int(1000, 9999),
                                ]);
                            }
                        );
                }
            );
        }
    }
}
