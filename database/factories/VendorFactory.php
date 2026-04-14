<?php

namespace Database\Factories;

use App\Domain\IdentityAndAccess\Enums\UserRole;
use App\Domain\IdentityAndAccess\Models\User;
use App\Domain\ProductCatalog\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        return [
            'id' => \Illuminate\Support\Str::ulid(),
            'user_id' => User::factory()->state(['role' => UserRole::Vendor]),
            'shop_name' => fake()->company(),
            'description' => fake()->paragraph(),
            'logo_url' => fake()->imageUrl(200, 200, 'business'),
        ];
    }
}
