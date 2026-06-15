<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Meme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MemeController extends Controller
{
    //get all memes, including the title of the associated article, sorted from new to old, paginated
    public function index(): JsonResponse
    {
        $memes = Meme::with('article:id,title')
            ->latest()
            ->paginate(12);

        return response()->json($memes);
    }
    //get single meme, including the title of the associated article
    public function show(Meme $meme): JsonResponse
    {
        return response()->json(
            $meme->load('article')
        );
    }
    //create a meme for a specific article, requires title, image_url and optional caption
    public function store(Request $request, Article $article): JsonResponse
    {
        Gate::authorize('create', Meme::class);
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

    //edit existing meme, can update title, image_url and caption
    public function update(Request $request, Meme $meme): JsonResponse
    {
        Gate::authorize('update', $meme);
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

    //delete a meme
    public function destroy(Meme $meme): JsonResponse
    {
        Gate::authorize('delete', $meme);
        $meme->delete();

        return response()->json([
            'message' => 'Meme deleted successfully',
        ]);
    }
}
