<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
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


// categories
Route::get('/categories', [CategoryController::class, 'index'])
    ->name('categories.index');


// products
Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');
