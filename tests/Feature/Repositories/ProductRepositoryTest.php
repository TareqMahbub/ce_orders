<?php

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Container\BindingResolutionException;

beforeEach(
    /**
     * @throws BindingResolutionException
     */
    function () {
        $this->productRepository = ProductRepository::make();

        $this->product = Product::safeInstance(Product::factory()->create());
    }
);

it('should get product by merchant product no', function () {
    $product = $this->productRepository->getProduct($this->product->merchant_product_no);

    expect($product)->not->toBeNull()
        ->and($product?->id)->toBe($this->product->id);
});

it('should create or update product if changed', function () {
    $attributes = ['merchant_product_no' => '123'];
    $values = ['name' => 'Product 123', 'is_active' => true];

    $product = $this->productRepository->createOrUpdateIfChanged($attributes, $values);

    expect($product->merchant_product_no)->toBe('123')
        ->and($product->name)->toBe('Product 123');

    $values = ['name' => 'Product 123 Updated'];

    $product = $this->productRepository->createOrUpdateIfChanged($attributes, $values);

    expect($product->merchant_product_no)->toBe('123')
        ->and($product->name)->toBe('Product 123 Updated');
});
