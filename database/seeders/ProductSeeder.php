<?php

namespace Database\Seeders;

use App\Domain\ProductCatalog\Enums\ProductStatus;
use App\Domain\ProductCatalog\Models\Product;
use App\Domain\ProductCatalog\Models\Vendor;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            'TechZone' => [
                ['name' => 'Wireless Headphones', 'price' => 79.99, 'stock' => 50],
                ['name' => 'Mechanical Keyboard', 'price' => 129.99, 'stock' => 30],
                ['name' => 'USB-C Hub', 'price' => 49.99, 'stock' => 100],
                ['name' => 'Webcam HD 1080p', 'price' => 89.99, 'stock' => 25],
                ['name' => 'Portable SSD 1TB', 'price' => 109.99, 'stock' => 40],
            ],
            'HomeComfort' => [
                ['name' => 'Standing Desk', 'price' => 349.99, 'stock' => 15],
                ['name' => 'Ergonomic Chair', 'price' => 299.99, 'stock' => 10],
                ['name' => 'Smart Lamp', 'price' => 39.99, 'stock' => 60],
                ['name' => 'Monitor Light Bar', 'price' => 59.99, 'stock' => 45],
                ['name' => 'Cable Management Kit', 'price' => 19.99, 'stock' => 80],
            ],
            'SportsPro' => [
                ['name' => 'Yoga Mat Premium', 'price' => 34.99, 'stock' => 70],
                ['name' => 'Resistance Bands Set', 'price' => 24.99, 'stock' => 90],
                ['name' => 'Water Bottle 1L', 'price' => 14.99, 'stock' => 120],
                ['name' => 'Running Shoes', 'price' => 89.99, 'stock' => 35],
                ['name' => 'Gym Gloves', 'price' => 19.99, 'stock' => 55],
            ],
            'BookWorld' => [
                ['name' => 'Clean Code', 'price' => 29.99, 'stock' => 40],
                ['name' => 'The Pragmatic Programmer', 'price' => 34.99, 'stock' => 30],
                ['name' => 'Design Patterns', 'price' => 39.99, 'stock' => 25],
                ['name' => 'Laravel Up & Running', 'price' => 44.99, 'stock' => 20],
                ['name' => 'Domain-Driven Design', 'price' => 49.99, 'stock' => 15],
            ],
        ];

        foreach ($products as $shopName => $items) {
            $vendor = Vendor::whereHas('user', function ($q) use ($shopName) {
                $q->where('email', strtolower(str_replace(' ', '', $shopName)) . '@example.com');
            })->first();

            if (!$vendor) continue;

            foreach ($items as $item) {
                Product::create([
                    'id' => \Illuminate\Support\Str::ulid(),
                    'vendor_id' => $vendor->id,
                    'name' => $item['name'],
                    'description' => fake()->paragraph(2),
                    'price' => $item['price'],
                    'stock' => $item['stock'],
                    'image_url' => 'https://picsum.photos/seed/' . urlencode($item['name']) . '/400/400',
                    'status' => ProductStatus::Active,
                ]);
            }
        }
    }
}
