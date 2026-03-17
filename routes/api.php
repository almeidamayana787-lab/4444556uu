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
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

// Payment endpoints (GGPIX)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/pix/deposit', [\App\Http\Controllers\PaymentController::class, 'createDeposit']);
    Route::post('/pix/withdraw', [\App\Http\Controllers\PaymentController::class, 'requestWithdrawal']);

    // Profile Subpages
    Route::get('/profile/history', [\App\Http\Controllers\ProfileController::class, 'getHistory']);
    Route::get('/profile/affiliate', [\App\Http\Controllers\ProfileController::class, 'getAffiliate']);
    Route::post('/profile/security', [\App\Http\Controllers\ProfileController::class, 'updateSecurity']);
});

// Info endpoints
Route::get('/faq', [\App\Http\Controllers\ProfileController::class, 'getFaq']);
Route::get('/support', [\App\Http\Controllers\ProfileController::class, 'getSupport']);

// Public Settings
Route::get('/settings', [AdminController::class, 'getSettings']);

// Public Game endpoints
Route::get('/games/popular', [GameApiController::class, 'getPopularGames']);
Route::get('/games/providers', [GameApiController::class, 'getSlotProviders']);
Route::get('/games/providers/all', [GameApiController::class, 'getAllProviders']);
Route::get('/games/provider/{code}', [GameApiController::class, 'getProviderGames']);
Route::get('/games/all-grouped', [GameApiController::class, 'getAllGamesGrouped']);
Route::middleware('auth:sanctum')->post('/game/launch', [GameApiController::class, 'launchGame']);

// Admin Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/settings', [AdminController::class, 'updateSettings']);
    Route::post('/admin/upload', [AdminController::class, 'uploadImage']);

    // Game API admin routes
    Route::post('/admin/test-api', [GameApiController::class, 'testConnection']);
    Route::post('/admin/fetch-games', [GameApiController::class, 'fetchGames']);
    Route::post('/admin/delete-games', [GameApiController::class, 'deleteGames']);
    Route::post('/admin/set-popular', [GameApiController::class, 'setPopular']);
    Route::post('/admin/set-retro', [GameApiController::class, 'setRetro']);
    Route::post('/admin/set-slot-provider', [GameApiController::class, 'setSlotProvider']);
    Route::post('/admin/set-provider-logo', [GameApiController::class, 'setProviderLogo']);
    Route::post('/admin/remove-slot-provider', [GameApiController::class, 'removeSlotProvider']);
    Route::get('/admin/games-grouped', [GameApiController::class, 'getAllGamesGrouped']);

    // User Management
    Route::get('/admin/users', [\App\Http\Controllers\UserManagementController::class, 'listUsers']);
    Route::post('/admin/users', [\App\Http\Controllers\UserManagementController::class, 'createUser']);
    Route::post('/admin/users/{id}', [\App\Http\Controllers\UserManagementController::class, 'updateUser']);
    Route::get('/admin/users/{id}/history', [\App\Http\Controllers\UserManagementController::class, 'getUserHistory']);

    // GGPIX Admin
    Route::get('/admin/ggpix/balance', [AdminController::class, 'getGGPIXBalance']);
    Route::post('/admin/ggpix/withdraw-profit', [AdminController::class, 'withdrawProfit']);

    // Retro Games Admin
    Route::get('/admin/retro/games', [\App\Http\Controllers\RetroGameController::class, 'index']);
    Route::get('/admin/retro/difficulty', [\App\Http\Controllers\RetroGameController::class, 'getDifficultySettings']);
    Route::post('/admin/retro/difficulty', [\App\Http\Controllers\RetroGameController::class, 'saveDifficultySettings']);
    Route::post('/admin/retro/toggle', [\App\Http\Controllers\RetroGameController::class, 'toggleActive']);
    Route::post('/admin/retro/update-game', [\App\Http\Controllers\RetroGameController::class, 'updateGame']);

    // Deposit Bonus Rules
    Route::get('/admin/bonus-rules', [\App\Http\Controllers\DepositBonusRuleController::class, 'index']);
    Route::post('/admin/bonus-rules', [\App\Http\Controllers\DepositBonusRuleController::class, 'store']);
    Route::put('/admin/bonus-rules/{id}', [\App\Http\Controllers\DepositBonusRuleController::class, 'update']);
    Route::delete('/admin/bonus-rules/{id}', [\App\Http\Controllers\DepositBonusRuleController::class, 'destroy']);

    // RTP Retribution System
    Route::post('/admin/sync-rtp', [AdminController::class, 'syncRtp']);
});

// Public Retro Games
Route::get('/retro/games', [\App\Http\Controllers\RetroGameController::class, 'index']);
Route::middleware('auth:sanctum')->get('/retro/game/{gameId}/settings', [\App\Http\Controllers\RetroGameController::class, 'getGameSettings']);

// Webhook for game callbacks (No auth required by MAX API)
Route::post('/webhook/game-callback', [GameApiController::class, 'handleCallback']);
// Webhook for GGPIX
Route::post('/webhook/ggpix', [\App\Http\Controllers\PaymentController::class, 'handleWebhook']);
