<?php

use App\Domain\IdentityAndAccess\Enums\UserRole;
use App\Domain\IdentityAndAccess\Models\User;
use App\Domain\ProductCatalog\Models\Vendor;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $is_buyer = true;
    public bool $is_vendor = false;
    public string $shop_name = '';

    public function register(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'shop_name' => [$this->is_vendor ? 'required' : 'nullable', 'string', 'max:255'],
        ]);

        $role = $this->is_vendor ? UserRole::Vendor : UserRole::Buyer;

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $role,
            'is_buyer' => $this->is_buyer,
            'is_vendor' => $this->is_vendor,
        ]);

        if ($this->is_vendor) {
            Vendor::create([
                'id' => Str::ulid(),
                'user_id' => $user->id,
                'shop_name' => $this->shop_name,
                'description' => '',
            ]);
        }

        event(new Registered($user));
        Auth::login($user);

        if ($user->isVendor() && !$user->isBuyer()) {
            $this->redirect(route('vendor.products.index'), navigate: true);
        } else {
            $this->redirect(route('market.index'), navigate: true);
        }
    }
}; ?>

<div>
    <form wire:submit="register">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Roles --}}
        <div class="mt-4">
            <x-input-label :value="__('I want to (select one or both)')" />
            <div class="flex gap-4 mt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model.live="is_buyer" class="text-indigo-600" />
                    <span class="text-sm text-gray-700">Shop as Buyer</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model.live="is_vendor" class="text-indigo-600" />
                    <span class="text-sm text-gray-700">Sell as Vendor</span>
                </label>
            </div>
        </div>

        @if($is_vendor)
            <div class="mt-4">
                <x-input-label for="shop_name" :value="__('Shop Name')" />
                <x-text-input wire:model="shop_name" id="shop_name" class="block mt-1 w-full" type="text" />
                <x-input-error :messages="$errors->get('shop_name')" class="mt-2" />
            </div>
        @endif

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>
            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
