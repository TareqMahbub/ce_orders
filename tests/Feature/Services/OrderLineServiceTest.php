<?php /** @noinspection SpellCheckingInspection */

use App\Models\Order;
use App\Models\OrderLine;
use App\Models\Product;
use App\Repositories\OrderLineRepository;
use App\Services\OrderLineService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->orderLineRepository = Mockery::mock(OrderLineRepository::class);
    $this->orderLineService = new OrderLineService($this->orderLineRepository);

    $this->product = Product::safeInstance(Product::factory()->create());
    $this->order = Order::safeInstance(Order::factory()->create());
});

it('syncs order line', function () {
    $line = [
        'MerchantProductNo' => 'MPN-12345',
        'Gtin' => '1234567890123',
        'Quantity' => 10,
        'Description' => 'Order Line 1',
        'Id' => 1,
        'ChannelOrderLineNo' => 'COLN-12345',
        'IsFulfillmentByMarketplace' => true,
        'ChannelProductNo' => 'CPN-12345',
        'StockLocation' => ['Id' => 1],
    ];

    $attributes = [
        'order_id' => $this->order->id,
        'product_id' => $this->product->id,
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

    $this->orderLineRepository
        ->shouldReceive('createOrUpdateIfChanged')
        ->with($attributes, $values)
        ->andReturnUsing(function ($attributes, $values) {
            return OrderLine::factory()->create($attributes + $values);
        });

    $orderLine = $this->orderLineService->syncOrderLine($this->order->id, $this->product->id, $line);

    expect($orderLine)->not->toBeNull()
        ->and($orderLine->order_id)->toBe($this->order->id)
        ->and($orderLine->product_id)->toBe($this->product->id)
        ->and($orderLine->quantity)->toBe(10)
        ->and($orderLine->description)->toBe('Order Line 1')
        ->and($orderLine->ce_order_line_id)->toBe(1)
        ->and($orderLine->channel_order_line_no)->toBe('COLN-12345')
        ->and($orderLine->is_fulfillment_by_marketplace)->toBeTrue()
        ->and($orderLine->channel_product_no)->toBe('CPN-12345')
        ->and($orderLine->stock_location_id)->toBe(1);
});
