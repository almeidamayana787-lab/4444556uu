<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GameApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/me', [AuthController::class, 'me']);

// Public Settings
Route::get('/settings', [AdminController::class, 'getSettings']);

// Public Game endpoints
Route::get('/games/popular', [GameApiController::class, 'getPopularGames']);
Route::get('/games/providers', [GameApiController::class, 'getSlotProviders']);
Route::get('/games/providers/all', [GameApiController::class, 'getAllProviders']);
Route::get('/games/provider/{code}', [GameApiController::class, 'getProviderGames']);
Route::post('/game/launch', [GameApiController::class, 'launchGame']);

// Admin Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/settings', [AdminController::class, 'updateSettings']);
    Route::post('/admin/upload', [AdminController::class, 'uploadImage']);

    // Game API admin routes
    Route::post('/admin/test-api', [GameApiController::class, 'testConnection']);
    Route::post('/admin/fetch-games', [GameApiController::class, 'fetchGames']);
    Route::post('/admin/delete-games', [GameApiController::class, 'deleteGames']);
    Route::post('/admin/set-popular', [GameApiController::class, 'setPopular']);
    Route::post('/admin/set-slot-provider', [GameApiController::class, 'setSlotProvider']);
    Route::post('/admin/set-provider-logo', [GameApiController::class, 'setProviderLogo']);
    Route::post('/admin/remove-slot-provider', [GameApiController::class, 'removeSlotProvider']);
    Route::get('/admin/games-grouped', [GameApiController::class, 'getAllGamesGrouped']);
});

// Webhook for game callbacks (No auth required by MAX API)
Route::post('/webhook/game-callback', [GameApiController::class, 'handleCallback']);
