<?php

use App\Http\Controllers\Api\AuthController;
// import home controller
use App\Http\Controllers\Api\HomePageController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

// home rout + home function (GET method)
Route::get('/home', [HomePageController::class, 'home']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
