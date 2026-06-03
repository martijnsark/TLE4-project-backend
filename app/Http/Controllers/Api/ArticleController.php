<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        //$articles = Article::query();

        // show latest & active articles
        $articles = Article::where('status', 'active')
            ->latest();


        //search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $articles->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('summary', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });
        }


        //$articles = Article::paginate(6);

        return response()->json($articles->paginate(6));
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

        return response()->json($article);
    }

    public function show(Article $article)
    {
        return response()->json($article);
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
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'content' => 'required|string',
            'image_url' => 'required|url',
            'original_url' => 'required|url',
            'tone' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'published_at' => 'nullable|date',
        ]);

        $article->update($request->all());

        $article->save();

        return response()->json($article);
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
