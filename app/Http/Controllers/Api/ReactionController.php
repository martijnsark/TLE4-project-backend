<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Meme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    //get reaction stats for an article or meme, including counts and the user's own reaction
    private function reactionStats(Request $request, Model $reactionable): JsonResponse
    {
        $reactionCounts = $reactionable->reactions()
            ->selectRaw('reaction, COUNT(*) as count')
            ->groupBy('reaction')
            ->pluck('count', 'reaction');

        $myReaction = null;

        if ($request->user()) {
            $myReaction = $reactionable->reactions()
                ->where('user_id', $request->user()->id)
                ->first();
        }

        return response()->json([
            'counts' => [
                'happy' => $reactionCounts['happy'] ?? 0,
                'shocked' => $reactionCounts['shocked'] ?? 0,
                'sad' => $reactionCounts['sad'] ?? 0,
            ],
            'my_reaction' => $myReaction,
        ]);
    }
    // Save or update the user's reaction to an article or meme
    private function saveReaction(Request $request, Model $reactionable): JsonResponse
    {
        $validated = $request->validate([
            'reaction' => ['required', 'in:happy,shocked,sad'],
        ]);

        $reaction = $reactionable->reactions()->updateOrCreate(
            [
                'user_id' => $request->user()->id,
            ],
            [
                'reaction' => $validated['reaction'],
            ]
        );

        return response()->json([
            'message' => 'Reaction saved successfully',
            'reaction' => $reaction,
        ]);
    }
    // Delete the user's reaction to an article or meme
    private function deleteReaction(Request $request, Model $reactionable): JsonResponse
    {
        $reaction = $reactionable->reactions()
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $reaction) {
            return response()->json([
                'message' => 'You have not reacted yet.',
            ], 404);
        }

        $reaction->delete();

        return response()->json([
            'message' => 'Reaction removed successfully',
        ]);
    }

    //morphic endpoints for articles and memes (prevents code duplication, uses the same private functions for both types of reactionables)

    public function articleIndex(Request $request, Article $article): JsonResponse
    {
        return $this->reactionStats($request, $article);
    }

    public function articleStore(Request $request, Article $article): JsonResponse
    {
        return $this->saveReaction($request, $article);
    }

    public function articleDestroy(Request $request, Article $article): JsonResponse
    {
        return $this->deleteReaction($request, $article);
    }

    public function memeIndex(Request $request, Meme $meme): JsonResponse
    {
        return $this->reactionStats($request, $meme);
    }

    public function memeStore(Request $request, Meme $meme): JsonResponse
    {
        return $this->saveReaction($request, $meme);
    }

    public function memeDestroy(Request $request, Meme $meme): JsonResponse
    {
        return $this->deleteReaction($request, $meme);
    }
}
