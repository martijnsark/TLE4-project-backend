<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(): JsonResponse
    //alle tags ophalen
    {
        $tags = Tag::orderBy('category')
            ->orderBy('name')
            ->get();

        return response()->json($tags);
    }

    public function myTags(Request $request): JsonResponse
    //tags ophalen die de gebruiker heeft geselecteerd als interesse tags, gesorteerd op categorie en naam
    {
        $tags = $request->user()
            ->interestTags()
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return response()->json($tags);
    }

    public function updateMyTags(Request $request): JsonResponse
    //tags updaten die de gebruiker heeft geselecteerd als interesse tags, gesorteerd op categorie en naam
    {
        $validated = $request->validate([
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
        ]);

        $request->user()->interestTags()->sync($validated['tag_ids']);

        return response()->json([
            'message' => 'Tag preferences updated successfully',
            'tags' => $request->user()
                ->interestTags()
                ->orderBy('category')
                ->orderBy('name')
                ->get(),
        ]);
    }
}
