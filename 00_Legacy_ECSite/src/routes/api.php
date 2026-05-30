<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\CartApiController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 商品関連API
Route::prefix('products')->group(function () {
    Route::get('/', [ProductApiController::class, 'index']);
    Route::get('/categories', [ProductApiController::class, 'categories']);
    Route::get('/suggestions', [ProductApiController::class, 'suggestions']);
    Route::get('/{product}', [ProductApiController::class, 'show']);
});

// カート関連API（認証必須）
Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
    Route::get('/', [CartApiController::class, 'index']);
    Route::get('/count', [CartApiController::class, 'count']);
    Route::post('/add/{product}', [CartApiController::class, 'add']);
    Route::patch('/update/{cartItem}', [CartApiController::class, 'update']);
    Route::delete('/remove/{cartItem}', [CartApiController::class, 'remove']);
    Route::delete('/clear', [CartApiController::class, 'clear']);
});

// パブリックAPI（認証不要）
Route::prefix('public')->group(function () {
    Route::get('/cart/count', [CartApiController::class, 'count']);
}); 