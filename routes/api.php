<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\ProductCartController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RecipientController;
use App\Http\Controllers\WarehouseOrderController;
use App\Http\Controllers\WarehouseOrderProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', [AuthController::class, 'login'])->name('login');

Route::get('/product_categories', [ProductCategoryController::class, 'index']);
Route::get('/product_categories_grouping', [ProductCategoryController::class, 'group_category']);
Route::get('/product_categories/{id}', [ProductCategoryController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/product_detail/{id}', [ProductController::class, 'show']);
Route::get('/product_related/{id}', [ProductController::class, 'product_related']);
Route::get('/products/{name?}', [ProductController::class, 'useParams'])->where('name', '[\w\s\-_\/]+');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/marketplaces', [MarketplaceController::class, 'index']);
    Route::get('/marketplace_detail/{id}', [MarketplaceController::class, 'show']);
    Route::post('/marketplaces', [MarketplaceController::class, 'store']);

    Route::get('/orders', [WarehouseOrderController::class, 'index']);
    Route::get('/orders/{invoice?}', [WarehouseOrderController::class, 'show'])->where('invoice', '[\w\s\-_\/]+');
    Route::get('/order_detail/{id}', [WarehouseOrderController::class, 'useId']);

    Route::get('/order_products', [WarehouseOrderProductController::class, 'index']);
    Route::get('/order_product_detail/{id}', [WarehouseOrderProductController::class, 'show']);
    Route::get('/order_products/{invoice?}', [WarehouseOrderProductController::class, 'useParams'])->where('invoice', '[\w\s\-_\/]+');

    Route::get('/recipients', [RecipientController::class, 'index']);
    Route::get('/recipient_detail/{id}', [RecipientController::class, 'show']);
    Route::get('/recipients/{name}', [RecipientController::class, 'useParams'])->where('name', '[\w\s\-_\/]+');

    Route::get('/product_cart/{user_id}', [ProductCartController::class, 'show']);
    Route::get('/product_cart_total/{user_id}', [ProductCartController::class, 'productCartTotal']);
    Route::post('/product_cart', [ProductCartController::class, 'create']);
    Route::post('/product_cart_increment/{id}', [ProductCartController::class, 'update']);
    Route::post('/product_cart_decrement/{id}', [ProductCartController::class, 'decrementQty']);
    Route::delete('/product_cart/{id}', [ProductCartController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
