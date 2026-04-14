<?php

use App\Domain\OrderManagement\Enums\PaymentMethod;
use App\Domain\OrderManagement\Services\CheckoutService;
use Livewire\Volt\Component;

new class extends Component {
    public string $paymentMethod = 'credit_card';
    public string $error = '';

    public function checkout(): void
    {
        $user = auth()->user();
        $cart = $user->cart;

        if (!$cart || $cart->items()->count() === 0) {
            $this->error = 'Your cart is empty.';
            return;
        }

        try {
            $order = app(CheckoutService::class)->checkout(
                $cart,
                PaymentMethod::from($this->paymentMethod)
            );

            $this->redirect(route('order.confirmation', $order));
        } catch (\RuntimeException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function with(): array
    {
        $cart = auth()->user()->cart;
        $items = $cart ? $cart->items()->with(['product.vendor'])->get() : collect();
        $total = $items->sum(fn($item) => $item->product->price * $item->quantity);

        return compact('items', 'total');
    }
}; ?>

<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Checkout</h1>

    @if($error)
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
    @endif

    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="font-semibold text-lg mb-4">Order Summary</h2>
        @foreach($items as $item)
            <div class="flex justify-between py-2 border-b last:border-0">
                <span>{{ $item->product->name }} × {{ $item->quantity }}</span>
                <span>${{ number_format($item->product->price * $item->quantity, 2) }}</span>
            </div>
        @endforeach
        <div class="flex justify-between font-bold text-lg mt-4">
            <span>Total</span>
            <span>${{ number_format($total, 2) }}</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="font-semibold text-lg mb-4">Payment Method</h2>
        <div class="flex gap-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" wire:model="paymentMethod" value="credit_card" />
                <span>Credit Card</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" wire:model="paymentMethod" value="wallet" />
                <span>Wallet</span>
            </label>
        </div>
    </div>

    <button wire:click="checkout" wire:loading.attr="disabled"
            class="w-full bg-blue-600 text-white rounded-lg py-3 font-semibold hover:bg-blue-700 disabled:opacity-50">
        <span wire:loading.remove>Place Order</span>
        <span wire:loading>Processing...</span>
    </button>
</div>
