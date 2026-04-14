<?php

namespace Database\Factories;

use App\Domain\IdentityAndAccess\Models\User;
use App\Domain\OrderManagement\Enums\OrderStatus;
use App\Domain\OrderManagement\Enums\PaymentMethod;
use App\Domain\OrderManagement\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'id' => \Illuminate\Support\Str::ulid(),
            'user_id' => User::factory(),
            'status' => OrderStatus::Pending,
            'payment_method' => fake()->randomElement(PaymentMethod::cases()),
            'total' => fake()->randomFloat(2, 10, 800),
        ];
    }
}
