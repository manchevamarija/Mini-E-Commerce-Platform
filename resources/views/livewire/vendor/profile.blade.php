<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $shop_name = '';
    public string $description = '';
    public string $logo_url = '';
    public bool $saved = false;

    public function mount(): void
    {
        $vendor = auth()->user()->vendor;
        if (!$vendor) abort(403);

        $this->shop_name = $vendor->shop_name;
        $this->description = $vendor->description ?? '';
        $this->logo_url = $vendor->logo_url ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'shop_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'logo_url' => 'nullable|url',
        ]);

        auth()->user()->vendor->update([
            'shop_name' => $this->shop_name,
            'description' => $this->description,
            'logo_url' => $this->logo_url,
        ]);

        $this->saved = true;
    }
}; ?>

<div style="font-family: 'Inter', sans-serif; max-width: 600px; margin: 0 auto;">

    <h1 style="font-size: 26px; font-weight: 800; color: #111; margin: 0 0 24px 0;">Shop Profile</h1>

    <div style="background: white; border-radius: 16px; border: 1px solid #ebebeb; padding: 32px;">

        @if($saved)
            <div style="background: #dcfce7; color: #16a34a; padding: 12px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; margin-bottom: 20px;">
                Shop profile updated successfully!
            </div>
        @endif

        <div style="margin-bottom: 20px;">
            <label style="font-size: 13px; font-weight: 600; color: #333; display: block; margin-bottom: 6px;">Shop Name</label>
            <input wire:model="shop_name" type="text"
                   style="width: 100%; border: 1px solid #e5e5e5; border-radius: 10px; padding: 10px 14px; font-size: 14px; outline: none;" />
            @error('shop_name') <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label style="font-size: 13px; font-weight: 600; color: #333; display: block; margin-bottom: 6px;">Description</label>
            <textarea wire:model="description" rows="4"
                      style="width: 100%; border: 1px solid #e5e5e5; border-radius: 10px; padding: 10px 14px; font-size: 14px; outline: none; resize: vertical;"></textarea>
            @error('description') <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p> @enderror
        </div>

        <div style="margin-bottom: 24px;">
            <label style="font-size: 13px; font-weight: 600; color: #333; display: block; margin-bottom: 6px;">Logo URL (optional)</label>
            <input wire:model.live="logo_url" type="url"
                   style="width: 100%; border: 1px solid #e5e5e5; border-radius: 10px; padding: 10px 14px; font-size: 14px; outline: none;" />
            @if($logo_url)
                <img src="{{ $logo_url }}" alt="Logo preview"
                     style="margin-top: 10px; width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 1px solid #eee;" />
            @endif
        </div>

        <button wire:click="save"
                style="background: #111; color: white; border: none; border-radius: 10px; padding: 12px 28px; font-size: 14px; font-weight: 700; cursor: pointer;">
            Save Changes
        </button>
    </div>
</div>
