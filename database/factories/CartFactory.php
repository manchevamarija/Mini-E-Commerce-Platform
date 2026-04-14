<?php

namespace Database\Factories;

use App\Domain\Cart\Models\Cart;
use App\Domain\IdentityAndAccess\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'id' => \Illuminate\Support\Str::ulid(),
            'user_id' => User::factory(),
        ];
    }
}
