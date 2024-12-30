<?php

/** @noinspection SpellCheckingInspection */

namespace App\Models;

use App\Traits\HasSelfCasting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $channel_order_no
 * @property string $status
 *
 * @property Collection<int, OrderLine> $lines
 */
class Order extends Model
{
    use HasFactory, HasSelfCasting;

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'channel_id',
        'global_channel_id',
        'channel_order_no',
        'status',
        'is_business_order',
        'billing_address',
        'shipping_address',
        'phone',
        'email',
        'currency_code',
        'order_date',
        'json'
    ];

    /**
     * @return HasMany
     */
    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    public function orderLines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }
}
