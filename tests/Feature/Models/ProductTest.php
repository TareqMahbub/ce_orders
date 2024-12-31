<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderLine;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->product = Product::safeInstance(Product::factory()->create());
    $this->order = Order::safeInstance(Order::factory()->create());
});

it('can have many order lines', function () {
    $orderLine1 = OrderLine::query()->create(['product_id' => $this->product->id, 'order_id' => $this->order->id, 'quantity' => 10, 'merchant_product_no' => $this->product->merchant_product_no]);
    $orderLine2 = OrderLine::query()->create(['product_id' => $this->product->id, 'order_id' => $this->order->id, 'quantity' => 10, 'merchant_product_no' => $this->product->merchant_product_no]);

    $this->assertTrue($this->product->orderLines->contains($orderLine1));
    $this->assertTrue($this->product->orderLines->contains($orderLine2));
    $this->assertEquals(2, $this->product->orderLines->count());
});

it('can create a product', function () {
    $product = Product::query()->create([
        'is_active' => true,
        'name' => 'Test Product',
        'description' => 'This is a test product',
        'brand' => 'Test Brand',
        'merchant_product_no' => 'MPN-12345',
        'manufacturer_product_number' => 'MPN-54321',
        'ean' => '1234567890123',
    ]);

    $this->assertDatabaseHas('products', [
        'id' => $product->id
    ]);
});
