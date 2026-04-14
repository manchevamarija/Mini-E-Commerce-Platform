<?php

use App\Domain\ProductCatalog\Models\Product;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void { $this->resetPage(); }

    public function deleteProduct(string $id): void
    {
        $vendor = auth()->user()->vendor;
        $product = Product::where('vendor_id', $vendor->id)->findOrFail($id);
        $product->delete();
    }

    public function with(): array
    {
        $vendor = auth()->user()->vendor;
        $products = Product::forVendor($vendor)
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);
        return compact('products');
    }
}; ?>

<div style="font-family: 'Inter', sans-serif; max-width: 1000px; margin: 0 auto;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="font-size: 26px; font-weight: 800; color: #111; margin: 0;">My Products</h1>
        <a href="{{ route('vendor.products.create') }}"
           style="background: #111; color: white; padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none;">
            + Add Product
        </a>
    </div>

    {{-- Search --}}
    <div style="margin-bottom: 16px;">
        <input wire:model.live="search" type="text" placeholder="Search products..."
               style="border: 1px solid #e5e5e5; border-radius: 10px; padding: 9px 14px; font-size: 13px; outline: none; width: 300px;" />
    </div>

    <div style="background: white; border-radius: 16px; border: 1px solid #ebebeb; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
            <tr style="background: #fafafa;">
                <th style="padding: 12px 20px; text-align: left; font-size: 12px; color: #999; font-weight: 600;">Product</th>
                <th style="padding: 12px 20px; text-align: left; font-size: 12px; color: #999; font-weight: 600;">Price</th>
                <th style="padding: 12px 20px; text-align: left; font-size: 12px; color: #999; font-weight: 600;">Stock</th>
                <th style="padding: 12px 20px; text-align: left; font-size: 12px; color: #999; font-weight: 600;">Status</th>
                <th style="padding: 12px 20px; text-align: left; font-size: 12px; color: #999; font-weight: 600;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                <tr style="border-top: 1px solid #f5f5f5;">
                    <td style="padding: 14px 20px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <img src="{{ $product->image_url }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;" />
                            <span style="font-size: 13px; font-weight: 600; color: #111;">{{ $product->name }}</span>
                        </div>
                    </td>
                    <td style="padding: 14px 20px; font-size: 13px; font-weight: 700; color: #111;">${{ number_format($product->price, 2) }}</td>
                    <td style="padding: 14px 20px;">
                            <span style="font-size: 13px; color: {{ $product->stock < 5 ? '#ef4444' : '#111' }}; font-weight: {{ $product->stock < 5 ? '700' : '400' }};">
                                {{ $product->stock }}
                                @if($product->stock < 5)  Low @endif
                            </span>
                    </td>
                    <td style="padding: 14px 20px;">
                            <span style="font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase;
                                background: {{ $product->status->value === 'active' ? '#dcfce7' : '#f3f4f6' }};
                                color: {{ $product->status->value === 'active' ? '#16a34a' : '#6b7280' }};">
                                {{ ucfirst($product->status->value) }}
                            </span>
                    </td>
                    <td style="padding: 14px 20px;">
                        <div style="display: flex; gap: 12px;">
                            <a href="{{ route('vendor.products.edit', $product) }}"
                               style="font-size: 13px; font-weight: 600; color: #6366f1; text-decoration: none;">Edit</a>
                            <button wire:click="deleteProduct('{{ $product->id }}')"
                                    wire:confirm="Are you sure?"
                                    style="font-size: 13px; font-weight: 600; color: #ef4444; background: none; border: none; cursor: pointer; padding: 0;">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding: 40px; text-align: center; color: #aaa; font-size: 14px;">
                        No products yet.
                        <a href="{{ route('vendor.products.create') }}" style="color: #6366f1; font-weight: 600;">Add your first product →</a>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 16px;">
        {{ $products->links() }}
    </div>
</div>
