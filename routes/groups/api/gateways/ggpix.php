<?php
use App\Http\Controllers\Api\Wallet\DepositController;
use Illuminate\Support\Facades\Route;

Route::prefix('ggpix')
    ->group(function () {
        Route::post('consult-status-transaction', [DepositController::class, 'consultStatusTransactionPix']);
    });
