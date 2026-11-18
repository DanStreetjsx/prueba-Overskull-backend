<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and gadgets.'],
            ['name' => 'Books', 'description' => 'Various genres of books.'],
            ['name' => 'Clothing', 'description' => 'Apparel for men, women, and children.'],
            ['name' => 'Home & Kitchen', 'description' => 'Household items and kitchenware.'],
            ['name' => 'Sports & Outdoors', 'description' => 'Equipment and gear for sports and outdoor activities.'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
