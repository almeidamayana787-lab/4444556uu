<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Retro Games Integration
use App\Http\Controllers\RetroGameController;

// Launcher route - using nested path for better asset resolution
Route::get('/play/{game}/p', [RetroGameController::class, 'play'])->name('retro.play');

// Compatibility layer for Retro Games HTML5
// These MUST be at the root prefix 'games/' as the games and frontend hardcode these paths
Route::prefix('games')->group(function () {
    Route::post('/start', [RetroGameController::class, 'startGame'])->middleware('auth:sanctum,web');
    Route::get('/{game}/info', [RetroGameController::class, 'getGameSettings'])->middleware('auth:sanctum,web');
    Route::post('/{game}/win', [RetroGameController::class, 'win'])->middleware('auth:sanctum,web');
    Route::post('/{game}/lost', [RetroGameController::class, 'lost'])->middleware('auth:sanctum,web');
    Route::get('/{game}', [RetroGameController::class, 'handleRedirect'])->middleware('auth:sanctum,web');
});
