<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductInfoController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Image upload routes
Route::post('/images/upload', [ImageUploadController::class, 'upload']);
Route::post('/images/ckeditor-upload', [ImageUploadController::class, 'ckeditorUpload']);
Route::post('/upload-images', [ImageUploadController::class, 'uploadMultiple']);
Route::delete('/images/delete', [ImageUploadController::class, 'delete']);
Route::post('/images/optimize', [ImageUploadController::class, 'getOptimizedUrl']);

// Category routes (public access for now)
Route::get('/categories/addnew', [CategoryController::class, 'addnew']);
Route::get('/categories/tree', [CategoryController::class, 'tree']);
Route::apiResource('categories', CategoryController::class);
Route::patch('/categories/{id}/toggle-status', [CategoryController::class, 'toggleStatus']);

// Product routes (public access for now)
Route::get('/products/addnew', [ProductController::class, 'addnew']);
Route::get('/products/by-category/{categoryId}', [ProductController::class, 'byCategory']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::apiResource('products', ProductController::class);

// Product Info routes (public access for now)
Route::get('/product-info/by-product/{productId}', [ProductInfoController::class, 'getByProduct']);
Route::post('/product-info/bulk', [ProductInfoController::class, 'bulkStore']);
Route::apiResource('product-info', ProductInfoController::class);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Admin routes
    Route::middleware('role:admin')->group(function () {
        // Thêm các route cho admin ở đây
    });

    // Accountant routes
    Route::middleware('role:accountant')->group(function () {
        // Thêm các route cho accountant ở đây
    });

    // Staff routes
    Route::middleware('role:staff')->group(function () {
        // Thêm các route cho staff ở đây
    });

    // User routes
    Route::middleware('role:user')->group(function () {
        // Thêm các route cho user ở đây
    });
}); 