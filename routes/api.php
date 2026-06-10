<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;

// import home controller
use App\Http\Controllers\Api\HomePageController;
use App\Http\Controllers\Api\MemeViewController;
use App\Http\Controllers\Api\PollController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Api\CallToActionController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\MemeController;
use App\Http\Controllers\Api\ArticleSourceController;
use App\Http\Controllers\Api\SourceController;
use App\Http\Controllers\Api\ReactionController;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// retired home rout + home function (GET method)
// Route::get('/home', [HomePageController::class, 'home']);
Route::get('/tags', [TagController::class, 'index']);
Route::get('memes', [MemeController::class, 'index']);
Route::get('memes/{meme}', [MemeController::class, 'show']);
Route::get('articles/{article}/reactions', [ReactionController::class, 'articleIndex']);
Route::get('memes/{meme}/reactions', [ReactionController::class, 'memeIndex']);

//memes
Route::post('meme-views', [MemeViewController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    // account route for displaying saved articles
    Route::get('/account', [AuthController::class, 'account']);
    //edit account details route, can update name and email
    Route::patch('/account', [AuthController::class, 'updateAccount']);
    // post route to save articles inside "saved_articles" table
    Route::post('/account/articles/{article}/save', [AuthController::class, 'saveArticle']);
    // delete route to remove article from saved articles of user
    Route::delete('/account/articles/{article}/save', [AuthController::class, 'removeSavedArticle']);
    // post route to save memes inside "saved_memes" table
    Route::post('/account/memes/{meme}/save', [AuthController::class, 'saveMeme']);
    // delete route to remove meme from saved memes of user
    Route::delete('/account/memes/{meme}/save', [AuthController::class, 'removeSavedMeme']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/update-account', [AuthController::class, 'updateAccount']);
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);
    Route::get('/me/tags', [TagController::class, 'myTags']);
    Route::put('/me/tags', [TagController::class, 'updateMyTags']);
    // reactions for articles and memes
    Route::get('articles/{article}/my-reaction', [ReactionController::class, 'articleMyReaction']);
    Route::get('memes/{meme}/my-reaction', [ReactionController::class, 'memeMyReaction']);

    Route::post('articles/{article}/reaction', [ReactionController::class, 'articleStore']);
    Route::delete('articles/{article}/reaction', [ReactionController::class, 'articleDestroy']);

    Route::post('memes/{meme}/reaction', [ReactionController::class, 'memeStore']);
    Route::delete('memes/{meme}/reaction', [ReactionController::class, 'memeDestroy']);
});

//articles
// happy feed route displays articles with the happy tag
Route::get('happy-feed', [ArticleController::class, 'happyFeed']);
Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/{article}', [ArticleController::class, 'show']);
Route::get('articles/{article}/sources', [ArticleSourceController::class, 'index']);
Route::get('sources', [SourceController::class, 'index']);

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
    Route::post('articles/{article}/sources', [ArticleSourceController::class, 'store']);
    Route::patch('articles/{article}/sources/{source}', [ArticleSourceController::class, 'update']);
    Route::delete('articles/{article}/sources/{source}', [ArticleSourceController::class, 'destroy']);
    Route::post('sources', [SourceController::class, 'store']);
    Route::patch('sources/{source}', [SourceController::class, 'update']);
    Route::delete('sources/{source}', [SourceController::class, 'destroy']);
    Route::patch('memes/{meme}', [MemeController::class, 'update']);
    Route::delete('memes/{meme}', [MemeController::class, 'destroy']);
});

// polls for everyone
Route::get('polls', [PollController::class, 'index']);
Route::get('polls/{poll}', [PollController::class, 'show']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('polls/{poll}/results', [PollController::class, 'results']);
    Route::get('poll-options', [App\Http\Controllers\Api\PollOptionController::class, 'index']);
    Route::post('poll-votes', [App\Http\Controllers\Api\PollVoteController::class, 'store']);
    Route::delete('poll-votes/{pollVote}', [App\Http\Controllers\Api\PollVoteController::class, 'destroy']);
});


// admin only
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('polls', [PollController::class, 'store']);
    Route::put('polls/{poll}', [PollController::class, 'update']);
    Route::patch('polls/{poll}', [PollController::class, 'update']);
    Route::delete('polls/{poll}', [PollController::class, 'destroy']);
    Route::post('poll-options', [App\Http\Controllers\Api\PollOptionController::class, 'store']);
    Route::put('poll-options/{pollOption}', [App\Http\Controllers\Api\PollOptionController::class, 'update']);
    Route::patch('poll-options/{pollOption}', [App\Http\Controllers\Api\PollOptionController::class, 'update']);
    Route::delete('poll-options/{pollOption}', [App\Http\Controllers\Api\PollOptionController::class, 'destroy']);
    Route::get('poll-votes/{pollVote}', [App\Http\Controllers\Api\PollVoteController::class, 'show']);
    Route::put('poll-votes/{pollVote}', [App\Http\Controllers\Api\PollVoteController::class, 'update']);
    Route::patch('poll-votes/{pollVote}', [App\Http\Controllers\Api\PollVoteController::class, 'update']);
});
