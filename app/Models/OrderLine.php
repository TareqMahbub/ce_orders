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
 * @property int $quantity
 * @property int $channel_product_no
 * @property string $merchant_product_no
 * @property string $stock_location_id
 * @property string|null $gtin
 * @property string $description
 *
 * @property Order $order
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
