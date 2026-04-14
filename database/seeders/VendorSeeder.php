<?php

namespace Database\Seeders;

use App\Domain\IdentityAndAccess\Enums\UserRole;
use App\Domain\IdentityAndAccess\Models\User;
use App\Domain\ProductCatalog\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            ['shop_name' => 'TechZone', 'description' => 'Best tech gadgets online'],
            ['shop_name' => 'HomeComfort', 'description' => 'Everything for your home'],
            ['shop_name' => 'SportsPro', 'description' => 'Professional sports equipment'],
            ['shop_name' => 'BookWorld', 'description' => 'Books for every reader'],
        ];

        foreach ($vendors as $data) {
            $user = User::factory()->create([
                'role' => UserRole::Vendor,
                'email' => strtolower(str_replace(' ', '', $data['shop_name'])) . '@example.com',
            ]);

            Vendor::create([
                'id' => \Illuminate\Support\Str::ulid(),
                'user_id' => $user->id,
                'shop_name' => $data['shop_name'],
                'description' => $data['description'],
            ]);
        }
    }
}
