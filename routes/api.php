<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/category', [CategoryController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/category', [CategoryController::class, 'store']);
    Route::put('/category/{id}', [CategoryController::class, 'update']);
    Route::delete('/category/{id}', [CategoryController::class, 'destroy']);
    Route::get('/category', [CategoryController::class, 'index']);

    Route::post('/product', [ProductController::class, 'store']);
    Route::put('/product/{id}', [ProductController::class, 'update']);
    Route::delete('/product/{id}', [ProductController::class, 'destroy']);
    Route::get('/product/{id}', [ProductController::class, 'show']);
    Route::get('/product', [ProductController::class, 'index']);

    Route::post('/order', [OrderController::class, 'store']);
    Route::put('/order/{id}', [OrderController::class, 'update']);
    Route::delete('/order/{id}', [OrderController::class, 'destroy']);
    Route::get('/order/{id}', [OrderController::class, 'show']);
    Route::get('/order', [OrderController::class, 'index']);

    Route::get('/favorites', [FavoritesController::class, 'index']);
    Route::post('/favorites/{id}', [FavoritesController::class, 'store']);
    Route::delete('/favorites/{id}', [FavoritesController::class, 'destroy']);

    Route::get('/cart{id}', [FavoritesController::class, 'index']);
    Route::post('/cart/{id}', [CartController::class, 'store']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::put('/cart/{id}', [CartController::class, 'update`']);

    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
    Route::get('/reviews/{id}', [ReviewController::class, 'index']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);

});



