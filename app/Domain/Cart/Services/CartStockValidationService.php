<?php

namespace App\Domain\Cart\Services;

use App\Domain\Cart\Models\Cart;

class CartStockValidationService
{
    public function validate(Cart $cart): array
    {
        $errors = [];

        foreach ($cart->items()->with('product')->get() as $item) {
            if ($item->product === null) {
                $errors[] = "Product no longer exists.";
                continue;
            }

            if ($item->quantity > $item->product->stock) {
                $errors[] = "'{$item->product->name}' only has {$item->product->stock} units available.";
            }
        }

        return $errors;
    }

    public function passes(Cart $cart): bool
    {
        return empty($this->validate($cart));
    }
}
