<?php

use App\Domain\ProductCatalog\Models\Product;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function deleteProduct(string $id): void
    {
        $vendor = auth()->user()->vendor;
        $product = Product::where('vendor_id', $vendor->id)->findOrFail($id);
        $product->delete();
    }

    public function with(): array
    {
        $vendor = auth()->user()->vendor;
        $products = Product::forVendor($vendor)->latest()->paginate(10);
        return compact('products');
    }
}; ?>

<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">My Products</h1>
        <a href="{{ route('vendor.products.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Add Product
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-3 text-sm font-medium text-gray-600">Product</th>
                <th class="text-left px-4 py-3 text-sm font-medium text-gray-600">Price</th>
                <th class="text-left px-4 py-3 text-sm font-medium text-gray-600">Stock</th>
                <th class="text-left px-4 py-3 text-sm font-medium text-gray-600">Status</th>
                <th class="text-left px-4 py-3 text-sm font-medium text-gray-600">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ $product->name }}</td>
                    <td class="px-4 py-3">${{ number_format($product->price, 2) }}</td>
                    <td class="px-4 py-3">{{ $product->stock }}</td>
                    <td class="px-4 py-3">{{ ucfirst($product->status->value) }}</td>
                    <td class="px-4 py-3 flex gap-2">
                        <a href="{{ route('vendor.products.edit', $product) }}"
                           class="text-blue-600 hover:underline text-sm">Edit</a>
                        <button wire:click="deleteProduct('{{ $product->id }}')"
                                wire:confirm="Are you sure?"
                                class="text-red-500 hover:underline text-sm">Delete</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No products yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
</div>
