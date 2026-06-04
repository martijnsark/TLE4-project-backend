<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            $articles = Article::query();
        } else {
            $articles = Article::where('status', 'active');
        }

        $articles = $articles
            ->with(['category', 'tags', 'callToAction', 'reactions', 'polls.options.votes', 'sources', 'views'])
            ->latest()
            ->when($request->filled('tag_id'), function ($query) use ($request) {
                $query->whereHas('tags', function ($tagQuery) use ($request) {
                    $tagQuery->where('tags.id', $request->tag_id);
                });
            })
            ->when($request->filled('tag'), function ($query) use ($request) {
                $query->whereHas('tags', function ($tagQuery) use ($request) {
                    $tagQuery->where('tags.name', $request->tag);
                });
            });

        if ($request->filled('search')) {
            $search = $request->input('search');

            $articles->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('summary', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        return ArticleResource::collection($articles->paginate(6));
    }

    public function create()
    {
        return response()->json(['message' => 'Create article success']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'content' => 'required|string',
            'image_url' => 'required|url',
            'original_url' => 'required|url',
            'tone' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'published_at' => 'nullable|date',

            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
        ]);

        $article = new Article();
        $article->title = $request->input('title');
        $article->summary = $request->input('summary');
        $article->content = $request->input('content');
        $article->image_url = $request->input('image_url');
        $article->original_url = $request->input('original_url');
        $article->tone = $request->input('tone');
        $article->status = $request->input('status');
        $article->published_at = $request->input('published_at');
        $article->author_id = auth()->id();
        $article->save();

        if ($request->has('tag_ids')) {
            $article->tags()->sync($request->input('tag_ids'));
        }

        return response()->json($article->load(['tags', 'callToAction']), 201);
    }

    public function show(Article $article)
    {
        // just show active for every one
        if (
            (!auth()->check() || auth()->user()->role !== 'admin')
            && $article->status !== 'active'
        ) {
            abort(403, 'Unauthorized action.');
        }

        return new ArticleResource($article->load(['category', 'tags', 'callToAction', 'reactions', 'polls.options.votes', 'sources', 'views']));
    }

    public function edit(Article $article)
    {
        if ($article->author_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        return response()->json(['message' => 'Update article success']);
    }

    public function update(Request $request, Article $article)
    {
        if ($article->author_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'summary' => ['sometimes', 'string'],
            'content' => ['sometimes', 'string'],
            'image_url' => ['sometimes', 'url'],
            'original_url' => ['sometimes', 'url'],
            'tone' => ['sometimes', 'in:Live,Achtergrond,Reportage,Opinie'],
            'status' => ['sometimes', 'in:draft,inactive,active,archived'],
            'published_at' => ['sometimes', 'nullable', 'date'],

            'tag_ids' => ['sometimes', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
        ]);

        $article->update($request->only([
            'title',
            'summary',
            'content',
            'image_url',
            'original_url',
            'tone',
            'status',
            'published_at',
        ]));

        if ($request->has('tag_ids')) {
            $article->tags()->sync($request->input('tag_ids'));
        }

        return response()->json($article->load(['tags', 'callToAction']));
    }

    public function destroy(Article $article)
    {
        if ($article->author_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $article->delete();

        return response()->json(['message' => 'Delete article success']);
    }
}
