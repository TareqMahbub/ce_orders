<?php

use App\Services\CeService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->ceService = new CeService();
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

it('should fail with bad StatusCode', function () {
    Http::fake([
        '*' => Http::response([
            'StatusCode' => 400,
            'Success' => true,
            'Content' => [
                'merchant_product_no' => 'MPN-12345',
                'name' => 'Test Product'
            ]
        ])
    ]);

    $product = $this->ceService->getProduct('MPN-12345');

    expect($product)->toBeArray()
        ->and($product)->toBeEmpty();
});

it('should fail when Success = false', function () {
    Http::fake([
        '*' => Http::response([
            'StatusCode' => 200,
            'Success' => false,
            'Content' => [
                'merchant_product_no' => 'MPN-12345',
                'name' => 'Test Product'
            ]
        ])
    ]);

    $product = $this->ceService->getProduct('MPN-12345');

    expect($product)->toBeArray()
        ->and($product)->toBeEmpty();
});

it('should fail when Content empty', function () {
    Http::fake([
        '*' => Http::response([
            'StatusCode' => 200,
            'Success' => true,
            'Content' => []
        ])
    ]);

    $product = $this->ceService->getProduct('MPN-12345');

    expect($product)->toBeArray()
        ->and($product)->toBeEmpty();
});

it('should fail without StatusCode', function () {
    Http::fake([
        '*' => Http::response([
            'Success' => true,
            'Content' => [
                'merchant_product_no' => 'MPN-12345',
                'name' => 'Test Product'
            ]
        ])
    ]);

    $product = $this->ceService->getProduct('MPN-12345');

    expect($product)->toBeArray()
        ->and($product)->toBeEmpty();
});

it('should fail without Success', function () {
    Http::fake([
        '*' => Http::response([
            'StatusCode' => 200,
            'Content' => [
                'merchant_product_no' => 'MPN-12345',
                'name' => 'Test Product'
            ]
        ])
    ]);

    $product = $this->ceService->getProduct('MPN-12345');

    expect($product)->toBeArray()
        ->and($product)->toBeEmpty();
});

it('should fail without Content', function () {
    Http::fake([
        '*' => Http::response([
            'StatusCode' => 200,
            'Success' => true
        ])
    ]);

    $product = $this->ceService->getProduct('MPN-12345');

    expect($product)->toBeArray()
        ->and($product)->toBeEmpty();
});
