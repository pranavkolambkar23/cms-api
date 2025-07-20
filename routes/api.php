<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (requires token)
Route::middleware('auth:sanctum')->group(function () {
    // Authenticated user info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Articles - accessible by both admin and author
    Route::apiResource('articles', ArticleController::class);

    // Categories - only accessible by admin (authorization check)
    Route::apiResource('categories', CategoryController::class)
        ->middleware('can:manage-categories');
});
