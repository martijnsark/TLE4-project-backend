<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    //get all articles, admins see all articles, regular users only see active articles, can search with title, summary or content, can filter with tag id or tag name, can filter with date range, can sort with latest, oldest or most viewed
    public function index(Request $request)
    {
        // Admins see all articles, regular users only see active articles
        if (auth()->check() && auth()->user()->role === 'admin') {
            $articles = Article::query();
        } else {
            $articles = Article::where('status', 'active');
        }

        $articles = $articles
            ->with(['tags', 'callToAction', 'reactions', 'polls.options.votes', 'sources', 'memes'])
            ->withCount('views');

        // Search with title, summary or content
        if ($request->filled('search')) {
            $search = $request->input('search');

            $articles->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('summary', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        // Filter with tag id
        if ($request->filled('tag_id')) {
            $articles->whereHas('tags', function ($query) use ($request) {
                $query->where('tags.id', $request->tag_id);
            });
        }

        // Filter with tag name
        if ($request->filled('tag')) {
            $articles->whereHas('tags', function ($query) use ($request) {
                $query->where('tags.name', $request->tag);
            });
        }

        // Filter from date
        if ($request->filled('date_from')) {
            $articles->whereDate('published_at', '>=', $request->input('date_from'));
        }

        // Filter with date
        if ($request->filled('date_to')) {
            $articles->whereDate('published_at', '<=', $request->input('date_to'));
        }

        // Sort
        match ($request->input('sort', 'latest')) {
            'oldest' => $articles
                ->orderBy('published_at')
                ->orderBy('created_at'),

            'views' => $articles
                ->orderByDesc('views_count')
                ->orderByDesc('published_at')
                ->orderByDesc('created_at'),

            'latest' => $articles
                ->orderByDesc('published_at')
                ->orderByDesc('created_at'),

            default => $articles
                ->orderByDesc('published_at')
                ->orderByDesc('created_at'),
        };

        return ArticleResource::collection($articles->paginate(6));
    }
    //create an article, only the author or admin can create an article
    public function create()
    {
        return response()->json(['message' => 'Create article success']);
    }
    //store an article, requires title, summary, content, image_url, tone and status, also can include published_at and tags associated with the article, only the author or admin can create an article
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'content' => 'nullable|string',
            'image_url' => 'required|string',
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
    //get single article
    public function show(Article $article)
    {
        if (
            (! auth()->check() || auth()->user()->role !== 'admin')
            && $article->status !== 'active'
        ) {
            abort(403, 'Unauthorized action.');
        }

        $article->views()->create([
            'user_id' => auth()->id(),
            'viewed_at' => now(),
        ]);

        return new ArticleResource(
            $article
                ->load(['tags', 'callToAction', 'reactions', 'polls.options.votes', 'sources', 'memes'])
                ->loadCount('views')
        );
    }
    //edit an article
    public function edit(Article $article)
    {
        if ($article->author_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        return response()->json(['message' => 'Update article success']);
    }
    //update an article, only the author or admin can update an article, can update title, summary, content, image_url, tone, status and published_at, also can update the tags associated with the article
    public function update(Request $request, Article $article)
    {
        if ($article->author_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'summary' => ['sometimes', 'string'],
            'content' => ['sometimes', 'string'],
            'image_url' => ['sometimes', 'string'],
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
            'tone',
            'status',
            'published_at',
        ]));

        if ($request->has('tag_ids')) {
            $article->tags()->sync($request->input('tag_ids'));
        }

        return response()->json($article->load(['tags', 'callToAction']));
    }
    //delete an article
    public function destroy(Article $article)
    {
        if ($article->author_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $article->delete();

        return response()->json(['message' => 'Delete article success']);
    }
    // happyfeed function
    public function happyFeed(Request $request)
    {
        // display articles if active and contains happy tag from new to old
        $articles = Article::query()
            ->where('status', 'active')
            ->whereHas('tags', function ($query) {
                $query->where('name', 'Goed nieuws');
            })
            ->with([
                'tags' => function ($query) {
                    $query->where('name', '!=', 'Goed nieuws');
                },
                'callToAction',
                'memes',
            ])
            ->withCount('views')
        ;

        if ($request->filled('tag_id')) {
            $articles->whereHas('tags', function ($query) use ($request) {
                $query->where('tags.id', $request->tag_id);
            });
        }

        if ($request->filled('tag')) {
            $articles->whereHas('tags', function ($query) use ($request) {
                $query->where('tags.name', $request->tag);
            });
        }

        $articles = $articles
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($articles);
    }

}
