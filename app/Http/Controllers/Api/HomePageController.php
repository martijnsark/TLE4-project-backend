<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Article;


// created the controller for home related routes and features
class HomePageController extends controller
{
    // home route displays articles from new to old and does not display inactive articles
    public function home()
    {
        $articles = Article::where('status', 'active')
            ->latest()
            ->get();

        return response()->json($articles);
    }
}
