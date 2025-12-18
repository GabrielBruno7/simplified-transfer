<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::POST('/user', [UserController::class, 'actionCreateUser']);