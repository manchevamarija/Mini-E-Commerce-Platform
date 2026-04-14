<?php

namespace App\Domain\ProductCatalog\Models;

use App\Domain\ProductCatalog\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): \Database\Factories\ProductFactory
    {
        return \Database\Factories\ProductFactory::new();
    }

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'vendor_id',
        'name',
        'description',
        'price',
        'stock',
        'image_url',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'status' => ProductStatus::class,
        ];
    }

    public function vendor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function scopeActive($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', ProductStatus::Active);
    }

    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->id)) {
                $product->id = (string) Str::ulid();
            }
        });
    }


    public function scopeForVendor($query, Vendor $vendor): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('vendor_id', $vendor->id);
    }
}
