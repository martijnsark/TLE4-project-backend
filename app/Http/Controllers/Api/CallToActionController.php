<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CallToActionController extends Controller
{
    public function store(Request $request, Article $article): JsonResponse
    //maak hier een cta aan voor een artikel, maar alleen als er nog geen cta is, anders return een error(max 1 cta per artikel)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'context_text' => ['required', 'string'],
            'goal_text' => ['required', 'string'],
            'target_url' => ['required', 'url'],
        ]);

        if ($article->callToAction()->exists()) {
            return response()->json([
                'message' => 'This article already has a call to action.',
            ], 409);
        }

        $callToAction = $article->callToAction()->create([
            'title' => $request->title,
            'context_text' => $request->context_text,
            'goal_text' => $request->goal_text,
            'target_url' => $request->target_url,
        ]);

        return response()->json([
            'message' => 'Call to action created successfully',
            'call_to_action' => $callToAction,
        ], 201);
    }

    public function update(Request $request, Article $article): JsonResponse
    //update een cta voor een artikel, maar alleen als er al een cta is, anders return een error(kan geen cta updaten als er nog geen cta is)
    {
        $callToAction = $article->callToAction;

        if (! $callToAction) {
            return response()->json([
                'message' => 'This article does not have a call to action yet.',
            ], 404);
        }

        $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'context_text' => ['sometimes', 'string'],
            'goal_text' => ['sometimes', 'string'],
            'target_url' => ['sometimes', 'url'],
        ]);

        $callToAction->update($request->only([
            'title',
            'context_text',
            'goal_text',
            'target_url',
        ]));

        return response()->json([
            'message' => 'Call to action updated successfully',
            'call_to_action' => $callToAction->fresh(),
        ]);
    }

    public function destroy(Article $article): JsonResponse
    //verwijderd een cta voor een artikel, maar alleen als er al een cta is, anders return een error(kan geen cta verwijderen als er nog geen cta is)
    {
        $callToAction = $article->callToAction;

        if (! $callToAction) {
            return response()->json([
                'message' => 'This article does not have a call to action.',
            ], 404);
        }

        $callToAction->delete();

        return response()->json([
            'message' => 'Call to action deleted successfully',
        ]);
    }
}
