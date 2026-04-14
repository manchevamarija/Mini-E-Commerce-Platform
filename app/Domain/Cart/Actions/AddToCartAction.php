<?php

namespace App\Domain\Cart\Actions;

use App\Domain\Cart\Models\Cart;
use App\Domain\Cart\Models\CartItem;
use App\Domain\ProductCatalog\Models\Product;
use Illuminate\Support\Str;

class AddToCartAction
{
    public function execute(Cart $cart, Product $product, int $quantity = 1): CartItem
    {
        $existingItem = $cart->items()->where('product_id', $product->id)->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;

            if ($newQuantity > $product->stock) {
                throw new \RuntimeException("Not enough stock. Available: {$product->stock}");
            }

            $existingItem->update(['quantity' => $newQuantity]);
            return $existingItem->fresh();
        }

        if ($quantity > $product->stock) {
            throw new \RuntimeException("Not enough stock. Available: {$product->stock}");
        }

        return CartItem::create([
            'id' => Str::ulid(),
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
        ]);
    }
}
