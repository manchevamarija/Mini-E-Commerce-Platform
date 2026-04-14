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

    public function with(): array
    {
        $related = Product::with('vendor')
            ->where('vendor_id', $this->product->vendor_id)
            ->where('id', '!=', $this->product->id)
            ->active()
            ->orderBy('price', 'asc')
            ->take(4)
            ->get();

        $recommended = Product::with('vendor')
            ->active()
            ->where('id', '!=', $this->product->id)
            ->where(function ($query) {
                $query->where('price', '<', $this->product->price)
                    ->orWhere(function ($q) {
                        $q->where('stock', '>', 0)
                            ->where('stock', '<=', 5);
                    });
            })
            ->orderByRaw("
                CASE
                    WHEN stock > 0 AND stock <= 5 THEN 0
                    ELSE 1
                END
            ")
            ->orderBy('price', 'asc')
            ->take(4)
            ->get();

        return compact('related', 'recommended');
    }
}; ?>

<div style="font-family: 'Inter', sans-serif; max-width: 900px; margin: 0 auto;">

    <a href="{{ route('market.index') }}"
       style="display: inline-block; margin-bottom: 20px; font-size: 13px; color: #6366f1; text-decoration: none; font-weight: 500;">
        &larr; Back to Marketplace
    </a>

    <div style="background: white; border-radius: 16px; border: 1px solid #eee; overflow: hidden;">
        <img src="https://picsum.photos/seed/{{ $product->id }}/900/350"
             alt="{{ $product->name }}"
             style="width: 100%; height: 280px; object-fit: cover; display: block;" />

        <div style="padding: 28px 32px;">
            <a href="{{ route('market.vendor', $product->vendor) }}"
               style="font-size: 11px; font-weight: 700; color: #6366f1; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px 0; text-decoration: none; display: block;">
                {{ $product->vendor->shop_name }}
            </a>

            <h1 style="font-size: 26px; font-weight: 800; color: #111; margin: 0 0 6px 0;">{{ $product->name }}</h1>
            <p style="font-size: 22px; font-weight: 800; color: #111; margin: 0 0 16px 0;">
                ${{ number_format($product->price, 2) }}
            </p>
            <p style="font-size: 14px; color: #555; line-height: 1.7; margin: 0 0 16px 0;">
                {{ $product->description }}
            </p>

            @if($product->stock === 0)
                <p style="font-size: 12px; color: #ef4444; font-weight: 700; margin: 0 0 24px 0;">
                    Out of stock
                </p>
            @elseif($product->stock <= 5)
                <p style="font-size: 12px; color: #ef4444; font-weight: 700; margin: 0 0 24px 0;">
                    Only {{ $product->stock }} left!
                </p>
            @else
                <p style="font-size: 12px; color: #aaa; margin: 0 0 24px 0;">
                    {{ $product->stock }} items in stock
                </p>
            @endif

            <hr style="border: none; border-top: 1px solid #f0f0f0; margin: 0 0 24px 0;" />

            <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label style="font-size: 13px; font-weight: 600; color: #333;">Quantity:</label>
                    <input wire:model="quantity"
                           type="number"
                           min="1"
                           max="{{ $product->stock }}"
                           style="width: 70px; border: 1.5px solid #e5e5e5; border-radius: 8px; padding: 8px 10px; font-size: 14px; text-align: center; outline: none;" />
                </div>

                <button wire:click="addToCart"
                        @if($product->stock === 0) disabled @endif
                        style="flex: 1; background: {{ $product->stock === 0 ? '#ccc' : '#111' }}; color: white; border: none; border-radius: 10px; padding: 12px 24px; font-size: 14px; font-weight: 700; cursor: {{ $product->stock === 0 ? 'not-allowed' : 'pointer' }}; min-width: 160px;">
                    {{ $product->stock === 0 ? 'Out of Stock' : 'Add to Cart' }}
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

    @if($related->count() > 0)
        <div style="margin-top: 40px;">
            <h2 style="font-size: 18px; font-weight: 800; color: #111; margin: 0 0 16px 0;">
                More from {{ $product->vendor->shop_name }}
            </h2>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;">
                @foreach($related as $item)
                    <a href="{{ route('market.show', $item) }}" style="text-decoration: none;">
                        <div style="background: white; border-radius: 12px; border: 1px solid #eee; overflow: hidden;">
                            <img src="https://picsum.photos/seed/{{ $item->id }}/400/220"
                                 alt="{{ $item->name }}"
                                 style="width: 100%; height: 110px; object-fit: cover; display: block;" />

                            <div style="padding: 10px 12px;">
                                <p style="font-size: 12px; font-weight: 700; color: #111; margin: 0 0 4px 0; line-height: 1.3;">
                                    {{ $item->name }}
                                </p>
                                <p style="font-size: 13px; font-weight: 800; color: #6366f1; margin: 0;">
                                    ${{ number_format($item->price, 2) }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if($recommended->count() > 0)
        <div style="margin-top: 40px;">
            <h2 style="font-size: 18px; font-weight: 800; color: #111; margin: 0 0 4px 0;">
                You Might Also Like
            </h2>
            <p style="font-size: 13px; color: #aaa; margin: 0 0 16px 0;">
                Cheaper alternatives and products running low on stock
            </p>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;">
                @foreach($recommended as $item)
                    <a href="{{ route('market.show', $item) }}" style="text-decoration: none;">
                        <div style="background: white; border-radius: 12px; border: 1.5px solid #eee; overflow: hidden;">
                            <div style="position: relative;">
                                <img src="https://picsum.photos/seed/{{ $item->id }}/400/220"
                                     alt="{{ $item->name }}"
                                     style="width: 100%; height: 110px; object-fit: cover; display: block;" />

                                @if($item->stock > 0 && $item->stock <= 5)
                                    <span style="position: absolute; top: 6px; left: 6px; background: #ef4444; color: white; font-size: 9px; font-weight: 700; padding: 2px 7px; border-radius: 20px;">
                                        Only {{ $item->stock }} left!
                                    </span>
                                @elseif($item->price < $product->price)
                                    <span style="position: absolute; top: 6px; left: 6px; background: #10b981; color: white; font-size: 9px; font-weight: 700; padding: 2px 7px; border-radius: 20px;">
                                        Cheaper choice
                                    </span>
                                @endif
                            </div>

                            <div style="padding: 10px 12px;">
                                <p style="font-size: 12px; font-weight: 700; color: #111; margin: 0 0 2px 0;">
                                    {{ $item->name }}
                                </p>

                                <p style="font-size: 11px; color: #aaa; margin: 0 0 6px 0;">
                                    {{ $item->vendor->shop_name }}
                                </p>

                                @if($item->stock > 0 && $item->stock <= 5)
                                    <p style="font-size: 11px; color: #ef4444; font-weight: 700; margin: 0 0 4px 0;">
                                        Low stock
                                    </p>
                                @elseif($item->price < $product->price)
                                    <p style="font-size: 11px; color: #10b981; font-weight: 700; margin: 0 0 4px 0;">
                                        Save ${{ number_format($product->price - $item->price, 2) }}
                                    </p>
                                @endif

                                <p style="font-size: 13px; font-weight: 800; color: #111; margin: 0;">
                                    ${{ number_format($item->price, 2) }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
