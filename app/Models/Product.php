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
 * @property bool $is_active
 * @property string $name
 * @property string $description
 * @property string $brand
 * @property string $merchant_product_no
 * @property string $manufacturer_product_number
 * @property string $ean
 * @property string $json
 *
 * @property Collection<int, OrderLine> $orderLines
 */
class Product extends Model
{
    use HasFactory, HasSelfCasting;

    protected $fillable = [
        'is_active',
        'name',
        'description',
        'brand',
        'merchant_product_no',
        'manufacturer_product_number',
        'ean',
        'json',
    ];

    public function orderLines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }
}
