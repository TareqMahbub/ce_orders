<?php

use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use App\Repositories\OrderRepository;
use Illuminate\Contracts\Container\BindingResolutionException;

beforeEach(
/**
 * @throws BindingResolutionException
 */
    function () {
        $this->orderRepository = OrderRepository::make();

        $this->product = Product::safeInstance(Product::factory()->create());
        $this->order = Order::safeInstance(Order::factory()->create());

        $orderLines = [
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'merchant_product_no' => $this->product->merchant_product_no,
            'stock_location_id' => 1,
            'gtin' => $this->product->ean,
            'quantity' => 10,
        ];
        $this->orderLine = OrderLine::safeInstance(OrderLine::factory()->state($orderLines)->create());

        $this->product1 = Product::safeInstance(Product::factory()->create());
        $this->order1 = Order::safeInstance(Order::factory()->create());

        $orderLines1 = [
            'order_id' => $this->order1->id,
            'product_id' => $this->product1->id,
            'merchant_product_no' => $this->product1->merchant_product_no,
            'stock_location_id' => 1,
            'gtin' => $this->product1->ean,
            'quantity' => 5,
        ];
        $this->orderLine1 = OrderLine::safeInstance(OrderLine::factory()->state($orderLines1)->create());
    }
);

it('should create or update order if changed', function () {
    $attributes = ['channel_id' => '123', 'global_channel_id' => '123','channel_order_no' => '12345'];
    $values = ['status' => 'IN_PROGRESS', 'is_business_order' => true];

    $order = $this->orderRepository->createOrUpdateIfChanged($attributes, $values);

    expect($order->channel_order_no)->toBe('12345')
        ->and($order->status)->toBe('IN_PROGRESS')
        ->and($order->is_business_order)->toBeTrue();

    $values = ['status' => 'SHIPPED'];

    $order = $this->orderRepository->createOrUpdateIfChanged($attributes, $values);

    expect($order->channel_order_no)->toBe('12345')
        ->and($order->status)->toBe('SHIPPED');
});

it('gets top sold products with 2 lines that belongs to 2 groups', function () {
    $topSoldProducts = $this->orderRepository->getTopSoldProducts(2);

    expect($topSoldProducts)->toHaveCount(2)
        ->and($topSoldProducts->first()->merchant_product_no)->toBe($this->orderLine->merchant_product_no)
        ->and($topSoldProducts->first()->total_quantity + 0)->toBe($this->orderLine->quantity)
        ->and($topSoldProducts->last()->merchant_product_no)->toBe($this->orderLine1->merchant_product_no)
        ->and($topSoldProducts->last()->total_quantity + 0)->toBe($this->orderLine1->quantity);
});

it('gets top sold products with 3 lines that belongs to 2 groups', function () {
    $this->order2 = Order::safeInstance(Order::factory()->create());

    $orderLines2 = [
        'order_id' => $this->order2->id,
        'product_id' => $this->product1->id,
        'merchant_product_no' => $this->product1->merchant_product_no,
        'stock_location_id' => 1,
        'gtin' => $this->product1->ean,
        'quantity' => 10,
    ];
    $this->orderLine2 = OrderLine::safeInstance(OrderLine::factory()->state($orderLines2)->create());

    $topSoldProducts = $this->orderRepository->getTopSoldProducts(2);

    expect($topSoldProducts)->toHaveCount(2)
        ->and($topSoldProducts->first()->merchant_product_no)->toBe($this->orderLine1->merchant_product_no)
        ->and($topSoldProducts->first()->total_quantity + 0)->toBe($this->orderLine1->quantity + 10)
        ->and($topSoldProducts->last()->merchant_product_no)->toBe($this->orderLine->merchant_product_no)
        ->and($topSoldProducts->last()->total_quantity + 0)->toBe($this->orderLine->quantity);
});
