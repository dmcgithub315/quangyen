<?php

namespace App\Http\Controllers;

use App\Models\ProductInfo;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductInfoController extends Controller
{
    /**
     * Display a listing of the product info.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $productId = $request->input('product_id');
            $search = $request->input('search');

            $query = ProductInfo::with('product');

            // Filter by product
            if ($productId) {
                $query->where('product_id', $productId);
            }

            // Search functionality
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply default ordering
            $query->ordered();

            $productInfo = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $productInfo->items(),
                'pagination' => [
                    'current_page' => $productInfo->currentPage(),
                    'last_page' => $productInfo->lastPage(),
                    'per_page' => $productInfo->perPage(),
                    'total' => $productInfo->total(),
                ],
                'message' => 'Product info retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product info.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $productInfo = ProductInfo::create($validated);

            return response()->json([
                'success' => true,
                'data' => $productInfo->load('product'),
                'message' => 'Product info created successfully'
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
                'message' => 'Error creating product info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product info.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $productInfo = ProductInfo::with('product')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $productInfo,
                'message' => 'Product info retrieved successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product info not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified product info.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $productInfo = ProductInfo::findOrFail($id);

            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $productInfo->update($validated);

            return response()->json([
                'success' => true,
                'data' => $productInfo->load('product'),
                'message' => 'Product info updated successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product info not found'
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
                'message' => 'Error updating product info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product info.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $productInfo = ProductInfo::findOrFail($id);
            $productInfo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product info deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product info not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product info by product ID.
     */
    public function getByProduct(int $productId): JsonResponse
    {
        try {
            // Verify product exists
            $product = Product::findOrFail($productId);

            $productInfo = ProductInfo::where('product_id', $productId)
                ->ordered()
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'product' => $product,
                    'info' => $productInfo
                ],
                'message' => 'Product info retrieved successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk create/update product info.
     */
    public function bulkStore(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'info' => 'required|array',
                'info.*.name' => 'required|string|max:255',
                'info.*.description' => 'nullable|string',
            ]);

            $productId = $validated['product_id'];
            $infoData = $validated['info'];

            // Clear existing info for this product (optional)
            if ($request->input('replace_existing', false)) {
                ProductInfo::where('product_id', $productId)->delete();
            }

            $createdInfo = [];
            foreach ($infoData as $info) {
                $info['product_id'] = $productId;
                $createdInfo[] = ProductInfo::create($info);
            }

            return response()->json([
                'success' => true,
                'data' => $createdInfo,
                'message' => 'Product info created successfully'
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
                'message' => 'Error creating product info: ' . $e->getMessage()
            ], 500);
        }
    }
}
