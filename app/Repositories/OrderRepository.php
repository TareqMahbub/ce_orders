<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderLine;
use App\Traits\HasMakeAble;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    use HasMakeAble;
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

    /**
     * @param  int  $limit
     * @return Collection
     */
    public function getTopSoldProducts(int $limit = 5): Collection
    {
        return OrderLine::query()
            ->join('products', 'order_lines.product_id', '=', 'products.id')
            ->select(['products.name as product_name', 'order_lines.merchant_product_no', 'order_lines.stock_location_id', 'order_lines.gtin', DB::raw('SUM(order_lines.quantity) as total_quantity')])
            ->whereNotNull(['order_lines.gtin', 'order_lines.merchant_product_no', 'order_lines.stock_location_id'])
            ->groupBy(['product_name', 'order_lines.merchant_product_no', 'order_lines.stock_location_id', 'order_lines.gtin'])
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();
    }
}
