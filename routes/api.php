<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/me', [AuthController::class, 'me']);

// Public Settings
Route::get('/settings', [AdminController::class, 'getSettings']);

// Admin Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/settings', [AdminController::class, 'updateSettings']);
    Route::post('/admin/upload', [AdminController::class, 'uploadImage']);
});
