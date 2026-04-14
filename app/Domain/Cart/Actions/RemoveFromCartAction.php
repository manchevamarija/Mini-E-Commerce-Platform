<?php

namespace App\Domain\Cart\Actions;

use App\Domain\Cart\Models\CartItem;

class RemoveFromCartAction
{
    public function execute(CartItem $item): void
    {
        $item->delete();
    }
}
