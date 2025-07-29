<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Electronics', 'Clothing', 'Books', 'Home & Garden', 'Sports', 
            'Toys', 'Health & Beauty', 'Automotive', 'Office Supplies', 'Food & Beverages'
        ];

        $electronicsProducts = [
            'Wireless Mouse', 'Mechanical Keyboard', 'USB-C Cable', 'Bluetooth Headphones',
            'Smartphone Charger', 'Laptop Stand', 'External Hard Drive', 'Webcam',
            'Tablet Screen Protector', 'Gaming Controller'
        ];

        $clothingProducts = [
            'Cotton T-Shirt', 'Denim Jeans', 'Hoodie Sweatshirt', 'Running Shoes',
            'Baseball Cap', 'Winter Jacket', 'Polo Shirt', 'Sneakers',
            'Leather Belt', 'Casual Dress'
        ];

        $booksProducts = [
            'Programming Guide', 'Business Strategy Book', 'Cooking Recipes',
            'Science Fiction Novel', 'History Textbook', 'Art Handbook',
            'Language Learning Book', 'Self-Help Guide', 'Travel Guide', 'Technical Manual'
        ];

        $homeGardenProducts = [
            'Indoor Plant Pot', 'LED Desk Lamp', 'Storage Organizer',
            'Kitchen Utensil Set', 'Decorative Pillow', 'Garden Tools',
            'Wall Clock', 'Picture Frame', 'Candle Set', 'Flower Vase'
        ];

        $sportsProducts = [
            'Yoga Mat', 'Dumbbell Set', 'Tennis Racket', 'Basketball',
            'Water Bottle', 'Fitness Tracker', 'Swimming Goggles',
            'Running Shorts', 'Gym Towel', 'Protein Shaker'
        ];

        $allProducts = [
            'Electronics' => $electronicsProducts,
            'Clothing' => $clothingProducts,
            'Books' => $booksProducts,
            'Home & Garden' => $homeGardenProducts,
            'Sports' => $sportsProducts,
        ];

        $category = $this->faker->randomElement($categories);
        
        // Get product name based on category, or use a generic one
        if (isset($allProducts[$category])) {
            $productName = $this->faker->randomElement($allProducts[$category]);
        } else {
            $productName = $this->faker->words(2, true) . ' ' . $category . ' Item';
        }

        $quantity = $this->faker->numberBetween(0, 200);
        $price = $this->faker->randomFloat(2, 5, 2000);

        // Determine status based on quantity
        $status = 'in_stock';
        if ($quantity <= 5) {
            $status = 'low_stock';
        } elseif ($quantity == 0) {
            $status = $this->faker->randomElement(['ordered', 'discontinued']);
        }

        $imageUrls = [
            'Electronics' => [
                'https://images.unsplash.com/photo-1517336714731-489689fd1ca8',
                'https://images.unsplash.com/photo-1519389950473-47ba0277781c',
                'https://images.unsplash.com/photo-1465101046530-73398c7f28ca',
                'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9',
                'https://images.unsplash.com/photo-1519125323398-675f0ddb6308',
            ],
            'Clothing' => [
                'https://images.unsplash.com/photo-1512436991641-6745cdb1723f',
                'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e',
                'https://images.unsplash.com/photo-1517841905240-472988babdf9',
                'https://images.unsplash.com/photo-1465101178521-c1a9136a3c8b',
                'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c',
            ],
            'Books' => [
                'https://images.unsplash.com/photo-1512820790803-83ca734da794',
                'https://images.unsplash.com/photo-1465101046530-73398c7f28ca',
                'https://images.unsplash.com/photo-1516979187457-637abb4f9353',
                'https://images.unsplash.com/photo-1519681393784-d120267933ba',
                'https://images.unsplash.com/photo-1509021436665-8f07dbf5bf1d',
            ],
            'Home & Garden' => [
                'https://images.unsplash.com/photo-1506744038136-46273834b3fb',
                'https://images.unsplash.com/photo-1465101178521-c1a9136a3c8b',
                'https://images.unsplash.com/photo-1502086223501-7ea6ecd79368',
                'https://images.unsplash.com/photo-1465101046530-73398c7f28ca',
                'https://images.unsplash.com/photo-1519125323398-675f0ddb6308',
            ],
            'Sports' => [
                'https://images.unsplash.com/photo-1517649763962-0c623066013b',
                'https://images.unsplash.com/photo-1506744038136-46273834b3fb',
                'https://images.unsplash.com/photo-1517841905240-472988babdf9',
                'https://images.unsplash.com/photo-1519389950473-47ba0277781c',
                'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9',
            ],
        ];

        $imageurl = isset($imageUrls[$category])
            ? $this->faker->randomElement($imageUrls[$category])
            : 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308';

        return [
            'name' => $productName,
            'quantity' => $quantity,
            'category' => $category,
            'description' => $this->generateDescription($productName, $category),
            'price' => $price,
            'status' => $status,
            'imageurl' => $imageurl,
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            }
        ];
    }

    /**
     * Generate realistic product descriptions
     */
    private function generateDescription($productName, $category): string
    {
        $descriptions = [
            'Electronics' => [
                'High-quality electronic device with modern features',
                'Advanced technology for enhanced user experience',
                'Durable and reliable electronic equipment',
                'State-of-the-art design with premium materials',
                'Energy-efficient with long-lasting performance'
            ],
            'Clothing' => [
                'Comfortable and stylish apparel for everyday wear',
                'Premium fabric with excellent fit and finish',
                'Trendy design suitable for various occasions',
                'Breathable material with modern cut',
                'High-quality garment with attention to detail'
            ],
            'Books' => [
                'Comprehensive guide with expert insights',
                'Well-researched content from industry professionals',
                'Educational resource with practical applications',
                'Engaging read with valuable information',
                'Essential reference for professionals and enthusiasts'
            ],
            'Home & Garden' => [
                'Functional and decorative item for your home',
                'Durable construction with aesthetic appeal',
                'Practical solution for home organization',
                'Quality craftsmanship with modern design',
                'Essential item for comfortable living'
            ],
            'Sports' => [
                'Professional-grade equipment for optimal performance',
                'Durable construction suitable for regular use',
                'Ergonomic design for comfort and efficiency',
                'High-quality materials for long-lasting durability',
                'Essential gear for fitness enthusiasts'
            ]
        ];

        $defaultDescriptions = [
            'Quality product with excellent value for money',
            'Popular item with great customer reviews',
            'Reliable and durable construction',
            'Perfect addition to your collection',
            'Highly recommended by customers'
        ];

        $categoryDescriptions = $descriptions[$category] ?? $defaultDescriptions;
        $baseDescription = $this->faker->randomElement($categoryDescriptions);
        
        return $baseDescription . '. ' . $this->faker->sentence();
    }

    /**
     * Create low stock products
     */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => $this->faker->numberBetween(1, 10),
            'status' => 'low_stock',
        ]);
    }

    /**
     * Create out of stock products
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => 0,
            'status' => $this->faker->randomElement(['ordered', 'discontinued']),
        ]);
    }

    /**
     * Create high-value products
     */
    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $this->faker->randomFloat(2, 500, 5000),
            'category' => $this->faker->randomElement(['Electronics', 'Automotive']),
        ]);
    }

    /**
     * Create electronics category products
     */
    public function electronics(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'Electronics',
            'name' => $this->faker->randomElement([
                'Laptop Computer', 'Smartphone', 'Tablet Device', 'Smart Watch',
                'Wireless Earbuds', 'Gaming Console', 'Digital Camera', 'Smart TV'
            ]),
        ]);
    }
}
