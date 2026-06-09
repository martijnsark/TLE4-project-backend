<?php

use App\Filament\Resources\Articles\Pages\EditArticle;
use App\Filament\Resources\Articles\RelationManagers\PollRelationManager;
use App\Models\Article;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
    $this->article = Article::factory()->create();
});

it('admin kan een poll aanmaken', function () {
    \Livewire\Livewire::test(PollRelationManager::class, [
        'ownerRecord' => $this->article,
        'pageClass'   => EditArticle::class,
    ])
        ->callTableAction('create', null, [
            'question' => 'Wat kies jij?',
        ])
        ->assertHasNoTableActionErrors();

    expect(Poll::where('article_id', $this->article->id)->where('question', 'Wat kies jij?')->exists())->toBeTrue();
});

it('admin kan een poll verwijderen', function () {
    $poll = Poll::create([
        'article_id' => $this->article->id,
        'question'   => 'Te verwijderen?',
    ]);
    PollOption::create(['poll_id' => $poll->id, 'option_text' => 'Ja']);
    PollOption::create(['poll_id' => $poll->id, 'option_text' => 'Nee']);

    \Livewire\Livewire::test(PollRelationManager::class, [
        'ownerRecord' => $this->article,
        'pageClass'   => EditArticle::class,
    ])
        ->callTableAction('delete', $poll)
        ->assertHasNoTableActionErrors();

    expect(Poll::find($poll->id))->toBeNull();
    expect(PollOption::where('poll_id', $poll->id)->count())->toBe(0);
});
