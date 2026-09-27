<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketsController;

Route::get('/', [TicketsController::class, 'index']);
