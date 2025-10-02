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

Route::post('/product', [ProductController::class, 'createProduct']);
Route::get('/product/{id}', [ProductController::class, 'getProductById']);
Route::put('/product/{id}', [ProductController::class, 'updateProduct']);
Route::delete('/product/{id}', [ProductController::class, 'deleteProduct']);
Route::get('/products', [ProductController::class, 'getListProduct']);
Route::put('/product/{id}/price', [ProductController::class, 'updateProductPrice']);

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
