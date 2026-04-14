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
    public string $role = 'buyer';
    public string $shop_name = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:buyer,vendor'],
            'shop_name' => [$this->role === 'vendor' ? 'required' : 'nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::from($validated['role']),
        ]);

        if ($validated['role'] === 'vendor') {
            Vendor::create([
                'id' => Str::ulid(),
                'user_id' => $user->id,
                'shop_name' => $validated['shop_name'],
                'description' => '',
            ]);
        }

        event(new Registered($user));
        Auth::login($user);

        if ($user->isVendor()) {
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

        {{-- Role --}}
        <div class="mt-4">
            <x-input-label :value="__('I want to')" />
            <div class="flex gap-4 mt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" wire:model.live="role" value="buyer" class="text-indigo-600" />
                    <span class="text-sm text-gray-700">Shop as Buyer</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" wire:model.live="role" value="vendor" class="text-indigo-600" />
                    <span class="text-sm text-gray-700">Sell as Vendor</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        {{-- Shop name (only for vendor) --}}
        @if($role === 'vendor')
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
