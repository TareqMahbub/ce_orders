<?php

namespace App\Repositories;

use App\Models\Product;
use App\Traits\HasMakeAble;

class ProductRepository
{
    use HasMakeAble;

    /**
     * @param  string  $merchantProductNo
     * @return Product|null
     */
    public function getProduct(string $merchantProductNo): ?Product
    {
        return Product::instance(Product::query()
            ->where('merchant_product_no', $merchantProductNo)
            ->first());
    }

    /**
     * @param  array  $attributes
     * @param  array  $values
     * @return Product
     */
    public function createOrUpdateIfChanged(array $attributes, array $values): Product
    {
        if ($order = Product::query()->where($attributes)->first()) {
            $order->fill($values);

            if (!$order->isDirty()) {
                return $order;
            }

            $order->update($values);
        } else {
            $order = Product::query()->create([...$attributes, ...$values]);
        }

        return $order;
    }
}
