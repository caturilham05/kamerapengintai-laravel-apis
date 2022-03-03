<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarehouseOrderController;
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
Route::post('/logout', [AuthController::class, 'logout']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/marketplaces', [MarketplaceController::class, 'index']);
    Route::get('/marketplaces/{id}', [MarketplaceController::class, 'show']);
    Route::post('/marketplaces', [MarketplaceController::class, 'store']);
    Route::get('/orders', [WarehouseOrderController::class, 'index']);
    Route::get('/orders/{invoice?}', [WarehouseOrderController::class, 'show'])->where('invoice', '[\w\s\-_\/]+');
    Route::get('/products', [ProductController::class, 'index']);
});
