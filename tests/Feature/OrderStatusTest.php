<?php

use App\Domain\IdentityAndAccess\Enums\UserRole;
use App\Domain\IdentityAndAccess\Models\User;
use App\Domain\OrderManagement\Actions\UpdateOrderStatusAction;
use App\Domain\OrderManagement\Enums\OrderStatus;
use App\Domain\OrderManagement\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('order can transition from pending to paid', function () {
    $user = User::factory()->create(['role' => UserRole::Buyer]);
    $order = Order::factory()->create(['user_id' => $user->id, 'status' => OrderStatus::Pending]);

    $updated = app(UpdateOrderStatusAction::class)->execute($order, OrderStatus::Paid);

    expect($updated->status)->toBe(OrderStatus::Paid);
});

test('order can transition from paid to shipped', function () {
    $user = User::factory()->create(['role' => UserRole::Buyer]);
    $order = Order::factory()->create(['user_id' => $user->id, 'status' => OrderStatus::Paid]);

    $updated = app(UpdateOrderStatusAction::class)->execute($order, OrderStatus::Shipped);

    expect($updated->status)->toBe(OrderStatus::Shipped);
});

test('order cannot skip status transition', function () {
    $user = User::factory()->create(['role' => UserRole::Buyer]);
    $order = Order::factory()->create(['user_id' => $user->id, 'status' => OrderStatus::Pending]);

    expect(fn() => app(UpdateOrderStatusAction::class)->execute($order, OrderStatus::Shipped))
        ->toThrow(\RuntimeException::class);
});

test('delivered order cannot transition further', function () {
    $user = User::factory()->create(['role' => UserRole::Buyer]);
    $order = Order::factory()->create(['user_id' => $user->id, 'status' => OrderStatus::Delivered]);

    expect(fn() => app(UpdateOrderStatusAction::class)->execute($order, OrderStatus::Paid))
        ->toThrow(\RuntimeException::class);
});
