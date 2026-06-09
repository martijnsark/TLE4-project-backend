<?php

use App\Filament\Resources\Memes\Pages\CreateMeme;
use App\Filament\Resources\Memes\Pages\ListMemes;
use App\Filament\Resources\Memes\Schemas\MemeForm;
use App\Models\Article;
use App\Models\Meme;
use App\Models\Tag;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->actingAs($this->admin);
});

it('lists memes in the table', function () {
    $memes = Meme::factory()->count(2)->create();

    \Livewire\Livewire::test(ListMemes::class)
        ->assertCanSeeTableRecords($memes);
});

it('can create a meme record with article FK', function () {
    $article = Article::factory()->create(['author_id' => $this->admin->id]);

    $meme = Meme::create([
        'article_id'  => $article->id,
        'title'       => 'Test meme',
        'image_url'   => 'https://example.com/img.jpg',
        'author'      => '@testuser',
        'author_name' => 'Test Gebruiker',
        'top'         => 'Bovenste tekst',
        'bot'         => 'Onderste tekst',
        'cat'         => 'KLIMAAT',
    ]);

    expect($meme->article_id)->toBe($article->id);
    expect($meme->author)->toBe('@testuser');
    expect($meme->cat)->toBe('KLIMAAT');

    $this->assertDatabaseHas('memes', ['id' => $meme->id, 'article_id' => $article->id]);
});

it('renders the cat dropdown with uppercase navigation tag names', function () {
    Tag::firstOrCreate(['name' => 'Klimaat'], ['category' => 'navigation']);
    Tag::firstOrCreate(['name' => 'Tech'],    ['category' => 'navigation']);
    Tag::firstOrCreate(['name' => 'Trending'], ['category' => 'flag']);

    $schema = MemeForm::configure((new \Filament\Schemas\Schema(new \Filament\Resources\Pages\CreateRecord)));

    $catComponent = collect($schema->getComponents())
        ->first(fn ($component) => method_exists($component, 'getName') && $component->getName() === 'cat');

    $options = $catComponent->getOptions();

    expect($options)
        ->toHaveKey('KLIMAAT')
        ->toHaveKey('TECH')
        ->not->toHaveKey('TRENDING');
});

it('allows a meme with only an image and no title/top/bot', function () {
    $meme = Meme::create([
        'image_url' => 'memes/img.jpg',
    ]);

    expect($meme->fresh())
        ->title->toBeNull()
        ->top->toBeNull()
        ->bot->toBeNull()
        ->caption->toBeNull()
        ->image_url->toBe('memes/img.jpg');
});
