<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::POST('/login', [AuthController::class, 'actionLogin']);
Route::POST('/user', [UserController::class, 'actionCreateUser']);

Route::middleware(['jwt'])->group(function () {
    // Route::GET('/test', [TestController::class, 'protectedRoute']);
    // Route::GET('/profile', [TestController::class, 'userProfile']);
});
