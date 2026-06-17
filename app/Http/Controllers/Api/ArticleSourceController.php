<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Source;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ArticleSourceController extends Controller
{
    //get all sources linked to an article, sorted by primary sources first
    public function index(Article $article): JsonResponse
    {
        $sources = $article->sources()
            ->orderByDesc('article_sources.is_primary')
            ->get();

        return response()->json($sources);
    }

    //link a source to an article, requires source_id, source_url and optional is_primary
    public function store(Request $request, Article $article): JsonResponse
    {
        Gate::authorize('create-article-source');
        $validated = $request->validate([
            'source_id' => ['required', 'integer', 'exists:sources,id'],
            'source_url' => ['required', 'url'],
            'is_primary' => ['sometimes', 'boolean'],
        ]);

        $source = Source::findOrFail($validated['source_id']);

        if ($article->sources()->where('sources.id', $source->id)->exists()) {
            return response()->json([
                'message' => 'This source is already linked to this article.',
            ], 409);
        }

        $article->sources()->attach($source->id, [
            'source_url' => $validated['source_url'],
            'is_primary' => $validated['is_primary'] ?? false,
        ]);

        return response()->json([
            'message' => 'Source linked to article successfully',
            'source' => $article->sources()
                ->where('sources.id', $source->id)
                ->first(),
        ], 201);
    }

    //update the pivot data for a source linked to an article, can update source_url and is_primary
    public function update(Request $request, Article $article, Source $source): JsonResponse
    {
        Gate::authorize('update-article-source');
        if (! $article->sources()->where('sources.id', $source->id)->exists()) {
            return response()->json([
                'message' => 'This source is not linked to this article.',
            ], 404);
        }

        $validated = $request->validate([
            'source_url' => ['sometimes', 'url'],
            'is_primary' => ['sometimes', 'boolean'],
        ]);

        $article->sources()->updateExistingPivot($source->id, $validated);

        return response()->json([
            'message' => 'Article source updated successfully',
            'source' => $article->sources()
                ->where('sources.id', $source->id)
                ->first(),
        ]);
    }

    //unlink a source from an article
    public function destroy(Article $article, Source $source): JsonResponse
    {
        Gate::authorize('delete-article-source');
        if (! $article->sources()->where('sources.id', $source->id)->exists()) {
            return response()->json([
                'message' => 'This source is not linked to this article.',
            ], 404);
        }

        $article->sources()->detach($source->id);

        return response()->json([
            'message' => 'Source removed from article successfully',
        ]);
    }
}
