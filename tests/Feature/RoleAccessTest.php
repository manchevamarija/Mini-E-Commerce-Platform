<?php

use App\Domain\IdentityAndAccess\Enums\UserRole;
use App\Domain\IdentityAndAccess\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot access cart', function () {
    $response = $this->get('/cart');
    $response->assertRedirect('/login');
});

test('guest cannot access checkout', function () {
    $response = $this->get('/checkout');
    $response->assertRedirect('/login');
});

test('buyer cannot access vendor products page', function () {
    $user = User::factory()->create(['role' => UserRole::Buyer]);
    $response = $this->actingAs($user)->get('/vendor/products');
    $response->assertStatus(403);
});

test('vendor cannot access buyer cart', function () {
    $user = User::factory()->create(['role' => UserRole::Vendor]);
    $response = $this->actingAs($user)->get('/cart');
    $response->assertStatus(403);
});
