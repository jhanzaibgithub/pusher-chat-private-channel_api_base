<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ChatController;

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (only for logged in users)
Route::middleware('auth:api')->group(function () {
    Route::post('/send-message', [ChatController::class, 'send']);
});
