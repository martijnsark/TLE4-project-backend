<?php

use App\Filament\Resources\Articles\Pages\EditArticle;
use App\Filament\Resources\Articles\RelationManagers\SourceRelationManager;
use App\Models\Article;
use App\Models\Source;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
    $this->article = Article::factory()->create();
});

it('admin kan een nieuwe source aanmaken en koppelen via het artikel', function () {
    \Livewire\Livewire::test(SourceRelationManager::class, [
        'ownerRecord' => $this->article,
        'pageClass'   => EditArticle::class,
    ])
        ->callTableAction('create', data: [
            'name'              => 'NOS',
            'url'               => 'https://nos.nl',
            'reliability_score' => 90,
            'source_url'        => 'https://nos.nl/klimaat',
            'is_primary'        => true,
        ])
        ->assertHasNoTableActionErrors();

    $source = Source::where('name', 'NOS')->first();

    expect($source)->not->toBeNull()
        ->and($this->article->sources()->where('source_id', $source->id)->exists())->toBeTrue()
        ->and($this->article->sources()->first()->pivot->source_url)->toBe('https://nos.nl/klimaat')
        ->and((bool) $this->article->sources()->first()->pivot->is_primary)->toBeTrue();
});

it('admin kan een bestaande source ontkoppelen via delete', function () {
    $source = Source::factory()->create();
    $this->article->sources()->attach($source->id, [
        'source_url' => 'https://nos.nl',
        'is_primary' => false,
    ]);

    \Livewire\Livewire::test(SourceRelationManager::class, [
        'ownerRecord' => $this->article,
        'pageClass'   => EditArticle::class,
    ])
        ->callTableAction('delete', $source)
        ->assertHasNoTableActionErrors();

    expect($this->article->sources()->where('sources.id', $source->id)->exists())->toBeFalse();
});

it('valideert verplichte velden bij create', function () {
    \Livewire\Livewire::test(SourceRelationManager::class, [
        'ownerRecord' => $this->article,
        'pageClass'   => EditArticle::class,
    ])
        ->callTableAction('create', data: [
            'name'       => '',
            'url'        => '',
            'source_url' => '',
        ])
        ->assertHasTableActionErrors([
            'name'       => 'required',
            'url'        => 'required',
            'source_url' => 'required',
        ]);
});
