<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ChatController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->group(function () {
    Route::post('/send-message', [ChatController::class, 'send']);
     Route::post('/get-messages', [ChatController::class, 'getMessage']);
    Route::get('/conversations', [ChatController::class, 'getAllConversations']);
});
