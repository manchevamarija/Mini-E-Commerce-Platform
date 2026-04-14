<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Marketplace - public
Route::get('/market', \App\Livewire\Market\Index::class)->name('market.index');
Route::get('/market/{product}', \App\Livewire\Market\Show::class)->name('market.show');

// Buyer routes
Route::middleware(['auth', 'role:buyer'])->group(function () {
    Route::get('/cart', \App\Livewire\Cart\Index::class)->name('cart.index');
    Route::get('/checkout', \App\Livewire\Checkout\Index::class)->name('checkout.index');
    Route::get('/orders', \App\Livewire\Buyer\Orders\Index::class)->name('buyer.orders.index');
    Route::get('/orders/{order}', \App\Livewire\Buyer\Orders\Show::class)->name('buyer.orders.show');
});

// Vendor routes
Route::middleware(['auth', 'role:vendor'])->group(function () {
    Route::get('/vendor/products', \App\Livewire\Vendor\Products\Index::class)->name('vendor.products.index');
    Route::get('/vendor/products/create', \App\Livewire\Vendor\Products\Create::class)->name('vendor.products.create');
    Route::get('/vendor/products/{product}/edit', \App\Livewire\Vendor\Products\Edit::class)->name('vendor.products.edit');
    Route::get('/vendor/orders', \App\Livewire\Vendor\Orders\Index::class)->name('vendor.orders.index');
});

require __DIR__.'/auth.php';
require __DIR__.'/auth.php';
