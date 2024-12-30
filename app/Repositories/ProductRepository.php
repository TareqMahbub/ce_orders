<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
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