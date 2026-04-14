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
        // Edge case: empty cart should be rejected before creating an order
        if ($cart->items()->count() === 0) {
            throw new \RuntimeException('Cart is empty.');
        }

        // Stock validation happens at checkout time, not just at add-to-cart time.
        // This handles the case where stock was available when added but is gone by checkout.
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

            // Order is created first so we have an ID, but stays 'pending' until payment succeeds.
            // If payment fails, the transaction rolls back and the order is never persisted.
            $order = $this->createOrderAction->execute($dto, $cart);

            $paymentSuccess = $this->paymentSimulator->process($total, $paymentMethod->value);

            if (!$paymentSuccess) {
                // Transaction will roll back — order and any partial changes are discarded.
                throw new \RuntimeException('Payment failed. Orders over $999 are rejected.');
            }

            // TODO: handle race condition — two buyers purchasing the last item simultaneously.
            // A proper fix would use DB::select('SELECT ... FOR UPDATE') to lock the product rows.
            foreach ($cart->items()->with('product')->get() as $item) {
                $item->product->decrement('stock', $item->quantity);
            }

            $order->update(['status' => OrderStatus::Paid]);

            // Cart is cleared atomically within the same transaction.
            // If anything above fails, the cart remains intact.
            $cart->items()->delete();

            return $order->fresh();
        });
    }
}
