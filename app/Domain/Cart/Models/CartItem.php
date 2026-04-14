<?php

namespace App\Domain\Cart\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory;

    protected static function newFactory(): \Database\Factories\CartItemFactory
    {
        return \Database\Factories\CartItemFactory::new();
    }


    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    public function cart(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Domain\ProductCatalog\Models\Product::class);
    }
}
