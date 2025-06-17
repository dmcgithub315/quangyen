<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Category::query();

            // Filter by active status
            if ($request->has('active')) {
                $query->where('is_active', $request->boolean('active'));
            }

            // Filter by parent category
            if ($request->has('parent_id')) {
                if ($request->parent_id === 'null' || $request->parent_id === null) {
                    $query->whereNull('parent_category_id');
                } else {
                    $query->where('parent_category_id', $request->parent_id);
                }
            }

            // Search by name
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Include relationships
            $query->with(['parent', 'children']);

            // Order by sort_order and name
            $query->orderBy('sort_order')->orderBy('name');

            // Pagination
            $perPage = $request->get('per_page', 15);
            $categories = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categories retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get data for adding a new category.
     *
     * @return JsonResponse
     */
    public function addnew(): JsonResponse
    {
        try {
            // Get all active categories that can be parent categories
            $parentCategories = Category::active()
                ->orderBy('name')
                ->get(['id', 'name', 'parent_category_id']);

            // Get the next sort order
            $nextSortOrder = Category::max('sort_order') + 1;

            return response()->json([
                'success' => true,
                'data' => [
                    'parent_categories' => $parentCategories,
                    'default_values' => [
                        'is_active' => true,
                        'sort_order' => $nextSortOrder,
                    ],
                    'form_rules' => [
                        'name' => 'required|string|max:255',
                        'description' => 'nullable|string',
                        'parent_category_id' => 'nullable|exists:categories,id',
                        'image' => 'nullable|string',
                        'is_active' => 'boolean',
                        'sort_order' => 'nullable|integer|min:0',
                        'slug' => 'nullable|string|max:255',
                    ]
                ],
                'message' => 'Data for adding new category retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving data for new category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created category.
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
                'parent_category_id' => 'nullable|exists:categories,id',
                'image' => 'nullable|string',
                'is_active' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            // Generate slug if not provided
            if (!isset($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            // Ensure slug is unique
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Category::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Set default values
            $validated['is_active'] = $validated['is_active'] ?? true;
            $validated['sort_order'] = $validated['sort_order'] ?? 0;
            $validated['created_by'] = auth()->id();

            $category = Category::create($validated);

            return response()->json([
                'success' => true,
                'data' => $category->load(['parent', 'children']),
                'message' => 'Category created successfully'
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
                'message' => 'Error creating category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified category.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $category = Category::with(['parent', 'children', 'descendants'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category retrieved successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified category.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'parent_category_id' => [
                    'nullable',
                    'exists:categories,id',
                    Rule::notIn([$id]) // Prevent self-reference
                ],
                'image' => 'nullable|string',
                'is_active' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
                'slug' => 'nullable|string|max:255',
            ]);

            // Generate slug if name changed and slug not provided
            if (!isset($validated['slug']) && $validated['name'] !== $category->name) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            // Ensure slug is unique (exclude current category)
            if (isset($validated['slug'])) {
                $originalSlug = $validated['slug'];
                $counter = 1;
                while (Category::where('slug', $validated['slug'])->where('id', '!=', $id)->exists()) {
                    $validated['slug'] = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            $validated['updated_by'] = auth()->id();

            $category->update($validated);

            return response()->json([
                'success' => true,
                'data' => $category->load(['parent', 'children']),
                'message' => 'Category updated successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
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
                'message' => 'Error updating category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified category.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            // Check if category has children
            if ($category->children()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category that has child categories'
                ], 400);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category tree structure.
     *
     * @return JsonResponse
     */
    public function tree(): JsonResponse
    {
        try {
            $categories = Category::with('descendants')
                ->whereNull('parent_category_id')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Category tree retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving category tree: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle category active status.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
            $category->is_active = !$category->is_active;
            $category->updated_by = auth()->id();
            $category->save();

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Category status updated successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating category status: ' . $e->getMessage()
            ], 500);
        }
    }
} 