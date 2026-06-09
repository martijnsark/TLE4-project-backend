<?php

use App\Filament\Resources\Sources\Pages\CreateSource;
use App\Filament\Resources\Sources\Pages\EditSource;
use App\Filament\Resources\Sources\Pages\ListSources;
use App\Filament\Resources\Sources\SourceResource;
use App\Models\Source;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

it('lists sources in the table', function () {
    $sources = Source::factory()->count(3)->create();

    \Livewire\Livewire::test(ListSources::class)
        ->assertCanSeeTableRecords($sources);
});

it('creates a source via admin', function () {
    \Livewire\Livewire::test(CreateSource::class)
        ->fillForm([
            'name'              => 'NOS',
            'url'               => 'https://nos.nl',
            'reliability_score' => 90,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Source::where('name', 'NOS')->first())
        ->not->toBeNull()
        ->and(Source::where('name', 'NOS')->first()->url)->toBe('https://nos.nl')
        ->and(Source::where('name', 'NOS')->first()->reliability_score)->toBe(90);
});

it('requires name and url on create', function () {
    \Livewire\Livewire::test(CreateSource::class)
        ->fillForm(['name' => '', 'url' => ''])
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'url'  => 'required',
        ]);
});

it('rejects an invalid url', function () {
    \Livewire\Livewire::test(CreateSource::class)
        ->fillForm([
            'name' => 'NOS',
            'url'  => 'niet-een-url',
        ])
        ->call('create')
        ->assertHasFormErrors(['url']);
});

it('updates reliability_score via edit', function () {
    $source = Source::factory()->create(['reliability_score' => 50]);

    \Livewire\Livewire::test(EditSource::class, ['record' => $source->getKey()])
        ->fillForm(['reliability_score' => 95])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($source->fresh()->reliability_score)->toBe(95);
});

it('deletes a source via the edit page action', function () {
    $source = Source::factory()->create();

    \Livewire\Livewire::test(EditSource::class, ['record' => $source->getKey()])
        ->callAction('delete');

    expect(Source::find($source->id))->toBeNull();
});

it('exposes only index/create/edit pages', function () {
    expect(array_keys(SourceResource::getPages()))
        ->toContain('index', 'create', 'edit');
});
