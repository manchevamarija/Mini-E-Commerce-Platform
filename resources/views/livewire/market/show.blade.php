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

<div style="font-family: 'Inter', sans-serif; max-width: 900px; margin: 0 auto;">

    <a href="{{ route('market.index') }}"
       style="display: inline-block; margin-bottom: 20px; font-size: 13px; color: #6366f1; text-decoration: none; font-weight: 500;">
        &larr; Back to Marketplace
    </a>

    <div style="background: white; border-radius: 16px; border: 1px solid #eee; overflow: hidden;">

        {{-- Image - fixed height --}}
        <img
            src="https://picsum.photos/seed/{{ $product->id }}/900/350"
            alt="{{ $product->name }}"
            style="width: 100%; height: 280px; object-fit: cover; display: block;"
        />

        {{-- Content --}}
        <div style="padding: 28px 32px;">

            <p style="font-size: 11px; font-weight: 700; color: #6366f1; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px 0;">
                {{ $product->vendor->shop_name }}
            </p>

            <h1 style="font-size: 26px; font-weight: 800; color: #111; margin: 0 0 6px 0; letter-spacing: -0.5px;">
                {{ $product->name }}
            </h1>

            <p style="font-size: 22px; font-weight: 800; color: #111; margin: 0 0 16px 0;">
                ${{ number_format($product->price, 2) }}
            </p>

            <p style="font-size: 14px; color: #555; line-height: 1.7; margin: 0 0 16px 0;">
                {{ $product->description }}
            </p>

            <p style="font-size: 12px; color: #aaa; margin: 0 0 24px 0;">
                {{ $product->stock }} items in stock
            </p>

            <hr style="border: none; border-top: 1px solid #f0f0f0; margin: 0 0 24px 0;" />

            {{-- Quantity + Add to Cart --}}
            <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label style="font-size: 13px; font-weight: 600; color: #333;">Quantity:</label>
                    <input
                        wire:model="quantity"
                        type="number"
                        min="1"
                        max="{{ $product->stock }}"
                        style="width: 70px; border: 1.5px solid #e5e5e5; border-radius: 8px; padding: 8px 10px; font-size: 14px; text-align: center; outline: none;"
                    />
                </div>

                <button
                    wire:click="addToCart"
                    style="flex: 1; background: #111; color: white; border: none; border-radius: 10px; padding: 12px 24px; font-size: 14px; font-weight: 700; cursor: pointer; min-width: 160px;">
                    Add to Cart
                </button>
            </div>

            @if($message)
                <div style="margin-top: 14px; padding: 10px 16px; border-radius: 8px; font-size: 13px; font-weight: 500;
                    background: {{ $success ? '#f0fdf4' : '#fef2f2' }};
                    color: {{ $success ? '#16a34a' : '#dc2626' }};">
                    {{ $message }}
                </div>
            @endif

        </div>
    </div>
</div>
