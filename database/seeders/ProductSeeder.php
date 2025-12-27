<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Product; // Make sure to import your Product model

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Ronda Earrings',
            'category' => 'earrings',
            'price' => 135.00,
            'product_images' => '/images/earrings-ronda-silver.jpg',
            // Add any other required fields like shop_id if necessary
        ]);
        Product::create([
            'name' => 'Bumble Bee Brooch',
            'category' => 'brooch',
            'price' => 125.00,
            'product_images' => '/images/brooch-bee.jpg',
        ]);
        // Add a few more products...
    }
}