<?php

use App\Services\CeService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->ceService = new CeService();
});

it('can fetch paged orders', function () {
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
        ])
    ]);

    $orders = $this->ceService->fetchPagedOrders();

    expect($orders)->toBeArray()
        ->and(count($orders))->toBe(2)
        ->and($orders[0]['ChannelOrderNo'])->toBe('404-4009207-6030747')
        ->and($orders[0]['Lines'][0]['Gtin'])->toBe('GTIN-12345')
        ->and($orders[1]['ChannelOrderNo'])->toBe('407-9040307-4220322')
        ->and($orders[1]['Lines'][0]['Gtin'])->toBe('GTIN-54321');
});

it('should not fetch paged orders with wrong StatusCode', function () {
    Http::fake([
        '*' => Http::response([
            'StatusCode' => 400,
            'Success' => true,
            'Content' => []
        ])
    ]);

    $orders = $this->ceService->fetchPagedOrders();

    expect($orders)->toBeArray()
        ->and(count($orders))->toBe(0);
});

it('should not fetch paged orders with Success=false', function () {
    Http::fake([
        '*' => Http::response([
            'StatusCode' => 400,
            'Success' => true,
            'Content' => []
        ])
    ]);

    $orders = $this->ceService->fetchPagedOrders();

    expect($orders)->toBeArray()
        ->and(count($orders))->toBe(0);
});

it('should fail without StatusCode in response', function () {

    Http::fake([
        '*' => Http::response([
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
        ])
    ]);

    $orders = $this->ceService->fetchPagedOrders();

    expect($orders)->toBeArray()
        ->and(count($orders))->toBe(0);
});

it('should fail without Success in response', function () {
    Http::fake([
        '*' => Http::response([
            'StatusCode' => 200,
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
        ])
    ]);

    $orders = $this->ceService->fetchPagedOrders();

    expect($orders)->toBeArray()
        ->and(count($orders))->toBe(0);
});

it('should fail without Content in response', function () {
    Http::fake([
        '*' => Http::response([
            'StatusCode' => 200,
            'Success' => true
        ])
    ]);

    $orders = $this->ceService->fetchPagedOrders();

    expect($orders)->toBeArray()
        ->and(count($orders))->toBe(0);
});

it('should fail with empty Content response', function () {
    Http::fake([
        '*' => Http::response([
            'StatusCode' => 200,
            'Success' => true,
            'Content' => []
        ])
    ]);

    $orders = $this->ceService->fetchPagedOrders();

    expect($orders)->toBeArray()
        ->and(count($orders))->toBe(0);
});
