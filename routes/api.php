<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// brands
Route::get('/brands', [BrandController::class, 'index'])
    ->name('brands.index');
Route::get('/brand/{uuid}', [BrandController::class, 'show'])
    ->name('brands.show');
Route::delete('/brand/{uuid}', [BrandController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('brands.destroy');
Route::post('/brand/create', [BrandController::class, 'store'])
    ->middleware(['auth'])
    ->name('brands.store');

// categories
Route::get('/categories', [CategoryController::class, 'index'])
    ->name('categories.index');
Route::get('/category/{uuid}', [CategoryController::class, 'show'])
    ->name('categories.show');
Route::delete('/category/{uuid}', [CategoryController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('categories.destroy');
Route::post('/category/create', [CategoryController::class, 'store'])
    ->middleware(['auth'])
    ->name('categories.store');

// products
Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');
Route::get('/product/{uuid}', [ProductController::class, 'show'])
    ->name('products.show');
Route::delete('/product/{uuid}', [ProductController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('products.destroy');
Route::post('/product/create', [ProductController::class, 'store'])
    ->middleware(['auth'])
    ->name('products.store');

// users
Route::post('/user/login', [UserController::class, 'login'])
    ->name('user.login');
Route::get('/user/logout', [UserController::class, 'logout'])
    ->name('user.logout');
Route::post('/user/create', [UserController::class, 'store'])
    ->name('user.store');

// admin
Route::group(['prefix' => 'admin'], function () {
    Route::post('/login', [AdminUserController::class, 'login'])
        ->name('admin.login');
    Route::get('/user-listing', [AdminUserController::class, 'index'])
        ->middleware(['auth:admin'])
        ->name('admin.index');
    Route::get('/logout', [AdminUserController::class, 'logout'])
        ->name('admin.logout');
});
