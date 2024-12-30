<?php

namespace App\Repositories;

use App\Models\OrderLine;

class OrderLineRepository
{
    /**
     * @param  array  $attributes
     * @param  array  $values
     * @return OrderLine
     */
    public function createOrUpdateIfChanged(array $attributes, array $values): OrderLine
    {
        if ($order = OrderLine::query()->where($attributes)->first()) {
            $order->fill($values);

            if (!$order->isDirty()) {
                return $order;
            }

            $order->update($values);
        } else {
            $order = OrderLine::query()->create([...$attributes, ...$values]);
        }

        return $order;
    }
}
