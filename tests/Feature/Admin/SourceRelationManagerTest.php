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

it('admin kan een bestaande source koppelen aan een artikel', function () {
    $source = Source::factory()->create();

    \Livewire\Livewire::test(SourceRelationManager::class, [
        'ownerRecord' => $this->article,
        'pageClass'   => EditArticle::class,
    ])
        ->callTableAction('attach', data: [
            'recordId'   => $source->id,
            'source_url' => 'https://nos.nl/klimaat',
            'is_primary' => true,
        ])
        ->assertHasNoTableActionErrors();

    expect($this->article->sources()->where('source_id', $source->id)->exists())->toBeTrue();
});

it('admin kan een source ontkoppelen van een artikel', function () {
    $source = Source::factory()->create();
    $this->article->sources()->attach($source->id, [
        'source_url' => 'https://nos.nl',
        'is_primary' => false,
    ]);

    \Livewire\Livewire::test(SourceRelationManager::class, [
        'ownerRecord' => $this->article,
        'pageClass'   => EditArticle::class,
    ])
        ->callTableAction('detach', $source)
        ->assertHasNoTableActionErrors();

    expect($this->article->sources()->where('sources.id', $source->id)->exists())->toBeFalse();
});
