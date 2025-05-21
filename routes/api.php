<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::get('categories', [CategoryController::class, 'categories']);
Route::get('/category-name/{id}', [CategoryController::class, 'getNameCategoryById']);

Route::get('/products/{category}', [ProductController::class, 'index']);
Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('subcategories/{category}', [CategoryController::class, 'subcategories']);
Route::post('quotations', [QuotationController::class, 'store']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::post('/cart/remove', [CartController::class, 'removeFromCart']);
    Route::get('/cart', [CartController::class, 'getCart']);
    Route::post('/cart/checkout', [CartController::class, 'checkout']);

});
Route::get('/orders', [CartController::class, 'getOrders']);
Route::get('/test-token', [AuthController::class, 'testToken']);
Route::get('/sliders', [BannerController::class, 'getSliders']);
