<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('table_session.validate')->group(function () {
    Route::apiResource('items', ItemController::class);
    
    Route::apiResource('categories', CategoryController::class);
    
    Route::apiResource('orders', OrderController::class);

    Route::post('/table-sessions/{token}/checkout', [CheckoutController::class, 'checkout']);
});
    
Route::post('/payments/create', [PaymentController::class, 'createTransaction']);

Route::post('/payments/webhook', [PaymentController::class, 'webhook']);

Route::get('/payments/status/{invoiceId}', [PaymentController::class, 'status']);

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
