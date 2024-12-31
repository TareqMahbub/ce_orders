<?php

use App\Models\Product;
use App\Services\ProductService;
use App\Repositories\ProductRepository;
use App\Services\CeService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->productRepository = Mockery::mock(ProductRepository::class);
    $this->ceService = Mockery::mock(CeService::class);
    $this->productService = new ProductService($this->productRepository, $this->ceService);
});

it( 'returns existing product in getOrSyncProduct',
    /**
     * @throws JsonException
     */
    function () {
        $merchantProductNo = 'MPN-12345';
        $product = Product::factory()->create(['merchant_product_no' => $merchantProductNo]);

        $this->productRepository
            ->shouldReceive('getProduct')
            ->with($merchantProductNo)
            ->andReturn($product);

        $result = $this->productService->getOrSyncProduct($merchantProductNo);

        expect($result)->not->toBeNull()
            ->and($result?->merchant_product_no)->toBe($merchantProductNo);
    }
);

it( 'syncs new product in getOrSyncProduct',
    /**
     * @throws JsonException
     */
    function () {
        $merchantProductNo = 'MPN-12345';
        $apiProduct = [
            'MerchantProductNo' => $merchantProductNo,
            'IsActive' => true,
            'Name' => 'Test Product',
            'Description' => 'Test Description',
            'Brand' => 'Test Brand',
            'Ean' => '1234567890123',
            'ManufacturerProductNumber' => 'MPN-12345',
        ];

        $this->productRepository
            ->shouldReceive('getProduct')
            ->with($merchantProductNo)
            ->andReturn(null);

        $this->ceService
            ->shouldReceive('getProduct')
            ->with($merchantProductNo)
            ->andReturn($apiProduct);

        $this->productRepository
            ->shouldReceive('createOrUpdateIfChanged')
            ->andReturnUsing(function ($attributes, $values) {
                return Product::factory()->create($attributes + $values);
            });

        $result = $this->productService->getOrSyncProduct($merchantProductNo);

        expect($result)->not->toBeNull()
            ->and($result?->merchant_product_no)->toBe($merchantProductNo)
            ->and($result?->name)->toBe('Test Product');
    }
);

it( 'syncs product in syncProduct',
    /**
     * @throws JsonException
     */
    function () {
        $apiProduct = [
            'MerchantProductNo' => 'MPN-12345',
            'IsActive' => true,
            'Name' => 'Test Product',
            'Description' => 'Test Description',
            'Brand' => 'Test Brand',
            'Ean' => '1234567890123',
            'ManufacturerProductNumber' => 'MPN-12345',
        ];

        $this->productRepository
            ->shouldReceive('createOrUpdateIfChanged')
            ->andReturnUsing(function ($attributes, $values) {
                return Product::factory()->create($attributes + $values);
            });

        $result = $this->productService->syncProduct($apiProduct);

        expect($result)->not->toBeNull()
            ->and($result->merchant_product_no)->toBe('MPN-12345')
            ->and($result->name)->toBe('Test Product');
    }
);
