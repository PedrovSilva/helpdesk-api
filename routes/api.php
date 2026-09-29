<?php

use App\Http\Controllers\Api\V1\AttachmentController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\SlaController;
use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Controllers\Api\V1\TicketHistoryController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TicketController::class, 'index']);

Route::prefix('api/v1')
    ->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('slas', SlaController::class);
        Route::apiResource('tickets', TicketController::class);
        Route::apiResource('comments', CommentController::class);
        Route::apiResource('attachments', AttachmentController::class);
        Route::apiResource('ticket-histories', TicketHistoryController::class);
    });
