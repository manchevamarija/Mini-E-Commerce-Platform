<?php

namespace App\Domain\OrderManagement\Actions;

use App\Domain\Cart\Models\Cart;
use App\Domain\OrderManagement\DTOs\CreateOrderDTO;
use App\Domain\OrderManagement\Models\Order;
use App\Domain\OrderManagement\Models\OrderItem;
use Illuminate\Support\Str;

class CreateOrderAction
{
    public function execute(CreateOrderDTO $dto, Cart $cart): Order
    {
        $order = Order::create([
            'id' => Str::ulid(),
            'user_id' => $dto->userId,
            'status' => \App\Domain\OrderManagement\Enums\OrderStatus::Pending,
            'payment_method' => $dto->paymentMethod,
            'total' => $dto->total,
        ]);

        foreach ($cart->items()->with('product')->get() as $item) {
            OrderItem::create([
                'id' => Str::ulid(),
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'vendor_id' => $item->product->vendor_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        return $order;
    }
}
