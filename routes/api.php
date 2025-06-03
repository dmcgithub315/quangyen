<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

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