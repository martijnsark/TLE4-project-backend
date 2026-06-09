<?php

use App\Filament\Resources\Articles\Pages\EditArticle;
use App\Filament\Resources\Articles\RelationManagers\CallToActionRelationManager;
use App\Models\Article;
use App\Models\CallToAction;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
    $this->article = Article::factory()->create();
});

it('admin kan een cta aanmaken op een artikel', function () {
    \Livewire\Livewire::test(CallToActionRelationManager::class, [
        'ownerRecord' => $this->article,
        'pageClass'   => EditArticle::class,
    ])
        ->callTableAction('create', null, [
            'title'        => 'Doe iets goeds',
            'context_text' => 'Achtergrond context.',
            'goal_text'    => 'Help mee.',
            'target_url'   => 'https://example.com',
        ])
        ->assertHasNoTableActionErrors();

    expect($this->article->callToAction()->where('title', 'Doe iets goeds')->exists())->toBeTrue();
});

it('admin kan een cta bewerken', function () {
    $cta = CallToAction::create([
        'article_id'   => $this->article->id,
        'title'        => 'Origineel',
        'context_text' => 'Context.',
        'goal_text'    => 'Doel.',
        'target_url'   => 'https://example.com',
    ]);

    \Livewire\Livewire::test(CallToActionRelationManager::class, [
        'ownerRecord' => $this->article,
        'pageClass'   => EditArticle::class,
    ])
        ->callTableAction('edit', $cta, [
            'title'        => 'Aangepast',
            'context_text' => 'Context.',
            'goal_text'    => 'Doel.',
            'target_url'   => 'https://example.com',
        ])
        ->assertHasNoTableActionErrors();

    expect($cta->fresh()->title)->toBe('Aangepast');
});

it('admin kan een cta verwijderen', function () {
    $cta = CallToAction::create([
        'article_id'   => $this->article->id,
        'title'        => 'Te verwijderen',
        'context_text' => 'Context.',
        'goal_text'    => 'Doel.',
        'target_url'   => 'https://example.com',
    ]);

    \Livewire\Livewire::test(CallToActionRelationManager::class, [
        'ownerRecord' => $this->article,
        'pageClass'   => EditArticle::class,
    ])
        ->callTableAction('delete', $cta)
        ->assertHasNoTableActionErrors();

    expect(CallToAction::find($cta->id))->toBeNull();
});
