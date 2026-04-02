<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ConditionController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('products', ProductController::class)
    ->only(['index', 'show']);
Route::apiResource('/categories', CategoryController::class)
    ->only(['index', 'show']);
Route::get('/conditions', [ConditionController::class, 'index']);

// protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('/me', [AuthController::class, 'me']);
    //-----------------------------------------------------------------------
    Route::apiResource('products', ProductController::class)
        ->except(['index', 'show']);

    Route::apiResource('/categories', CategoryController::class)
        ->except(['index', 'show']);

    //-----------------------------------------------------------------------
    Route::get('/buyer/orders', [OrderController::class, 'buyerOrders']);
    Route::get('/seller/orders', [OrderController::class, 'sellerOrders']);

    //Route::post('/products/{id}/order', [OrderController::class, 'store']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

    Route::post('/orders/{id}/accept', [OrderController::class, 'accept']);
    Route::post('/orders/{id}/ship', [OrderController::class, 'ship']);
    Route::post('/orders/{id}/deliver', [OrderController::class, 'deliver']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{id}/reject', [OrderController::class, 'reject']);
    Route::post('/orders/{id}/failed-delivery', [OrderController::class, 'failedDelivery']);
});
