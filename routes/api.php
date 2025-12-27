<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransferController;

Route::POST('/login', [AuthController::class, 'actionLogin']);
Route::POST('/user', [UserController::class, 'actionCreateUser']);

Route::middleware(['jwt'])->group(function () {
    Route::POST('/transfer', [TransferController::class, 'actionTransfer']);
    Route::POST('/user/{document}/add-balance', [TransferController::class, 'actionAddBalance']);
    Route::GET('/user/{document}/statement', [TransferController::class, 'actionStatements']);
});
