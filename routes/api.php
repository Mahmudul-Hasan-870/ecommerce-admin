<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
});
