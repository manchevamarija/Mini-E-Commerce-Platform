<?php

use App\Domain\ProductCatalog\Actions\UpdateProductAction;
use App\Domain\ProductCatalog\Models\Product;
use Livewire\Volt\Component;

new class extends Component {
    public Product $product;
    public string $name = '';
    public string $description = '';
    public string $price = '';
    public string $stock = '';
    public string $imageUrl = '';

    public function mount(Product $product): void
    {
        $vendor = auth()->user()->vendor;
        if ($product->vendor_id !== $vendor->id) {
            abort(403);
        }

        $this->product = $product;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->imageUrl = $product->image_url ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
            'imageUrl' => 'nullable|url',
        ]);

        app(UpdateProductAction::class)->execute($this->product, [
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'stock' => (int) $this->stock,
            'image_url' => $this->imageUrl ?: null,
        ]);

        $this->redirect(route('vendor.products.index'));
    }
}; ?>

<div class="max-w-2xl mx-auto px-4 py-8">
    <a href="{{ route('vendor.products.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">← Back</a>
    <h1 class="text-3xl font-bold mb-6">Edit Product</h1>

    <div class="bg-white rounded-xl shadow p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Name</label>
            <input wire:model="name" type="text" class="w-full border rounded-lg px-4 py-2" />
            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea wire:model="description" rows="4" class="w-full border rounded-lg px-4 py-2"></textarea>
            @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Price ($)</label>
                <input wire:model="price" type="number" step="0.01" class="w-full border rounded-lg px-4 py-2" />
                @error('price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Stock</label>
                <input wire:model="stock" type="number" class="w-full border rounded-lg px-4 py-2" />
                @error('stock') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-1">Image URL (optional)</label>
            <input wire:model="imageUrl" type="url" class="w-full border rounded-lg px-4 py-2" />
            @error('imageUrl') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <button wire:click="save"
                class="w-full bg-blue-600 text-white rounded-lg py-3 font-semibold hover:bg-blue-700">
            Update Product
        </button>
    </div>
</div>
