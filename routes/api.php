<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::POST('/login', [AuthController::class, 'actionLogin']);
Route::POST('/user', [UserController::class, 'actionCreateUser']);
