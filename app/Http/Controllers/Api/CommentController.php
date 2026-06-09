<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\CommentTemplate;
use App\Models\Meme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    private function listComments(Model $commentable): JsonResponse
    {
        $comments = $commentable->comments()
            ->with(['user:id,username,name', 'template:id,text'])
            ->latest()
            ->get();

        return response()->json($comments);
    }

    private function storeComment(Request $request, Model $commentable): JsonResponse
    {
        $validated = $request->validate([
            'comment_template_id' => ['required', 'integer', 'exists:comment_templates,id'],
        ]);

        $template = CommentTemplate::where('id', $validated['comment_template_id'])
            ->where('is_active', true)
            ->first();

        if (! $template) {
            return response()->json([
                'message' => 'This comment template is not active or does not exist.',
            ], 422);
        }

        $comment = $commentable->comments()->create([
            'user_id' => $request->user()->id,
            'comment_template_id' => $template->id,
        ]);

        return response()->json([
            'message' => 'Comment placed successfully',
            'comment' => $comment->load(['user:id,username,name', 'template:id,text']),
        ], 201);
    }

    public function articleIndex(Article $article): JsonResponse
    {
        return $this->listComments($article);
    }

    public function articleStore(Request $request, Article $article): JsonResponse
    {
        return $this->storeComment($request, $article);
    }

    public function memeIndex(Meme $meme): JsonResponse
    {
        return $this->listComments($meme);
    }

    public function memeStore(Request $request, Meme $meme): JsonResponse
    {
        return $this->storeComment($request, $meme);
    }

    public function destroy(Request $request, \App\Models\Comment $comment): JsonResponse
    {
        if ($comment->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
        ]);
    }
}
