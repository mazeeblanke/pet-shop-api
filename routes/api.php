<?php

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


// categories
Route::get('/categories', [CategoryController::class, 'index'])
    ->name('categories.index');
Route::get('/category/{uuid}', [CategoryController::class, 'show'])
    ->name('categories.show');


// products
Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');
Route::get('/product/{uuid}', [ProductController::class, 'show'])
    ->name('products.show');

// users
Route::post('/user/login', [UserController::class, 'login'])
    ->name('user.login');
Route::get('/user/logout', [UserController::class, 'logout'])
    ->name('user.logout');
