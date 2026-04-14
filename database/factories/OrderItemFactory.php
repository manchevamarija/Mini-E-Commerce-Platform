<?php

namespace Database\Factories;

use App\Domain\OrderManagement\Models\Order;
use App\Domain\OrderManagement\Models\OrderItem;
use App\Domain\ProductCatalog\Models\Product;
use App\Domain\ProductCatalog\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $product = Product::factory()->create();

        return [
            'id' => \Illuminate\Support\Str::ulid(),
            'order_id' => Order::factory(),
            'product_id' => $product->id,
            'vendor_id' => $product->vendor_id,
            'quantity' => fake()->numberBetween(1, 5),
            'price' => $product->price,
        ];
    }
}
