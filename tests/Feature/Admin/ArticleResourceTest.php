<?php

use App\Filament\Resources\Articles\Pages\CreateArticle;
use App\Filament\Resources\Articles\Pages\EditArticle;
use App\Filament\Resources\Articles\Pages\ListArticles;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->actingAs($this->admin);
});

it('lists articles in the table', function () {
    $articles = Article::factory()->count(3)->create();

    \Livewire\Livewire::test(ListArticles::class)
        ->assertCanSeeTableRecords($articles);
});

it('creates an article with body paragraphs and tone', function () {
    \Livewire\Livewire::test(CreateArticle::class)
        ->fillForm([
            'title'           => 'Test artikel',
            'summary'         => 'Een korte samenvatting.',
            'body_paragraphs' => [['value' => 'Eerste paragraaf.']],
            'original_url'    => 'https://example.com/artikel',
            'tone'            => 'Live',
            'status'          => 'draft',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $article = Article::latest()->first();
    expect($article->title)->toBe('Test artikel');
    expect($article->tone)->toBe('Live');
    expect($article->body_paragraphs)->toBe([['value' => 'Eerste paragraaf.']]);
});

it('validates required fields on create', function () {
    \Livewire\Livewire::test(CreateArticle::class)
        ->fillForm(['title' => ''])
        ->call('create')
        ->assertHasFormErrors(['title' => 'required']);
});

it('koppelt trending-tag via tag-select op een artikel', function () {
    $article     = Article::factory()->create();
    $trendingTag = Tag::firstOrCreate(['name' => 'Trending'], ['category' => 'flag']);

    \Livewire\Livewire::test(EditArticle::class, ['record' => $article->getKey()])
        ->fillForm(['tags' => [$trendingTag->id]])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($article->fresh()->is_trending)->toBeTrue();
});

it('updates article tags via the relationship select', function () {
    $article = Article::factory()->create();
    $tag = Tag::create(['name' => 'TestTag']);

    \Livewire\Livewire::test(EditArticle::class, ['record' => $article->getKey()])
        ->fillForm(['tags' => [$tag->id]])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($article->fresh()->tags->pluck('id'))->toContain($tag->id);
});

it('persists the selected author when creating an article', function () {
    $author = User::factory()->create();

    \Livewire\Livewire::test(CreateArticle::class)
        ->fillForm([
            'title'           => 'Met expliciete auteur',
            'summary'         => 'samenvatting',
            'body_paragraphs' => [['value' => 'p']],
            'original_url'    => 'https://example.com/auteur',
            'tone'            => 'Live',
            'status'          => 'draft',
            'author_id'       => $author->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Article::latest()->first()->author_id)->toBe($author->id);
});

it('deletes an article via the edit-page header action', function () {
    $article = Article::factory()->create();

    \Livewire\Livewire::test(EditArticle::class, ['record' => $article->getKey()])
        ->callAction('delete');

    expect(Article::find($article->id))->toBeNull();
});
