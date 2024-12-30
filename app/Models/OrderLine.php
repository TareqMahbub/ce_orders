<?php

/** @noinspection SpellCheckingInspection */

namespace App\Models;

use App\Traits\HasSelfCasting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $order_id
 * @property string $merchant_product_no
 * @property string $gtin
 * @property int $quantity
 * @property string $description
 * @property string $ce_order_line_id
 * @property string $channel_order_line_no
 * @property bool $is_fulfillment_by_marketplace
 * @property int $channel_product_no
 * @property int $stock_location_id
 * @property string $json
 *
 * @property Order $order
 * @property Product $product
 */
class OrderLine extends Model
{
    use HasFactory, HasSelfCasting;

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'merchant_product_no',
        'gtin',
        'quantity',
        'description',
        'ce_order_line_id',
        'channel_order_line_no',
        'is_fulfillment_by_marketplace',
        'channel_product_no',
        'stock_location_id',
        'json'
    ];

    protected $casts = [
        'is_fulfillment_by_marketplace' => 'boolean',
    ];

    /**
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'merchant_product_no';
    }

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
