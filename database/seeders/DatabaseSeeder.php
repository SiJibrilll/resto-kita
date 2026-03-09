<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\Table;
use App\Models\TableSession;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $categories = [
            [
                'name' => 'Appetizers',
                'description' => 'Starters served before the main course'
            ],
            [
                'name' => 'Main Courses',
                'description' => 'Primary dishes of the restaurant'
            ],
            [
                'name' => 'Desserts',
                'description' => 'Sweet dishes served after meals'
            ],
            [
                'name' => 'Beverages',
                'description' => 'Hot and cold drinks'
            ],
            [
                'name' => 'Coffee & Tea',
                'description' => 'Freshly brewed coffee and tea selections'
            ],
            [
                'name' => 'Juices & Smoothies',
                'description' => 'Fresh fruit juices and blended drinks'
            ],
            [
                'name' => 'Special Menu',
                'description' => 'Seasonal or promotional dishes'
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']], 
                ['description' => $category['description']]
            );
        }

        $items = [
            // Appetizers
            [
                'name' => 'Spring Rolls',
                'description' => 'Crispy rolls stuffed with vegetables',
                'price' => 25000,
                'img' => 'spring-rolls.jpg',
                'is_active' => true,
                'category' => 'Appetizers',
            ],
            [
                'name' => 'Garlic Bread',
                'description' => 'Toasted bread with garlic butter',
                'price' => 20000,
                'img' => 'garlic-bread.jpg',
                'is_active' => true,
                'category' => 'Appetizers',
            ],

            // Main Courses
            [
                'name' => 'Grilled Chicken',
                'description' => 'Grilled chicken served with rice and vegetables',
                'price' => 55000,
                'img' => 'grilled-chicken.jpg',
                'is_active' => true,
                'category' => 'Main Courses',
            ],
            [
                'name' => 'Beef Steak',
                'description' => 'Juicy beef steak with black pepper sauce',
                'price' => 85000,
                'img' => 'beef-steak.jpg',
                'is_active' => true,
                'category' => 'Main Courses',
            ],

            // Desserts
            [
                'name' => 'Chocolate Lava Cake',
                'description' => 'Warm chocolate cake with molten center',
                'price' => 35000,
                'img' => 'lava-cake.jpg',
                'is_active' => true,
                'category' => 'Desserts',
            ],

            // Beverages
            [
                'name' => 'Iced Tea',
                'description' => 'Freshly brewed iced tea',
                'price' => 15000,
                'img' => 'iced-tea.jpg',
                'is_active' => true,
                'category' => 'Beverages',
            ],
            [
                'name' => 'Cappuccino',
                'description' => 'Espresso with steamed milk foam',
                'price' => 30000,
                'img' => 'cappuccino.jpg',
                'is_active' => true,
                'category' => 'Coffee & Tea',
            ],
        ];

        foreach ($items as $itemData) {

            $category = Category::where('name', $itemData['category'])->first();

            if ($category) {
                Item::firstOrCreate(
                    ['name' => $itemData['name']],
                    [
                        'description' => $itemData['description'],
                        'price' => $itemData['price'],
                        'img' => $itemData['img'],
                        'is_active' => $itemData['is_active'],
                        'category_id' => $category->id,
                    ]
                );
            }
        }

        $table = Table::create([
            'number' => 1,
        ]);

        $tableSession = TableSession::create([
            'table_id' => $table->id,
            'token' => 'abc',
            'seated_at' => now()
        ]);

        $tableSession->orders()->create();

        $order = Order::first();

        $order->items()->create([
            'item_id' => 1,
            'amount' => 2
        ]);
    }
}
