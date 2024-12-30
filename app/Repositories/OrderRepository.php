<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderLine;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    /**
     * @param  array  $attributes
     * @param  array  $values
     * @return Order
     */
    public function createOrUpdateIfChanged(array $attributes, array $values): Order
    {
        if ($order = Order::query()->where($attributes)->first()) {
            $order->fill($values);

            if (!$order->isDirty()) {
                return $order;
            }

            $order->update($values);
        } else {
            $order = Order::query()->create([...$attributes, ...$values]);
        }

        return $order;
    }
}
