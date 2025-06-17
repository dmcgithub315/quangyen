<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::query();

            // Filter by stock status
            if ($request->has('stock_status')) {
                $query->where('stock_status', $request->stock_status);
            }

            // Filter by price range
            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // Search functionality
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $products = $query->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $products->items(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'message' => 'Products retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get data for adding a new product.
     *
     * @return JsonResponse
     */
    public function addnew(): JsonResponse
    {
        try {
            // Get all categories for dropdown
            $categories = Category::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']);

            // Get common values for dropdowns
            $stockStatuses = [
                'in_stock' => 'Còn hàng',
                'out_of_stock' => 'Hết hàng',
                'on_backorder' => 'Đặt trước'
            ];

            $priceUnits = ['VNĐ', 'USD', 'kg', 'm', 'm²', 'm³', 'tấn', 'cuộn', 'tấm', 'thanh'];

            return response()->json([
                'success' => true,
                'data' => [
                    'categories' => $categories,
                    'stock_statuses' => $stockStatuses,
                    'price_units' => $priceUnits,
                    'default_values' => [
                        'stock_status' => 'in_stock',
                        'manage_stock' => false,
                        'stock_quantity' => 0,
                        'rating' => 0.00,
                        'price_unit' => 'VNĐ'
                    ]
                ],
                'message' => 'Data for adding new product retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving data for new product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
                'price_unit' => 'nullable|string|max:50',
                'stock_quantity' => 'nullable|integer|min:0',
                'stock_status' => 'required|in:in_stock,out_of_stock,on_backorder',
                'origin' => 'nullable|string|max:255',
            ]);

            $validated['created_by'] = auth()->id();
            $validated['updated_by'] = auth()->id();

            $product = Product::create($validated);

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product created successfully'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = Product::with(['creator', 'updater', 'productInfo'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product retrieved successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified product.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
                'price_unit' => 'nullable|string|max:50',
                'stock_quantity' => 'nullable|integer|min:0',
                'stock_status' => 'required|in:in_stock,out_of_stock,on_backorder',
                'origin' => 'nullable|string|max:255',
            ]);

            $validated['updated_by'] = auth()->id();

            $product->update($validated);

            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Product updated successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products by category.
     *
     * @param Request $request
     * @param int $categoryId
     * @return JsonResponse
     */
    public function byCategory(Request $request, int $categoryId): JsonResponse
    {
        try {
            $query = Product::with(['category'])
                ->where('category_id', $categoryId);

            // Apply other filters
            if ($request->has('search')) {
                $query->search($request->search);
            }

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('per_page', 15);
            $products = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Products by category retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving products by category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured products with high ratings.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $minRating = $request->get('min_rating', 4.0);
            $limit = $request->get('limit', 10);

            $products = Product::with(['category'])
                ->withRating($minRating)
                ->inStock()
                ->orderBy('rating', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Featured products retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving featured products: ' . $e->getMessage()
            ], 500);
        }
    }
}
