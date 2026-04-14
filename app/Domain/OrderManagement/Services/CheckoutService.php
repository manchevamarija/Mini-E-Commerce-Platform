<?php

namespace App\Domain\OrderManagement\Services;

use App\Domain\Cart\Models\Cart;
use App\Domain\Cart\Services\CartStockValidationService;
use App\Domain\OrderManagement\Actions\CreateOrderAction;
use App\Domain\OrderManagement\DTOs\CreateOrderDTO;
use App\Domain\OrderManagement\Enums\OrderStatus;
use App\Domain\OrderManagement\Enums\PaymentMethod;
use App\Domain\OrderManagement\Models\Order;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(
        private CartStockValidationService $stockValidator,
        private CreateOrderAction $createOrderAction,
        private PaymentSimulatorService $paymentSimulator,
    ) {}

    public function checkout(Cart $cart, PaymentMethod $paymentMethod): Order
    {
        if ($cart->items()->count() === 0) {
            throw new \RuntimeException('Cart is empty.');
        }

        $errors = $this->stockValidator->validate($cart);
        if (!empty($errors)) {
            throw new \RuntimeException('Stock validation failed: ' . implode(', ', $errors));
        }

        $total = $cart->total();

        return DB::transaction(function () use ($cart, $paymentMethod, $total) {
            $dto = new CreateOrderDTO(
                userId: $cart->user_id,
                paymentMethod: $paymentMethod,
                total: $total,
            );

            $order = $this->createOrderAction->execute($dto, $cart);

            $paymentSuccess = $this->paymentSimulator->process($total, $paymentMethod->value);

            if (!$paymentSuccess) {
                throw new \RuntimeException('Payment failed. Orders over $999 are rejected.');
            }

            foreach ($cart->items()->with('product')->get() as $item) {
                $item->product->decrement('stock', $item->quantity);
            }

            $order->update(['status' => OrderStatus::Paid]);

            $cart->items()->delete();

            return $order->fresh();
        });
    }
}
