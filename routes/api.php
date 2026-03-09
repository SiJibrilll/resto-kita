<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('table_session.validate')->group(function () {
    Route::apiResource('items', ItemController::class);
    
    Route::apiResource('categories', CategoryController::class);
    
    Route::apiResource('orders', OrderController::class);
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
