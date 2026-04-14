<?php

namespace App\Domain\ProductCatalog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vendor extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'shop_name',
        'description',
        'logo_url',
    ];

    protected static function booted(): void
    {
        static::creating(function ($vendor) {
            if (empty($vendor->id)) {
                $vendor->id = (string) Str::ulid();
            }
        });
    }

    protected static function newFactory(): \Database\Factories\VendorFactory
    {
        return \Database\Factories\VendorFactory::new();
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Domain\IdentityAndAccess\Models\User::class);
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class);
    }
}
