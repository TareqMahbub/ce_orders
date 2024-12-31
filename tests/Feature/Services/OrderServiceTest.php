<?php

use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use App\Repositories\OrderRepository;
use App\Services\CeService;
use App\Services\OrderLineService;
use App\Services\OrderService;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->orderRepository = Mockery::mock(OrderRepository::class);
    $this->ceService = Mockery::mock(CeService::class);
    $this->orderLineService = Mockery::mock(OrderLineService::class);
    $this->productService = Mockery::mock(ProductService::class);
    $this->orderService = new OrderService(
        $this->orderRepository,
        $this->ceService,
        $this->orderLineService,
        $this->productService
    );
});

it( 'syncs orders from CE',
    /**
     * @throws Exception
     */
    function () {
        $orders = [
            [
                'ChannelId' => 1,
                'GlobalChannelId' => 1,
                'ChannelOrderNo' => '12345',
                'Status' => 'NEW',
                'IsBusinessOrder' => false,
                'BillingAddress' => [],
                'ShippingAddress' => [],
                'Phone' => '1234567890',
                'Email' => 'test@example.com',
                'CurrencyCode' => 'USD',
                'OrderDate' => '2023-01-01',
                'Lines' => [
                    [
                        'MerchantProductNo' => 'MPN-12345',
                        'Gtin' => '1234567890123',
                        'Quantity' => 10,
                        'Description' => 'Order Line 1',
                        'Id' => 1,
                        'ChannelOrderLineNo' => 'COLN-12345',
                        'IsFulfillmentByMarketplace' => true,
                        'ChannelProductNo' => 'CPN-12345',
                        'StockLocation' => ['Id' => 1],
                    ]
                ]
            ]
        ];

        $this->ceService
            ->shouldReceive('fetchPagedOrders')
            ->with(1)
            ->andReturn($orders);

        $this->ceService
            ->shouldReceive('fetchPagedOrders')
            ->with(2)
            ->andReturn([]);

        $this->orderRepository
            ->shouldReceive('createOrUpdateIfChanged')
            ->andReturnUsing(function ($attributes, $values) {
                return Order::factory()->create($attributes + $values);
            });

        $this->productService
            ->shouldReceive('getOrSyncProduct')
            ->with('MPN-12345')
            ->andReturn(Product::factory()->create(['merchant_product_no' => 'MPN-12345']));

        $this->orderLineService
            ->shouldReceive('syncOrderLine')
            ->andReturnUsing(function ($orderId, $productId, $line) {
                return OrderLine::factory()->create([
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'quantity' => $line['Quantity'],
                    'description' => $line['Description'],
                    'ce_order_line_id' => $line['Id'],
                    'channel_order_line_no' => $line['ChannelOrderLineNo'],
                    'is_fulfillment_by_marketplace' => $line['IsFulfillmentByMarketplace'],
                    'channel_product_no' => $line['ChannelProductNo'],
                    'stock_location_id' => $line['StockLocation']['Id'],
                ]);
            });

        $this->orderService->syncOrders();

        $this->assertDatabaseHas('orders', ['channel_order_no' => '12345']);
        $this->assertDatabaseHas('order_lines', ['channel_order_line_no' => 'COLN-12345']);
    }
);

it( 'syncs order',
    /**
     * @throws JsonException
     */
    function () {
        $data = [
            'ChannelId' => 1,
            'GlobalChannelId' => 1,
            'ChannelOrderNo' => '12345',
            'Status' => 'NEW',
            'IsBusinessOrder' => false,
            'BillingAddress' => [],
            'ShippingAddress' => [],
            'Phone' => '1234567890',
            'Email' => 'test@example.com',
            'CurrencyCode' => 'USD',
            'OrderDate' => '2023-01-01',
        ];

        $this->orderRepository
            ->shouldReceive('createOrUpdateIfChanged')
            ->andReturnUsing(function ($attributes, $values) {
                return Order::factory()->create($attributes + $values);
            });

        $order = $this->orderService->syncOrder($data);

        expect($order)->not->toBeNull()
            ->and($order->channel_order_no)->toBe('12345');
    }
);

it('gets stock', function () {
    $product = [
        'Stock' => 100
    ];

    $this->ceService
        ->shouldReceive('getProduct')
        ->with('MPN-12345')
        ->andReturn($product);

    $stock = $this->orderService->getStock('MPN-12345');

    expect($stock)->toBe(100);
});

it('adds stock', function () {
    $this->ceService
        ->shouldReceive('getProduct')
        ->with('MPN-12345')
        ->andReturn(['Stock' => 100]);

    $this->ceService
        ->shouldReceive('setStock')
        ->with('MPN-12345', 1, 150)
        ->andReturn(true);

    $result = $this->orderService->addStock('MPN-12345', 1, 50);

    expect($result)->toBeTrue();
});
