<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding products...');

        // Create a variety of products for comprehensive testing
        
        // Electronics - 25 products
        $this->command->info('📱 Creating Electronics products...');
        Product::factory()
            ->count(20)
            ->electronics()
            ->create();

        // Create some specific high-value electronics for testing constraints
        Product::factory()
            ->count(5)
            ->electronics()
            ->highValue()
            ->create();

        // Clothing - 20 products
        $this->command->info('👕 Creating Clothing products...');
        Product::factory()
            ->count(20)
            ->state(['category' => 'Clothing'])
            ->create();

        // Books - 15 products
        $this->command->info('📚 Creating Books products...');
        Product::factory()
            ->count(15)
            ->state(['category' => 'Books'])
            ->create();

        // Home & Garden - 15 products
        $this->command->info('🏠 Creating Home & Garden products...');
        Product::factory()
            ->count(15)
            ->state(['category' => 'Home & Garden'])
            ->create();

        // Sports - 12 products
        $this->command->info('⚽ Creating Sports products...');
        Product::factory()
            ->count(12)
            ->state(['category' => 'Sports'])
            ->create();

        // Other categories - 13 products
        $this->command->info('🎯 Creating other category products...');
        Product::factory()
            ->count(5)
            ->state(['category' => 'Toys'])
            ->create();

        Product::factory()
            ->count(4)
            ->state(['category' => 'Health & Beauty'])
            ->create();

        Product::factory()
            ->count(4)
            ->state(['category' => 'Office Supplies'])
            ->create();

        // Create some low stock products for testing alerts
        $this->command->info('⚠️ Creating low stock products...');
        Product::factory()
            ->count(8)
            ->lowStock()
            ->create();

        // Create some out of stock products
        $this->command->info('❌ Creating out of stock products...');
        Product::factory()
            ->count(6)
            ->outOfStock()
            ->create();

        // Create some specific test products with known data
        $this->createSpecificTestProducts();

        $totalProducts = Product::count();
        $this->command->info("✅ Successfully created {$totalProducts} products!");
        
        // Display statistics
        $this->displayStatistics();
    }

    /**
     * Create specific test products for stakeholder testing
     */
    private function createSpecificTestProducts(): void
    {
        $this->command->info('🧪 Creating specific test products...');

        $testProducts = [
            [
                'name' => 'MacBook Pro 16" M2',
                'quantity' => 15,
                'category' => 'Electronics',
                'description' => 'Apple MacBook Pro with M2 Pro chip, 16GB RAM, 512GB SSD. Perfect for professional development and creative work.',
                'price' => 2499.99,
                'status' => 'in_stock'
            ],
            [
                'name' => 'Wireless Gaming Mouse',
                'quantity' => 3,
                'category' => 'Electronics',
                'description' => 'High-precision wireless gaming mouse with RGB lighting and customizable buttons.',
                'price' => 79.99,
                'status' => 'low_stock'
            ],
            [
                'name' => 'Professional Office Chair',
                'quantity' => 0,
                'category' => 'Office Supplies',
                'description' => 'Ergonomic office chair with lumbar support and adjustable height.',
                'price' => 299.99,
                'status' => 'ordered'
            ],
            [
                'name' => 'Vintage Leather Jacket',
                'quantity' => 8,
                'category' => 'Clothing',
                'description' => 'Premium genuine leather jacket with classic vintage styling.',
                'price' => 189.99,
                'status' => 'in_stock'
            ],
            [
                'name' => 'Smart Home Security Camera',
                'quantity' => 25,
                'category' => 'Electronics',
                'description' => '4K security camera with night vision, motion detection, and cloud storage.',
                'price' => 149.99,
                'status' => 'in_stock'
            ],
            [
                'name' => 'Organic Coffee Beans',
                'quantity' => 2,
                'category' => 'Food & Beverages',
                'description' => 'Premium organic coffee beans from sustainable farms.',
                'price' => 24.99,
                'status' => 'low_stock'
            ],
            [
                'name' => 'Fitness Resistance Bands Set',
                'quantity' => 45,
                'category' => 'Sports',
                'description' => 'Complete set of resistance bands with various resistance levels for home workouts.',
                'price' => 39.99,
                'status' => 'in_stock'
            ],
            [
                'name' => 'Discontinued Gaming Console',
                'quantity' => 0,
                'category' => 'Electronics',
                'description' => 'Legacy gaming console no longer in production.',
                'price' => 199.99,
                'status' => 'discontinued'
            ]
        ];

        foreach ($testProducts as $product) {
            Product::create($product);
        }
    }

    /**
     * Display seeding statistics
     */
    private function displayStatistics(): void
    {
        $this->command->info('📊 Product Statistics:');
        
        $stats = [
            'Total Products' => Product::count(),
            'In Stock' => Product::where('status', 'in_stock')->count(),
            'Low Stock' => Product::where('status', 'low_stock')->count(),
            'Ordered' => Product::where('status', 'ordered')->count(),
            'Discontinued' => Product::where('status', 'discontinued')->count(),
        ];

        foreach ($stats as $label => $count) {
            $this->command->info("  {$label}: {$count}");
        }

        $this->command->info('📋 Categories:');
        $categories = Product::distinct()->pluck('category');
        foreach ($categories as $category) {
            $count = Product::where('category', $category)->count();
            $this->command->info("  {$category}: {$count} products");
        }

        $totalValue = Product::sum(DB::raw('price * quantity'));
        $this->command->info("💰 Total Inventory Value: $" . number_format($totalValue, 2));
    }
}
