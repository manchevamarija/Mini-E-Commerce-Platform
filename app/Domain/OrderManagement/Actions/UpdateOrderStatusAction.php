<?php

namespace App\Domain\OrderManagement\Actions;

use App\Domain\OrderManagement\Enums\OrderStatus;
use App\Domain\OrderManagement\Models\Order;

class UpdateOrderStatusAction
{
    public function execute(Order $order, OrderStatus $newStatus): Order
    {
        if (!$order->status->canTransitionTo($newStatus)) {
            throw new \RuntimeException(
                "Cannot transition from {$order->status->value} to {$newStatus->value}"
            );
        }

        $order->update(['status' => $newStatus]);
        return $order->fresh();
    }
}
