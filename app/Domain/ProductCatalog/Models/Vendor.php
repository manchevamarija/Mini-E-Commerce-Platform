<?php

namespace App\Domain\ProductCatalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory;
    protected static function newFactory(): \Database\Factories\VendorFactory
    {
        return \Database\Factories\VendorFactory::new();
    }

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'shop_name',
        'description',
        'logo_url',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Domain\IdentityAndAccess\Models\User::class);
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class);
    }
}
