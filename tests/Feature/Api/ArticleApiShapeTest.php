<?php

use App\Models\Article;
use App\Models\Reaction;
use App\Models\Tag;
use App\Models\User;

it('returns the RN-mock JSON shape for the article index', function () {
    $article = Article::factory()->create([
        'title'           => 'Test artikel',
        'summary'         => 'Korte samenvatting',
        'body_paragraphs' => [['value' => 'Paragraaf één.'], ['value' => 'Paragraaf twee.']],
        'tone'            => 'Live',
        'status'          => 'active',
        'published_at'    => now(),
    ]);

    $this->getJson('/api/articles')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id', 'title', 'sub', 'body', 'img', 'cat',
                    'tags', 'tone', 'trending', 'date', 'time',
                    'views', 'readers',
                    'reactions' => ['smile', 'meh', 'frown'],
                    'actions',
                    'sources',
                ],
            ],
            'links',
            'meta',
        ]);
});

it('maps summary to sub and body_paragraphs to body array', function () {
    $article = Article::factory()->create([
        'summary'         => 'Dit is de sub.',
        'body_paragraphs' => [['value' => 'Alinea 1.'], ['value' => 'Alinea 2.']],
        'status'          => 'active',
    ]);

    $this->getJson('/api/articles')
        ->assertOk()
        ->assertJsonPath('data.0.sub', 'Dit is de sub.')
        ->assertJsonPath('data.0.body', ['Alinea 1.', 'Alinea 2.']);
});

it('omits goodNews key when is_good_news is false', function () {
    Article::factory()->create(['status' => 'active']);

    $response = $this->getJson('/api/articles')->assertOk();
    expect(array_key_exists('goodNews', $response->json('data.0')))->toBeFalse();
});

it('includes goodNews true when is_good_news flag is set', function () {
    Article::factory()->goodNews()->create(['status' => 'active']);

    $this->getJson('/api/articles')
        ->assertOk()
        ->assertJsonPath('data.0.goodNews', true);
});

it('formats the published_at date and time in Dutch locale', function () {
    Article::factory()->create([
        'status'       => 'active',
        'published_at' => \Carbon\Carbon::parse('2026-06-04 14:30:00'),
    ]);

    $response = $this->getJson('/api/articles')->assertOk();

    $date = $response->json('data.0.date');
    $time = $response->json('data.0.time');

    expect($date)->toMatch('/^\d+ \w+ \d{4}$/');
    expect($time)->toBe('14:30');
});

it('hides non-active articles from unauthenticated users', function () {
    Article::factory()->draft()->create();

    $this->getJson('/api/articles')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('shows non-active articles to admin users', function () {
    $admin = User::factory()->admin()->create();
    Article::factory()->draft()->create();

    $this->actingAs($admin, 'sanctum')
        ->getJson('/api/articles')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('returns reaction structure with smile/meh/frown keys', function () {
    Article::factory()->create(['status' => 'active']);

    $response = $this->getJson('/api/articles')->assertOk();

    $reactions = $response->json('data.0.reactions');
    expect($reactions)->toHaveKeys(['smile', 'meh', 'frown']);
    expect($reactions['smile'])->toBeInt();
    expect($reactions['meh'])->toBeInt();
    expect($reactions['frown'])->toBeInt();
});

it('returns correct reaction counts grouped by smile/meh/frown', function () {
    $article = Article::factory()->create(['status' => 'active']);
    $u1      = User::factory()->create();
    $u2      = User::factory()->create();

    Reaction::create(['article_id' => $article->id, 'user_id' => $u1->id, 'reaction' => 'smile']);
    Reaction::create(['article_id' => $article->id, 'user_id' => $u2->id, 'reaction' => 'meh']);

    $this->getJson('/api/articles')
        ->assertOk()
        ->assertJsonPath('data.0.reactions.smile', 1)
        ->assertJsonPath('data.0.reactions.meh', 1)
        ->assertJsonPath('data.0.reactions.frown', 0);
});
