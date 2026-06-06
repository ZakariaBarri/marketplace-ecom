<?php

use App\Http\Controllers\Api\AddresseController;
use App\Http\Controllers\Api\Admin\AdminCategoryController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\AdminProductController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ConditionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SizeController;
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

Route::post('/broadcasting/auth', \App\Http\Controllers\Api\BroadcastAuthController::class)
    ->middleware('auth:sanctum'); // أو auth:api حسب نظامك

// notifications
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/notifications', [NotificationController::class, 'index']);

    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

});

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
Route::get('/sizes', [SizeController::class, 'index']);

// protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('/me', [AuthController::class, 'me']);
    //-----------------------------------------------------------------------
    Route::apiResource('products', ProductController::class)
        ->except(['index', 'show']);

    Route::get('/my-products', [ProductController::class, 'myProducts']);

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

    //----------------------------------------------------------------------
    Route::post('/reviews', [ReviewController::class, 'store']);
    // استعراض جميع التقييمات (اختياري: Admin)
    Route::get('/reviews', [ReviewController::class, 'index']);
    // تقييمات مستخدم معين
    Route::get('/users/{id}/reviews', [ReviewController::class, 'userReviews']);
    // تقييمات كبائع
    Route::get('/users/{id}/reviews/seller', [ReviewController::class, 'sellerReviews']);
    // تقييمات كمشتري
    Route::get('/users/{id}/reviews/buyer', [ReviewController::class, 'buyerReviews']);
    // تقييمات كتبتها شخصيًا
    Route::get('/reviews/my', [ReviewController::class, 'myReviews']);

    //-----------------------------------------------------------------------
    Route::apiResource('/me/addresses', AddresseController::class);
    //change to /addresses 

    //-----------------------------------------------------------------------
    Route::prefix('profile')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::post('/password', [ProfileController::class, 'changePassword']);
        Route::get('/stats', [ProfileController::class, 'stats']);
    });

    //-----------------------------------------------------------------------
    //Route::get('/notifications', [NotificationController::class, 'index']);

});

Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {

    // 📊 Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard']);

    // 👥 Users
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/{user}', [AdminUserController::class, 'show']);
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);

    // 🛍️ Products
    Route::get('/products', [AdminProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/{product}', [AdminProductController::class, 'show']);
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy']);

    // 🗂️ Categories
    Route::apiResource('/categories', AdminCategoryController::class);
});
    //-----------------------------------------------------------------------