<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemeView;
use Illuminate\Http\Request;

class MemeViewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'meme_id' => 'required|exists:memes,id',
            'user_id' => 'required|exists:users,id',
            'viewing_time_seconds' => 'required|integer|min:0',
        ]);

        $view = new MemeView();
        $view->meme_id = $request->meme_id;
        $view->user_id = $request->user_id;
        $view->viewing_time_seconds = $request->viewing_time_seconds;
        $view->save();

        return response()->json([
            'message' => 'View opgeslagen'
        ]);
    }
}
