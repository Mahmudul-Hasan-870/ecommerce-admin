<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;

// Registration route
Route::post('register', [AuthController::class, 'register']);
// Login route
Route::post('login', [AuthController::class, 'login']);

// Routes that require authentication (protected by Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    // Update user information (name, email)
    Route::put('update', [AuthController::class, 'update']);
    // Logout route
    Route::delete('logout', [AuthController::class, 'logout']);
    // Get the authenticated user's info
    Route::get('me', [AuthController::class, 'me']);

     // Place an order
     Route::post('order', [OrderController::class, 'placeOrder']);

     // Get all orders of the logged-in user
     Route::get('orders', [OrderController::class, 'getOrders']);
 
     // Get the status of a specific order
     Route::get('order/{id}/status', [OrderController::class, 'getOrderStatus']);
 
     // Update order status (Admin or User)
     Route::put('order/{id}/status', [OrderController::class, 'updateOrderStatus']);
 

});
