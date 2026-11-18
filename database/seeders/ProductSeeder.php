<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $products = [
            ['name' => 'Smartphone', 'description' => 'Latest model smartphone with advanced features.', 'price' => 699.99, 'stock' => 50, 'category_id' => 1],
            ['name' => 'Laptop', 'description' => 'High-performance laptop for work and gaming.', 'price' => 1299.99, 'stock' => 30, 'category_id' => 1],
            ['name' => 'Novel', 'description' => 'A captivating novel by a renowned author.', 'price' => 19.99, 'stock' => 100, 'category_id' => 2],
            ['name' => 'T-shirt', 'description' => 'Comfortable cotton t-shirt available in various sizes.', 'price' => 14.99, 'stock' => 200, 'category_id' => 3],
            ['name' => 'Blender', 'description' => 'Powerful blender for smoothies and food preparation.', 'price' => 49.99, 'stock' => 40, 'category_id' => 4],
            ['name' => 'Tent', 'description' => 'Durable tent suitable for camping and outdoor adventures.', 'price' => 89.99, 'stock' => 25, 'category_id' => 5],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['name' => $product['name'], 'category_id' => $product['category_id']],
                [
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                ]
            );
        }
    }
}
