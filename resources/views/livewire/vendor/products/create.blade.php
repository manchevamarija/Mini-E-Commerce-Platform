<?php

use App\Domain\ProductCatalog\Actions\CreateProductAction;
use App\Domain\ProductCatalog\DTOs\CreateProductDTO;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $description = '';
    public string $price = '';
    public string $stock = '';
    public string $imageUrl = '';

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
            'imageUrl' => 'nullable|url',
        ]);

        $vendor = auth()->user()->vendor;

        $dto = new CreateProductDTO(
            vendorId: $vendor->id,
            name: $this->name,
            description: $this->description,
            price: (float) $this->price,
            stock: (int) $this->stock,
            imageUrl: $this->imageUrl ?: null,
        );

        app(CreateProductAction::class)->execute($dto);

        $this->redirect(route('vendor.products.index'));
    }
}; ?>

<div class="max-w-2xl mx-auto px-4 py-8">
    <a href="{{ route('vendor.products.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">← Back</a>
    <h1 class="text-3xl font-bold mb-6">Add New Product</h1>

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
            <input wire:model.live="imageUrl" type="url" class="w-full border rounded-lg px-4 py-2" />
            @error('imageUrl') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            @if($imageUrl)
                <div style="margin-top: 12px;">
                    <p style="font-size: 12px; color: #999; margin-bottom: 6px;">Preview:</p>
                    <img src="{{ $imageUrl }}" alt="Preview"
                         style="width: 200px; height: 150px; object-fit: cover; border-radius: 10px; border: 1px solid #eee;" />
                </div>
            @endif
        </div>
        <button wire:click="save"
                class="w-full bg-blue-600 text-white rounded-lg py-3 font-semibold hover:bg-blue-700">
            Create Product
        </button>
    </div>
</div>
