<?php

use App\Http\Controllers\Gateway\GgpixController;
use Illuminate\Support\Facades\Route;

Route::prefix('ggpix')
    ->group(function () {
        Route::post('callback', [GgpixController::class, 'callbackMethod']);
    });
