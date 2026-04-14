<?php

namespace App\Domain\Cart\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Cart extends Model
{
    use HasFactory;

    protected static function newFactory(): \Database\Factories\CartFactory
    {
        return \Database\Factories\CartFactory::new();
    }

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
    ];


    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Domain\IdentityAndAccess\Models\User::class);
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function total(): float
    {
        return $this->items->sum(fn($item) => $item->product->price * $item->quantity);
    }
}
