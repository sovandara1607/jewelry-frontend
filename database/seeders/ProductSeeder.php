<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product; // Make sure to import your Product model

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'product_name' => 'Ronda Earrings',
            'product_category' => 'earrings',
            'product_price' => 135.00,
            'product_images' => '/images/earrings-ronda-silver.jpg',
            'shop_id' => 1,
        ]);
        Product::create([
            'product_name' => 'Bumble Bee Brooch',
            'product_category' => 'brooch',
            'product_price' => 125.00,
            'product_images' => '/images/brooch-bee.jpg',
            'shop_id' => 1,
        ]);
    }
}
