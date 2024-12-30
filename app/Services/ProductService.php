<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Traits\HasMakeAble;
use JsonException;

class ProductService
{
    use HasMakeAble;
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getProduct(string $merchantProductNo): ?Product
    {
        return Product::instance(Product::query()
            ->where('merchant_product_no', $merchantProductNo)
            ->first());
    }

    /**
     * @throws JsonException
     */
    public function syncProduct(array $apiProduct): Product
    {
        $attributes = [
            'merchant_product_no' => $apiProduct['MerchantProductNo'],
        ];

        $values = [
            'is_active' => $apiProduct['IsActive'],
            'name' => $apiProduct['Name'],
            'description' => $apiProduct['Description'],
            'brand' => $apiProduct['Brand'],
            'ean' => $apiProduct['Ean'],
            'manufacturer_product_number' => $apiProduct['ManufacturerProductNumber'],
            'json' => json_encode($apiProduct, JSON_THROW_ON_ERROR),
        ];

        return $this->productRepository->createOrUpdateIfChanged($attributes, $values);
    }
}
