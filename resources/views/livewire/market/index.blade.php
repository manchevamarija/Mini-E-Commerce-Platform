<?php

use App\Domain\ProductCatalog\Models\Product;
use App\Domain\ProductCatalog\Models\Vendor;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $vendorId = '';
    public string $minPrice = '';
    public string $maxPrice = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedVendorId(): void { $this->resetPage(); }
    public function updatedMinPrice(): void { $this->resetPage(); }
    public function updatedMaxPrice(): void { $this->resetPage(); }

    public function with(): array
    {
        $products = Product::active()
            ->with('vendor')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->vendorId, fn($q) => $q->where('vendor_id', $this->vendorId))
            ->when($this->minPrice, fn($q) => $q->where('price', '>=', $this->minPrice))
            ->when($this->maxPrice, fn($q) => $q->where('price', '<=', $this->maxPrice))
            ->paginate(12);

        return [
            'products' => $products,
            'vendors' => Vendor::all(),
        ];
    }
}; ?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Marketplace</h1>

    {{-- Filters --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <input wire:model.live="search" type="text" placeholder="Search products..."
               class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />

        <select wire:model.live="vendorId" class="border rounded-lg px-4 py-2">
            <option value="">All Vendors</option>
            @foreach($vendors as $vendor)
                <option value="{{ $vendor->id }}">{{ $vendor->shop_name }}</option>
            @endforeach
        </select>

        <input wire:model.live="minPrice" type="number" placeholder="Min price"
               class="border rounded-lg px-4 py-2" />

        <input wire:model.live="maxPrice" type="number" placeholder="Max price"
               class="border rounded-lg px-4 py-2" />
    </div>

    {{-- Products Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-xl shadow hover:shadow-md transition p-4">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                     class="w-full h-48 object-cover rounded-lg mb-3" />
                <h3 class="font-semibold text-lg">{{ $product->name }}</h3>
                <p class="text-sm text-gray-500 mb-1">{{ $product->vendor->shop_name }}</p>
                <p class="text-blue-600 font-bold text-lg">${{ number_format($product->price, 2) }}</p>
                <p class="text-sm text-gray-400">Stock: {{ $product->stock }}</p>
                <a href="{{ route('market.show', $product) }}"
                   class="mt-3 block text-center bg-blue-600 text-white rounded-lg py-2 hover:bg-blue-700">
                    View Product
                </a>
            </div>
        @empty
            <p class="col-span-4 text-center text-gray-500">No products found.</p>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>
