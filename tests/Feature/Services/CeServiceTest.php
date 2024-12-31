<?php

use App\Services\CeService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->ceService = new CeService();
});

it('should fetch paged orders', function () {
    Http::fake([
        '*' => Http::response([
            'StatusCode' => 200,
            'Success' => true,
            'Content' => [
                [
                    'ChannelId' => 51,
                    'GlobalChannelId' => 1633,
                    'ChannelOrderNo' => '404-4009207-6030747',
                    'Status' => 'NEW',
                    'Lines' => [
                        [
                            'Gtin' => 'GTIN-12345',
                            'Description' => 'Test Product',
                            'Quantity' => 1,
                            'MerchantProductNo' => 'MPN-12345',
                            'ChannelProductNo' => 'CPN-12345',
                            'StockLocation' => [
                                'Id' => 1
                            ]
                        ]
                    ]
                ],
                [
                    'ChannelId' => 51,
                    'GlobalChannelId' => 1633,
                    'ChannelOrderNo' => '407-9040307-4220322',
                    'Status' => 'NEW',
                    'Lines' => [
                        [
                            'Gtin' => 'GTIN-54321',
                            'Description' => 'Test Product 2',
                            'Quantity' => 2,
                            'MerchantProductNo' => 'MPN-54321',
                            'ChannelProductNo' => 'CPN-54321',
                            'StockLocation' => [
                                'Id' => 2
                            ]
                        ]
                    ]
                ]
            ]
        ], 200)
    ]);

    $orders = $this->ceService->fetchPagedOrders(1, ['NEW']);

    expect($orders)->toBeArray()
        ->and(count($orders))->toBe(2)
        ->and($orders[0]['ChannelOrderNo'])->toBe('404-4009207-6030747')
        ->and($orders[0]['Lines'][0]['Gtin'])->toBe('GTIN-12345')
        ->and($orders[1]['ChannelOrderNo'])->toBe('407-9040307-4220322')
        ->and($orders[1]['Lines'][0]['Gtin'])->toBe('GTIN-54321');
});

it('should set stock successfully', function () {
    Http::fake([
        '*' => Http::response(['StatusCode' => 200, 'Success' => true])
    ]);

    $result = $this->ceService->setStock('MPN-12345', 1, 100);

    expect($result)->toBeTrue();
});

it('should get product successfully', function () {
    Http::fake([
        '*' => Http::response([
            'StatusCode' => 200,
            'Success' => true,
            'Content' => [
                'merchant_product_no' => 'MPN-12345',
                'name' => 'Test Product'
            ]
        ])
    ]);

    $product = $this->ceService->getProduct('MPN-12345');

    expect($product)->toBeArray()
        ->and($product['merchant_product_no'])->toBe('MPN-12345')
        ->and($product['name'])->toBe('Test Product');
});

it('should return empty array when getting product fails', function () {
    Http::fake([
        '*' => Http::response(null, 500)
    ]);

    $product = $this->ceService->getProduct('MPN-12345');

    expect($product)->toBeArray()
        ->and($product)->toBeEmpty();
});
