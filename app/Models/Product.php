<?php

/** @noinspection SpellCheckingInspection */

namespace App\Models;

use App\Traits\HasSelfCasting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
