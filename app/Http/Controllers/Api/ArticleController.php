<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::paginate(6);

        return view('articles.index', compact('articles'));
    }

    public function create()
    {
        return view('articles.create');
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

        return redirect()->route('articles.index', $article->id);
    }

    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        if ($article->author_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        return view('articles.edit', compact('article'));
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

        return redirect()->route('articles.index');
    }

    public function destroy(Article $article)
    {
        if ($article->author_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $article->delete();

        return redirect()->route('articles.index');
    }
}
