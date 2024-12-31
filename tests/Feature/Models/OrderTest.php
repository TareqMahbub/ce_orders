<?php

use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->product = Product::safeInstance(Product::factory()->create());
    $this->order = Order::safeInstance(Order::factory()->create());
});

it('have many order lines', function () {
    $orderLine1 = OrderLine::query()->create(['product_id' => $this->product->id, 'order_id' => $this->order->id, 'quantity' => 10, 'merchant_product_no' => $this->product->merchant_product_no]);
    $orderLine2 = OrderLine::query()->create(['product_id' => $this->product->id, 'order_id' => $this->order->id, 'quantity' => 10, 'merchant_product_no' => $this->product->merchant_product_no]);

    $this->assertTrue($this->order->orderLines->contains($orderLine1));
    $this->assertTrue($this->order->orderLines->contains($orderLine2));
    $this->assertEquals(2, $this->order->orderLines->count());
});

it('can create an order', function () {
    $order = Order::safeInstance(Order::factory()->create([
        'channel_id' => 1,
        'global_channel_id' => 1,
        'channel_order_no' => 'CO-12345',
        'status' => 'pending',
        'is_business_order' => true,
        'billing_address' => '123 Test St',
        'shipping_address' => '123 Test St',
        'phone' => '1234567890',
        'email' => 'test@example.com',
        'currency_code' => 'USD',
        'order_date' => now()
    ]));

    $this->assertDatabaseHas('orders', [
        'id' => $order->id
    ]);
});
