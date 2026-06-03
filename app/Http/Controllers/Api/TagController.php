<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(): JsonResponse
    {
        $tags = Tag::orderBy('category')
            ->orderBy('name')
            ->get();

        return response()->json($tags);
    }

    public function myTags(Request $request): JsonResponse
    {
        $tags = $request->user()
            ->interestTags()
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return response()->json($tags);
    }

    public function updateMyTags(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tag_ids' => ['required', 'array'],
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
