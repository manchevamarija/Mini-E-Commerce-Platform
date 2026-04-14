<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


Route::get('/', function () {
    return redirect()->route('market.index');
});
// Marketplace - public
Volt::route('/market', 'market.index')->name('market.index');
Volt::route('/market/{product}', 'market.show')->name('market.show');

// Buyer routes
Route::middleware(['auth', 'role:buyer'])->group(function () {
    Volt::route('/cart', 'cart.index')->name('cart.index');
    Volt::route('/checkout', 'checkout.index')->name('checkout.index');
    Volt::route('/orders', 'buyer.orders.index')->name('buyer.orders.index');
    Volt::route('/orders/{order}', 'buyer.orders.show')->name('buyer.orders.show');
});

// Vendor routes
Route::middleware(['auth', 'role:vendor'])->group(function () {
    Volt::route('/vendor/products', 'vendor.products.index')->name('vendor.products.index');
    Volt::route('/vendor/products/create', 'vendor.products.create')->name('vendor.products.create');
    Volt::route('/vendor/products/{product}/edit', 'vendor.products.edit')->name('vendor.products.edit');
    Volt::route('/vendor/orders', 'vendor.orders.index')->name('vendor.orders.index');
});

Volt::route('/vendor/dashboard', 'vendor.dashboard')->name('vendor.dashboard');
Volt::route('/profile', 'profile.index')->name('profile');
require __DIR__.'/auth.php';
