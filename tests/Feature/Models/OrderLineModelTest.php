<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderLine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Random\RandomException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->product = Product::safeInstance(Product::factory()->create());
    $this->order = Order::safeInstance(Order::factory()->create());
    $this->orderLine = OrderLine::query()->create(['product_id' => $this->product->id, 'order_id' => $this->order->id, 'quantity' => 10, 'merchant_product_no' => $this->product->merchant_product_no]);
});

it('belongs to an order', function () {
    $orderLine = OrderLine::query()->create(['product_id' => $this->product->id, 'order_id' => $this->order->id, 'quantity' => 10, 'merchant_product_no' => $this->product->merchant_product_no]);
    $this->assertTrue($orderLine->order->is($this->order));
});

it('belongs to a product', function () {
    $orderLine = OrderLine::query()->create(['product_id' => $this->product->id, 'order_id' => $this->order->id, 'quantity' => 10, 'merchant_product_no' => $this->product->merchant_product_no]);

    $this->assertTrue($orderLine->product->is($this->product));
});

it( 'can create an order line',
    /**
     * @throws RandomException
     */
    function () {
        $orderLine = OrderLine::query()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'merchant_product_no' => $this->product->merchant_product_no,
            'gtin' => 'GTIN-12345',
            'quantity' => 10,
            'description' => 'Test order line',
            'ce_order_line_id' => random_int(10000, 99999),
            'channel_order_line_no' => 'CO-12345',
            'is_fulfillment_by_marketplace' => true,
            'channel_product_no' => 12345,
            'stock_location_id' => 1,
        ]);

        $this->assertDatabaseHas('order_lines', [ 'id' => $orderLine->id ]);
    }
);
