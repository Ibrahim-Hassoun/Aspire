<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Services\ProductsServices;
use App\Http\Requests\ProductsListRequest;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductDeleteRequest;

class ProductsController extends Controller
{
    use ApiResponse;
    protected $productsServices;
    
    public function __construct(ProductsServices $productsServices)
    {
        $this->productsServices = $productsServices;
    }

    /**
     * Get a paginated list of products with search and filtering
     *
     * @param ProductsListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(ProductsListRequest $request)
    {
        try {
            $params = $request->getValidatedWithDefaults();
            $products = $this->productsServices->list($params);
            $user = auth()->user();
            
            return $this->success('Products retrieved successfully', [
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ],
                'filters_applied' => array_filter($params, function($value) {
                    return $value !== null && $value !== '';
                }),
                'user_permissions' => [
                    'can_delete' => $user->canDeleteProducts(),
                    'role' => $user->getRoleDisplayName(),
                    'is_admin' => $user->hasAdminPrivileges()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    /**
     * Get product statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics()
    {
        try {
            $stats = $this->productsServices->getStatistics();
            return $this->success('Product statistics retrieved successfully', $stats);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    /**
     * Get products that are low in stock
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lowStock(Request $request)
    {
        try {
            $threshold = $request->get('threshold', 10);
            $products = $this->productsServices->getLowStockProducts($threshold);
            
            return $this->success('Low stock products retrieved successfully', [
                'products' => $products,
                'threshold' => $threshold,
                'count' => $products->count()
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    /**
     * Create a new product
     *
     * @param ProductCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(ProductCreateRequest $request)
    {
        try {
            $data = $request->getValidatedWithDefaults();
            $product = $this->productsServices->create($data);
            
            return $this->success('Product created successfully', [
                'product' => $product
            ], 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    /**
     * Get suggested categories for product creation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestedCategories()
    {
        try {
            $categories = $this->productsServices->getSuggestedCategories();
            
            return $this->success('Suggested categories retrieved successfully', [
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    /**
     * Check if a product name is available
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkNameAvailability(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'exclude_id' => 'nullable|integer|exists:products,id'
            ]);

            $name = trim($request->get('name'));
            $excludeId = $request->get('exclude_id');
            
            $exists = $this->productsServices->nameExists($name, $excludeId);
            
            return $this->success('Name availability checked', [
                'name' => $name,
                'available' => !$exists,
                'exists' => $exists
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    /**
     * Delete a single product
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(int $id)
    {
        try {
            $deleted = $this->productsServices->delete($id);
            
            if ($deleted) {
                return $this->success('Product deleted successfully', [
                    'deleted_product_id' => $id
                ]);
            } else {
                return $this->error('Failed to delete product', 500);
            }
        } catch (\Exception $e) {
            $statusCode = $e->getCode() === 404 ? 404 : 400;
            return $this->error($e->getMessage(), $statusCode);
        }
    }

    /**
     * Delete multiple products
     *
     * @param ProductDeleteRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMultiple(ProductDeleteRequest $request)
    {
        try {
            $ids = $request->get('ids');
            $force = $request->get('force', false);

            // Check deletion constraints unless force is true
            if (!$force) {
                $constraints = $this->productsServices->checkDeletionConstraints($ids);
                
                if (!empty($constraints)) {
                    return $this->error('Some products cannot be deleted due to constraints', 422, [
                        'constraints' => $constraints,
                        'message' => 'Use force=true to override constraints'
                    ]);
                }
            }

            $results = $this->productsServices->deleteMultiple($ids);
            
            $message = sprintf(
                'Deletion completed: %d deleted, %d failed, %d not found',
                count($results['deleted']),
                count($results['failed']),
                count($results['not_found'])
            );

            return $this->success($message, [
                'results' => $results,
                'summary' => [
                    'deleted_count' => count($results['deleted']),
                    'failed_count' => count($results['failed']),
                    'not_found_count' => count($results['not_found']),
                    'total_requested' => count($ids)
                ]
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    /**
     * Get a single product by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        try {
            $product = $this->productsServices->findById($id);
            
            if (!$product) {
                return $this->error('Product not found', 404);
            }
            
            return $this->success('Product retrieved successfully', [
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }

    /**
     * Check deletion constraints for products
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkDeletionConstraints(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'required|integer|exists:products,id'
            ]);

            $ids = $request->get('ids');
            $constraints = $this->productsServices->checkDeletionConstraints($ids);
            
            return $this->success('Deletion constraints checked', [
                'constraints' => $constraints,
                'can_delete_all' => empty($constraints),
                'constrained_count' => count($constraints),
                'total_checked' => count($ids)
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
