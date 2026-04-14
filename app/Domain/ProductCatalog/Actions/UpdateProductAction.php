<?php

namespace App\Domain\ProductCatalog\Actions;

use App\Domain\ProductCatalog\Models\Product;

class UpdateProductAction
{
    public function execute(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh();
    }
}
