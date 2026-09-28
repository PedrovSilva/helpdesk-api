<?php

use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TicketController::class, 'index']);

Route::prefix('v1')
    ->group(function () {
        Route::apiResource('users', UserController::class);
    });
