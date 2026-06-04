<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Meme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemeController extends Controller
{
    //alle memes ophalen van nieuw naar oud, inclusief de bijbehorende artikelen
    public function index(): JsonResponse
    {
        $memes = Meme::with('article:id,title')
            ->latest()
            ->paginate(12);

        return response()->json($memes);
    }
    //een specifieke meme ophalen, inclusief het bijbehorende artikel
    public function show(Meme $meme): JsonResponse
    {
        return response()->json(
            $meme->load('article')
        );
    }
    //meme aanmaken voor een artikel
    public function store(Request $request, Article $article): JsonResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image_url' => ['required', 'url'],
            'caption' => ['nullable', 'string'],
        ]);

        $meme = $article->memes()->create([
            'title' => $request->title,
            'image_url' => $request->image_url,
            'caption' => $request->caption,
        ]);

        return response()->json([
            'message' => 'Meme created successfully',
            'meme' => $meme,
        ], 201);
    }

    //edit bestaande meme
    public function update(Request $request, Meme $meme): JsonResponse
    {
        $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'image_url' => ['sometimes', 'url'],
            'caption' => ['sometimes', 'nullable', 'string'],
        ]);

        $meme->update($request->only([
            'title',
            'image_url',
            'caption',
        ]));

        return response()->json([
            'message' => 'Meme updated successfully',
            'meme' => $meme->fresh(),
        ]);
    }

    //meme banishen van realiteit
    public function destroy(Meme $meme): JsonResponse
    {
        $meme->delete();

        return response()->json([
            'message' => 'Meme deleted successfully',
        ]);
    }
}
