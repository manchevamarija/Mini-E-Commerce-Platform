<?php

use App\Domain\Cart\Actions\AddToCartAction;
use App\Domain\Cart\Models\Cart;
use App\Domain\ProductCatalog\Models\Product;
use Livewire\Volt\Component;

new class extends Component {
    public Product $product;
    public int $quantity = 1;
    public string $message = '';
    public bool $success = false;

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    public function addToCart(): void
    {
        if (!auth()->check()) {
            $this->redirect(route('login'));
            return;
        }

        $user = auth()->user();
        $cart = $user->cart ?? Cart::create([
            'id' => \Illuminate\Support\Str::ulid(),
            'user_id' => $user->id,
        ]);

        try {
            app(AddToCartAction::class)->execute($cart, $this->product, $this->quantity);
            $this->success = true;
            $this->message = 'Added to cart!';
        } catch (\RuntimeException $e) {
            $this->success = false;
            $this->message = $e->getMessage();
        }
    }
}; ?>

<div class="max-w-4xl mx-auto px-4 py-8">
    <a href="{{ route('market.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">← Back to Marketplace</a>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
             class="w-full rounded-xl shadow" />

        <div>
            <h1 class="text-3xl font-bold mb-2">{{ $product->name }}</h1>
            <p class="text-gray-500 mb-1">by {{ $product->vendor->shop_name }}</p>
            <p class="text-2xl font-bold text-blue-600 mb-4">${{ number_format($product->price, 2) }}</p>
            <p class="text-gray-700 mb-4">{{ $product->description }}</p>
            <p class="text-sm text-gray-500 mb-4">In stock: {{ $product->stock }}</p>

            @if($message)
                <div class="mb-4 px-4 py-2 rounded {{ $success ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $message }}
                </div>
            @endif

            <div class="flex items-center gap-4 mb-4">
                <label class="text-sm font-medium">Quantity:</label>
                <input wire:model="quantity" type="number" min="1" max="{{ $product->stock }}"
                       class="w-20 border rounded px-3 py-1" />
            </div>

            <button wire:click="addToCart"
                    class="w-full bg-blue-600 text-white rounded-lg py-3 font-semibold hover:bg-blue-700">
                Add to Cart
            </button>
        </div>
    </div>
</div>
