<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Traits\HasMakeAble;
use Exception;
use JsonException;
use RuntimeException;

class OrderService
{
    use HasMakeAble;

    private OrderRepository $orderRepository;
    private CeService $ceService;
    private OrderLineService $orderLineService;
    private ProductService $productService;

    public function __construct(
        OrderRepository $orderRepository,
        CeService $ceService,
        OrderLineService $orderLineService,
        ProductService $productService
    ) {
        $this->orderRepository = $orderRepository;
        $this->ceService = $ceService;
        $this->orderLineService = $orderLineService;
        $this->productService = $productService;
    }

    /**
     * Sync orders from CE
     *
     * @throws Exception
     */
    public function syncOrders(): void
    {
        $page = 1;
        while ($orders = $this->ceService->fetchPagedOrders($page)) {
            echo "Syncing orders: page $page, fetched: " . count($orders) . " orders\n";

            foreach($orders as $order) {
                $syncedOrder = $this->syncOrder($order);

                foreach($order['Lines'] as $line) {
                    if (array_key_exists('MerchantProductNo', $line)) {
                        $product = $this->productService->getOrSyncProduct($line['MerchantProductNo']);

                        if ($product) {
                            $this->orderLineService->syncOrderLine($syncedOrder->id, $product->id, $line);
                        }
                    }
                }
            }

            $page++;
        }
    }

    /**
     * @param  array  $data
     * @return Order
     * @throws JsonException
     */
    public function syncOrder(array $data): Order
    {
        $attributes = [
            'channel_id' => $data['ChannelId'],
            'global_channel_id' => $data['GlobalChannelId'],
            'channel_order_no' => $data['ChannelOrderNo']
        ];

        $values = [
            'status' => $data['Status'],
            'is_business_order' => $data['IsBusinessOrder'],
            'billing_address' => json_encode($data['BillingAddress'], JSON_THROW_ON_ERROR),
            'shipping_address' => json_encode($data['ShippingAddress'], JSON_THROW_ON_ERROR),
            'phone' => $data['Phone'],
            'email' => $data['Email'],
            'currency_code' => $data['CurrencyCode'],
            'order_date' => $data['OrderDate'],
            'json' => json_encode($data, JSON_THROW_ON_ERROR),
        ];

        return $this->orderRepository->createOrUpdateIfChanged($attributes, $values);
    }

    /**
     * @param  string  $merchantProductNo
     * @return int
     * @throws RuntimeException
     */
    public function getStock(string $merchantProductNo): int
    {
        $product = $this->ceService->getProduct($merchantProductNo);

        if (array_key_exists('Stock', $product)) {
            return $product['Stock'];
        }

        throw new RuntimeException("Failed to get the stock for merchant product no: $merchantProductNo");
    }

    /**
     * @param  string  $merchantProductNo
     * @param  int  $stockLocationId
     * @param  int  $newStock
     * @return bool
     */
    public function addStock(string $merchantProductNo, int $stockLocationId, int $newStock): bool
    {
        $currentStock = $this->getStock($merchantProductNo);

        return $this->ceService->setStock($merchantProductNo, $stockLocationId, $currentStock + $newStock);
    }
}
