<?php

namespace Database\Factories;

use App\Domain\ProductCatalog\Enums\ProductStatus;
use App\Domain\ProductCatalog\Models\Product;
use App\Domain\ProductCatalog\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'id' => \Illuminate\Support\Str::ulid(),
            'vendor_id' => VendorFactory::new()->create()->id,
            'name' => fake()->randomElement([
                'Wireless Headphones', 'Mechanical Keyboard', 'USB-C Hub',
                'Standing Desk', 'Webcam HD', 'Monitor Light Bar',
                'Laptop Stand', 'Mouse Pad XL', 'Cable Management Kit',
                'Ergonomic Chair', 'Smart Lamp', 'Portable SSD',
            ]),
            'description' => fake()->paragraph(3),
            'price' => fake()->randomFloat(2, 5, 500),
            'stock' => fake()->numberBetween(0, 100),
            'image_url' => fake()->imageUrl(400, 400, 'technics'),
            'status' => ProductStatus::Active,
        ];
    }
}
