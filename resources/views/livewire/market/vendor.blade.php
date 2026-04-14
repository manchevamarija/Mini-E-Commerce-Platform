<?php

use App\Domain\ProductCatalog\Models\Product;
use App\Domain\ProductCatalog\Models\Vendor;
use App\Domain\Cart\Actions\AddToCartAction;
use App\Domain\Cart\Models\Cart;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public Vendor $vendor;

    public function mount(Vendor $vendor): void
    {
        $this->vendor = $vendor;
    }

    public function addToCart(string $productId): void
    {
        if (!auth()->check()) {
            $this->redirect(route('login'));
            return;
        }

        $product = Product::findOrFail($productId);
        $user = auth()->user();

        $cart = $user->cart ?? Cart::create([
            'id' => \Illuminate\Support\Str::ulid(),
            'user_id' => $user->id,
        ]);

        try {
            app(AddToCartAction::class)->execute($cart, $product, 1);
            session()->flash('added_' . $productId, true);
        } catch (\RuntimeException $e) {
            session()->flash('error_' . $productId, $e->getMessage());
        }
    }

    public function with(): array
    {
        $products = Product::where('vendor_id', $this->vendor->id)
            ->active()
            ->paginate(12);

        return compact('products');
    }
}; ?>

<div style="font-family: 'Inter', sans-serif;">

    {{-- Vendor Header --}}
    <div style="background: #111; border-radius: 16px; padding: 32px 40px; margin-bottom: 24px; color: white; display: flex; align-items: center; gap: 20px;">
        @if($vendor->logo_url)
            <img src="{{ $vendor->logo_url }}" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid #333;" />
        @else
            <div style="width: 72px; height: 72px; border-radius: 50%; background: #333; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 800; color: white;">
                {{ substr($vendor->shop_name, 0, 1) }}
            </div>
        @endif
        <div>
            <h1 style="font-size: 28px; font-weight: 800; margin: 0 0 4px 0;">{{ $vendor->shop_name }}</h1>
            @if($vendor->description)
                <p style="font-size: 14px; color: #888; margin: 0;">{{ $vendor->description }}</p>
            @endif
            <p style="font-size: 12px; color: #666; margin: 8px 0 0 0;">{{ $products->total() }} products</p>
        </div>
    </div>

    {{-- Products Grid --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px;">
        @forelse($products as $product)
            <div style="background: white; border-radius: 14px; border: 1px solid #eee; overflow: hidden; display: flex; flex-direction: column;">
                <div style="position: relative;">
                    <a href="{{ route('market.show', $product) }}">
                        <img src="https://picsum.photos/seed/{{ $product->id }}/400/220"
                             style="width: 100%; height: 150px; object-fit: cover; display: block;" />
                    </a>
                    @if($product->stock === 0)
                        <span style="position: absolute; top: 8px; left: 8px; background: #111; color: white; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 20px;">Out of stock</span>
                    @elseif($product->stock <= 5)
                        <span style="position: absolute; top: 8px; left: 8px; background: #ef4444; color: white; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 20px;">Only {{ $product->stock }} left!</span>
                    @endif
                </div>
                <div style="padding: 12px; flex: 1; display: flex; flex-direction: column;">
                    <h3 style="font-size: 13px; font-weight: 700; color: #111; margin: 0 0 4px 0;">{{ $product->name }}</h3>
                    <p style="font-size: 11px; color: #aaa; margin: 0 0 10px 0;">{{ $product->stock }} in stock</p>
                    <div style="margin-top: auto;">
                        <p style="font-size: 16px; font-weight: 800; color: #111; margin: 0 0 10px 0;">${{ number_format($product->price, 2) }}</p>
                        @if(session('added_' . $product->id))
                            <p style="font-size: 11px; color: #16a34a; text-align: center; margin-bottom: 6px;">Added to cart!</p>
                        @endif
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('market.show', $product) }}"
                               style="flex: 1; text-align: center; border: 1.5px solid #6366f1; color: #6366f1; border-radius: 8px; padding: 7px 0; font-size: 12px; font-weight: 600; text-decoration: none;">
                                Details
                            </a>
                            <button wire:click="addToCart('{{ $product->id }}')"
                                    @if($product->stock === 0) disabled @endif
                                    style="flex: 1; background: {{ $product->stock === 0 ? '#ccc' : '#111' }}; color: white; border: none; border-radius: 8px; padding: 7px 0; font-size: 12px; font-weight: 600; cursor: {{ $product->stock === 0 ? 'not-allowed' : 'pointer' }};">
                                {{ $product->stock === 0 ? 'Out of Stock' : 'Add to Cart' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: span 4; text-align: center; padding: 80px 0; color: #aaa;">No products found.</div>
        @endforelse
    </div>

    <div style="margin-top: 32px;">{{ $products->links() }}</div>
</div>
