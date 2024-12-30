<?php

namespace App\Services;

use App\Models\OrderLine;
use App\Repositories\OrderLineRepository;

class OrderLineService
{
    private OrderLineRepository $orderLineRepository;
    public function __construct(OrderLineRepository $orderLineRepository)
    {
        $this->orderLineRepository = $orderLineRepository;
    }

    /**
     * @param  int  $orderId
     * @param  int  $productId
     * @param  array  $line
     * @return OrderLine
     */
    public function syncOrderLine(int $orderId, int $productId, array $line): OrderLine
    {
        $attributes = [
            'order_id' => $orderId,
            'product_id' => $productId,
            'merchant_product_no' => $line['MerchantProductNo'],
            'gtin' => $line['Gtin'],
        ];

        $values = [
            'quantity' => $line['Quantity'],
            'description' => $line['Description'],
            'ce_order_line_id' => $line['Id'],
            'channel_order_line_no' => $line['ChannelOrderLineNo'],
            'is_fulfillment_by_marketplace' => $line['IsFulfillmentByMarketplace'],
            'channel_product_no' => $line['ChannelProductNo'],
            'stock_location_id' => $line['StockLocation']['Id'],
        ];

        return $this->orderLineRepository->createOrUpdateIfChanged($attributes, $values);
    }
}
