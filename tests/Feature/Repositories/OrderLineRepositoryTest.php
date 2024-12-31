<?php

use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use App\Repositories\OrderLineRepository;
use Illuminate\Contracts\Container\BindingResolutionException;

beforeEach(
/**
 * @throws BindingResolutionException
 */
    function () {
        $this->orderLineRepository = OrderLineRepository::make();

        $this->product = Product::safeInstance(Product::factory()->create());
        $this->order = Order::safeInstance(Order::factory()->create());

        $orderLines = [
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'merchant_product_no' => $this->product->merchant_product_no,
            'gtin' => $this->product->ean,
            'quantity'  => 10,
        ];
        $this->orderLine = OrderLine::safeInstance(OrderLine::query()->create($orderLines));
    }
);

it('should create or update order line if changed', function () {
    $attributes = [
        'order_id' => $this->order->id,
        'product_id' => $this->product->id,
        'merchant_product_no' => $this->product->merchant_product_no,
    ];
    $values = ['quantity' => 10, 'description' => 'Order Line 1'];

    $orderLine = $this->orderLineRepository->createOrUpdateIfChanged($attributes, $values);

    expect($orderLine->order_id)->toBe($this->order->id)
        ->and($orderLine->product_id)->toBe($this->product->id)
        ->and($orderLine->quantity)->toBe(10)
        ->and($orderLine->description)->toBe('Order Line 1');

    $values = ['quantity' => 20, 'description' => 'Order Line 1 Updated'];

    $orderLine = $this->orderLineRepository->createOrUpdateIfChanged($attributes, $values);

    expect($orderLine->order_id)->toBe($this->order->id)
        ->and($orderLine->product_id)->toBe($this->product->id)
        ->and($orderLine->quantity)->toBe(20)
        ->and($orderLine->description)->toBe('Order Line 1 Updated');
});
