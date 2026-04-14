<?php

use App\Domain\Cart\Models\Cart;
use App\Domain\Cart\Models\CartItem;
use App\Domain\IdentityAndAccess\Enums\UserRole;
use App\Domain\IdentityAndAccess\Models\User;
use App\Domain\OrderManagement\Enums\PaymentMethod;
use App\Domain\OrderManagement\Services\CheckoutService;
use App\Domain\ProductCatalog\Models\Product;
use App\Domain\ProductCatalog\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('checkout succeeds and decrements stock', function () {
    $user = User::factory()->create(['role' => UserRole::Buyer]);
    $vendor = Vendor::factory()->create();
    $product = Product::factory()->create(['vendor_id' => $vendor->id, 'stock' => 10, 'price' => 50]);
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 2]);

    $order = app(CheckoutService::class)->checkout($cart, PaymentMethod::CreditCard);

    expect($order->status->value)->toBe('paid');
    expect($product->fresh()->stock)->toBe(8);
    expect($cart->items()->count())->toBe(0);
});

test('checkout fails when total exceeds 999', function () {
    $user = User::factory()->create(['role' => UserRole::Buyer]);
    $vendor = Vendor::factory()->create();
    $product = Product::factory()->create(['vendor_id' => $vendor->id, 'stock' => 10, 'price' => 500]);
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 3]);

    expect(fn() => app(CheckoutService::class)->checkout($cart, PaymentMethod::CreditCard))
        ->toThrow(\RuntimeException::class, 'Payment failed');

    expect($cart->items()->count())->toBe(1);
});

test('checkout fails when product has insufficient stock', function () {
    $user = User::factory()->create(['role' => UserRole::Buyer]);
    $vendor = Vendor::factory()->create();
    $product = Product::factory()->create(['vendor_id' => $vendor->id, 'stock' => 1, 'price' => 50]);
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 5]);

    expect(fn() => app(CheckoutService::class)->checkout($cart, PaymentMethod::CreditCard))
        ->toThrow(\RuntimeException::class);

    expect($cart->items()->count())->toBe(1);
});
