<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
// import home controller
use App\Http\Controllers\Api\HomePageController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Api\CallToActionController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\MemeController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// home rout + home function (GET method)
Route::get('/home', [HomePageController::class, 'home']);
Route::get('/tags', [TagController::class, 'index']);
Route::get('memes', [MemeController::class, 'index']);
Route::get('memes/{meme}', [MemeController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    // account route for displaying saved articles
    Route::get('/account', [AuthController::class, 'account']);
    // post route to save articles inside "saved_articles" table
    Route::post('/account/articles/{article}/save', [AuthController::class, 'saveArticle']);
    // delete route to remove article from saved articles of user
    Route::delete('/account/articles/{article}/save', [AuthController::class, 'removeSavedArticle']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/update-account', [AuthController::class, 'updateAccount']);
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);
    Route::get('/me/tags', [TagController::class, 'myTags']);
    Route::put('/me/tags', [TagController::class, 'updateMyTags']);
});

Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/{article}', [ArticleController::class, 'show']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('articles', [ArticleController::class, 'store']);
    Route::put('articles/{article}', [ArticleController::class, 'update']);
    Route::patch('articles/{article}', [ArticleController::class, 'update']);
    Route::delete('articles/{article}', [ArticleController::class, 'destroy']);
    Route::get('articles/{article}/edit', [ArticleController::class, 'edit']);
    Route::post('articles/{article}/cta', [CallToActionController::class, 'store']);
    Route::patch('articles/{article}/cta', [CallToActionController::class, 'update']);
    Route::delete('articles/{article}/cta', [CallToActionController::class, 'destroy']);
    Route::post('articles/{article}/memes', [MemeController::class, 'store']);
    Route::patch('memes/{meme}', [MemeController::class, 'update']);
    Route::delete('memes/{meme}', [MemeController::class, 'destroy']);
});
