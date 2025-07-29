<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductsServices
{
    /**
     * Get a paginated list of products with search and filtering
     *
     * @param array $params
     * @return LengthAwarePaginator
     */
    public function list(array $params): LengthAwarePaginator
    {
        $query = Product::query();

        // Apply search filter
        if (!empty($params['search'])) {
            $query->search($params['search']);
        }

        // Apply category filter
        if (!empty($params['category'])) {
            $query->byCategory($params['category']);
        }

        // Apply status filter
        if (!empty($params['status'])) {
            $query->byStatus($params['status']);
        }

        // Apply price range filter
        if (!empty($params['min_price']) || !empty($params['max_price'])) {
            $query->priceRange($params['min_price'] ?? null, $params['max_price'] ?? null);
        }

        // Apply low stock filter if threshold is provided
        if (!empty($params['low_stock_threshold'])) {
            $query->lowStock($params['low_stock_threshold']);
        }

        // Apply sorting
        $sortBy = $params['sort_by'] ?? 'created_at';
        $sortOrder = $params['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $params['per_page'] ?? 15;
        
        return $query->paginate($perPage);
    }

    /**
     * Get product statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total_products' => Product::count(),
            'in_stock' => Product::where('status', 'in_stock')->count(),
            'low_stock' => Product::where('status', 'low_stock')->count(),
            'ordered' => Product::where('status', 'ordered')->count(),
            'discontinued' => Product::where('status', 'discontinued')->count(),
            'total_value' => Product::sum(DB::raw('price * quantity')),
            'categories' => Product::distinct()->pluck('category')->filter()->values(),
        ];
    }

    /**
     * Get products that are low in stock
     *
     * @param int $threshold
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLowStockProducts(int $threshold = 10)
    {
        return Product::lowStock($threshold)->get();
    }

    /**
     * Create a new product
     *
     * @param array $data
     * @return Product
     * @throws \Exception
     */
    public function create(array $data): Product
    {
        try {
            // Create the product
            $product = Product::create($data);

            // Log the creation (optional - you can add logging here if needed)
            Log::info('Product created successfully', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'created_by' => auth()->id() ?? 'system'
            ]);

            return $product;
        } catch (\Exception $e) {
            Log::error('Failed to create product', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception('Failed to create product: ' . $e->getMessage());
        }
    }

    /**
     * Check if a product name already exists
     *
     * @param string $name
     * @param int|null $excludeId
     * @return bool
     */
    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $query = Product::where('name', $name);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Get suggested categories based on existing products
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSuggestedCategories()
    {
        return Product::distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->filter()
            ->sort()
            ->values();
    }

    /**
     * Delete a product by ID
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id): bool
    {
        try {
            $product = Product::find($id);
            
            if (!$product) {
                throw new \Exception('Product not found', 404);
            }

            // Log the product details before deletion
            Log::info('Product deletion initiated', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'category' => $product->category,
                'deleted_by' => auth()->id() ?? 'system'
            ]);

            // Store product info for logging after deletion
            $productInfo = [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'quantity' => $product->quantity,
                'price' => $product->price
            ];

            // Delete the product
            $deleted = $product->delete();

            if ($deleted) {
                Log::info('Product deleted successfully', [
                    'product_info' => $productInfo,
                    'deleted_by' => auth()->id() ?? 'system'
                ]);
            }

            return $deleted;
        } catch (\Exception $e) {
            Log::error('Failed to delete product', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Delete multiple products by IDs
     *
     * @param array $ids
     * @return array
     */
    public function deleteMultiple(array $ids): array
    {
        $results = [
            'deleted' => [],
            'failed' => [],
            'not_found' => []
        ];

        foreach ($ids as $id) {
            try {
                $product = Product::find($id);
                
                if (!$product) {
                    $results['not_found'][] = $id;
                    continue;
                }

                $productInfo = [
                    'id' => $product->id,
                    'name' => $product->name
                ];

                if ($product->delete()) {
                    $results['deleted'][] = $productInfo;
                } else {
                    $results['failed'][] = $productInfo;
                }
            } catch (\Exception $e) {
                $results['failed'][] = [
                    'id' => $id,
                    'error' => $e->getMessage()
                ];
            }
        }

        Log::info('Bulk product deletion completed', [
            'results' => $results,
            'deleted_by' => auth()->id() ?? 'system'
        ]);

        return $results;
    }

    /**
     * Get a product by ID
     *
     * @param int $id
     * @return Product|null
     */
    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    /**
     * Check if products can be safely deleted (no dependencies)
     *
     * @param array $ids
     * @return array
     */
    public function checkDeletionConstraints(array $ids): array
    {
        $constraints = [];
        
        foreach ($ids as $id) {
            $product = Product::find($id);
            
            if (!$product) {
                $constraints[$id] = ['error' => 'Product not found'];
                continue;
            }

            // Add any business logic constraints here
            // For example, check if product is referenced in orders, etc.
            $productConstraints = [];

            // Example constraint: Don't delete products with high value
            if ($product->price * $product->quantity > 10000) {
                $productConstraints[] = 'High-value product (total value > $10,000)';
            }

            // Example constraint: Don't delete if it's the last product in category
            if ($product->category) {
                $categoryCount = Product::where('category', $product->category)->count();
                if ($categoryCount === 1) {
                    $productConstraints[] = 'Last product in category "' . $product->category . '"';
                }
            }

            if (!empty($productConstraints)) {
                $constraints[$id] = [
                    'product_name' => $product->name,
                    'constraints' => $productConstraints
                ];
            }
        }

        return $constraints;
    }
}
