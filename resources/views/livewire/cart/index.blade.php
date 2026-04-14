<?php

use App\Domain\Cart\Actions\RemoveFromCartAction;
use App\Domain\Cart\Models\Cart;
use Livewire\Volt\Component;

new class extends Component {
    public function removeItem(string $itemId): void
    {
        $cart = auth()->user()->cart;
        $item = $cart->items()->find($itemId);
        if ($item) {
            app(RemoveFromCartAction::class)->execute($item);
        }
    }

    public function updateQuantity(string $itemId, int $quantity): void
    {
        $cart = auth()->user()->cart;
        $item = $cart->items()->with('product')->find($itemId);

        if (!$item) return;

        if ($quantity < 1) {
            $item->delete();
            return;
        }

        if ($quantity > $item->product->stock) {
            session()->flash('error', "Only {$item->product->stock} units available.");
            return;
        }

        $item->update(['quantity' => $quantity]);
    }

    public function with(): array
    {
        $cart = auth()->user()->cart;
        $items = $cart ? $cart->items()->with(['product.vendor'])->get() : collect();
        $grouped = $items->groupBy(fn($item) => $item->product->vendor->shop_name);
        $total = $items->sum(fn($item) => $item->product->price * $item->quantity);

        return compact('items', 'grouped', 'total', 'cart');
    }
}; ?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Your Cart</h1>

    @if(session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">{{ session('error') }}</div>
    @endif

    @if($items->isEmpty())
        <div class="text-center text-gray-500 py-16">
            <p class="text-xl mb-4">Your cart is empty.</p>
            <a href="{{ route('market.index') }}" class="text-blue-600 hover:underline">Browse Marketplace</a>
        </div>
    @else
        @foreach($grouped as $shopName => $shopItems)
            <div class="mb-6 bg-white rounded-xl shadow p-4">
                <h2 class="font-semibold text-lg mb-3 text-gray-700">{{ $shopName }}</h2>
                @foreach($shopItems as $item)
                    <div class="flex items-center justify-between py-3 border-b last:border-0">
                        <div class="flex items-center gap-4">
                            <img src="{{ $item->product->image_url }}" class="w-16 h-16 object-cover rounded" />
                            <div>
                                <p class="font-medium">{{ $item->product->name }}</p>
                                <p class="text-sm text-gray-500">${{ number_format($item->product->price, 2) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="number" value="{{ $item->quantity }}" min="1"
                                   wire:change="updateQuantity('{{ $item->id }}', $event.target.value)"
                                   class="w-16 border rounded px-2 py-1 text-center" />
                            <p class="font-semibold w-20 text-right">${{ number_format($item->product->price * $item->quantity, 2) }}</p>
                            <button wire:click="removeItem('{{ $item->id }}')" class="text-red-500 hover:text-red-700">✕</button>
                        </div>
                    </div>
                @endforeach
                <p class="text-right text-sm text-gray-500 mt-2">
                    Subtotal: ${{ number_format($shopItems->sum(fn($i) => $i->product->price * $i->quantity), 2) }}
                </p>
            </div>
        @endforeach

        <div class="bg-white rounded-xl shadow p-4 flex justify-between items-center">
            <p class="text-xl font-bold">Total: ${{ number_format($total, 2) }}</p>
            <a href="{{ route('checkout.index') }}"
               class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700">
                Proceed to Checkout
            </a>
        </div>
    @endif
</div>
