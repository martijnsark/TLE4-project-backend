<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Source;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleSourceController extends Controller
{
    /**
     * Haalt alle bronnen op die gekoppeld zijn aan een artikel.
     *
     * Dit kan de frontend gebruiken om onder een artikel
     * een bronnenoverzicht te tonen.
     */
    public function index(Article $article): JsonResponse
    {
        $sources = $article->sources()
            ->orderByDesc('article_sources.is_primary')
            ->get();

        return response()->json($sources);
    }

    /**
     * Koppelt een bron aan een artikel.
     *
     * De bron zelf bestaat al in de sources tabel.
     * Hier slaan we de specifieke URL van het bronartikel op
     * in de koppeltabel article_sources.
     */
    public function store(Request $request, Article $article): JsonResponse
    {
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

    /**
     * Past de bronkoppeling van een artikel aan.
     *
     * Dit update niet de source zelf, maar alleen de gegevens
     * in de article_sources koppeltabel.
     */
    public function update(Request $request, Article $article, Source $source): JsonResponse
    {
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

    /**
     * Verwijdert een bronkoppeling van een artikel.
     *
     * Dit verwijdert alleen de koppeling in article_sources,
     * niet de source uit de sources tabel.
     */
    public function destroy(Article $article, Source $source): JsonResponse
    {
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
