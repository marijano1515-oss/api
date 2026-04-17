<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/logout',[AuthController::class,'logout']);
Route::post('/category',[CategoryController::class,'store']);
Route::post('/category',[CategoryController::class,'update']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



