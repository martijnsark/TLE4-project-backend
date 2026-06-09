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
        ]);

        $view = new MemeView();
        $view->meme_id = $request->meme_id;
        $view->save();

        return response()->json([
            'message' => 'View opgeslagen'
        ]);
    }
}
