<?php

namespace App\Domain\ProductCatalog\Actions;

use App\Domain\ProductCatalog\DTOs\CreateProductDTO;
use App\Domain\ProductCatalog\Models\Product;
use Illuminate\Support\Str;

class CreateProductAction
{
    public function execute(CreateProductDTO $dto): Product
    {
        return Product::create([
            'id' => Str::ulid(),
            'vendor_id' => $dto->vendorId,
            'name' => $dto->name,
            'description' => $dto->description,
            'price' => $dto->price,
            'stock' => $dto->stock,
            'image_url' => $dto->imageUrl,
        ]);
    }
}
