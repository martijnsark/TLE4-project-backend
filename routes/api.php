<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

//articles
Route::middleware('auth')->group(function () {
    Route::get('articles/{article}/edit', [\App\Http\Controllers\Api\ArticleController::class, 'edit'])->name('article.edit');
});
Route::resource('articles', \App\Http\Controllers\Api\ArticleController::class);
