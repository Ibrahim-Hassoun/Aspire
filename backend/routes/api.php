<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductsController;

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

Route::middleware('authenticated')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/profile', [AuthController::class, 'profile']);
});

});

Route::group(['middleware' => 'authenticated'], function () {
    Route::post('/sendMessage', [\App\Http\Controllers\ChatbotController::class, 'sendMessage']);
    Route::get('/test-chatbot', [\App\Http\Controllers\ChatbotController::class, 'testConnection']);
    Route::get('/simple-test', [\App\Http\Controllers\ChatbotController::class, 'simpleTest']);
    
    // Products routes
    Route::group(['prefix' => 'products'], function () {
        Route::get('/', [ProductsController::class, 'list']);
        Route::post('/', [ProductsController::class, 'create']);
        Route::get('/statistics', [ProductsController::class, 'statistics']);
        Route::get('/low-stock', [ProductsController::class, 'lowStock']);
        Route::get('/suggested-categories', [ProductsController::class, 'suggestedCategories']);
        Route::post('/check-name', [ProductsController::class, 'checkNameAvailability']);
        Route::post('/check-deletion-constraints', [ProductsController::class, 'checkDeletionConstraints']);
        Route::get('/{id}', [ProductsController::class, 'show']);
        
        // Admin-only routes (delete operations)
        Route::middleware('admin')->group(function () {
            Route::delete('/bulk', [ProductsController::class, 'deleteMultiple']);
            Route::delete('/{id}', [ProductsController::class, 'delete']);
        });
    });
});