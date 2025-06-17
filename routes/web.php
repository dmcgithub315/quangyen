<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', function () {
    return view('pages.login');
})->name('login');

Route::get('/register', function () {
    return view('pages.register');
})->name('register');

Route::get('/product', function () {
    return view('home.product');
})->name('product');

Route::get('/category', function () {
    return view('home.category');
})->name('category');


Route::get('/dashboard', function () {
    return view('home.dashboard');
})->middleware(['auth', 'role:admin'])->name('dashboard');

Route::post('/login', [AuthController::class, 'webLogin'])->name('login.post');

Route::post('/logout', [AuthController::class, 'webLogout'])->name('logout');

// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

// Protected routes
// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::get('/user', [AuthController::class, 'user']);

//     // Admin routes
//     Route::middleware('role:admin')->group(function () {
//         // Thêm các route cho admin ở đây
//     });

//     // Accountant routes
//     Route::middleware('role:accountant')->group(function () {
//         // Thêm các route cho accountant ở đây
//     });

//     // Staff routes
//     Route::middleware('role:staff')->group(function () {
//         // Thêm các route cho staff ở đây
//     });

//     // User routes
//     Route::middleware('role:user')->group(function () {
//         // Thêm các route cho user ở đây
//     });
// });
