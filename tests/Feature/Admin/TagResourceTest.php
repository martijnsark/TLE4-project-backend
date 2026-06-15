<?php

use App\Enums\TagCategory;
use App\Enums\TagIcon;
use App\Filament\Resources\Tags\Pages\CreateTag;
use App\Filament\Resources\Tags\Pages\EditTag;
use App\Filament\Resources\Tags\Pages\ListTags;
use App\Models\Tag;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

it('lists tags in the table', function () {
    $tags = Tag::factory()->count(3)->create();

    \Livewire\Livewire::test(ListTags::class)
        ->assertCanSeeTableRecords($tags);
});

it('creates a navigation tag via admin', function () {
    \Livewire\Livewire::test(CreateTag::class)
        ->fillForm([
            'name'     => 'Klimaat',
            'category' => 'navigation',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Tag::where('name', 'Klimaat')->first())
        ->not->toBeNull()
        ->and(Tag::where('name', 'Klimaat')->first()->category)->toBe(TagCategory::Navigation);
});

it('creates a tag with an icon via admin', function () {
    \Livewire\Livewire::test(CreateTag::class)
        ->fillForm([
            'name'     => 'Sport',
            'category' => 'navigation',
            'icon'     => 'trend',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Tag::where('name', 'Sport')->first()->icon)->toBe(TagIcon::Trend);
});

it('requires a name on create', function () {
    \Livewire\Livewire::test(CreateTag::class)
        ->fillForm(['name' => '', 'category' => 'topic'])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

it('updates a tag category via edit', function () {
    $tag = Tag::create(['name' => 'Goed nieuws', 'category' => 'topic']);

    \Livewire\Livewire::test(EditTag::class, ['record' => $tag->getKey()])
        ->fillForm(['category' => 'flag'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($tag->fresh()->category)->toBe(TagCategory::Flag);
});

it('deletes a tag via the edit page action', function () {
    $tag = Tag::create(['name' => 'Tijdelijk', 'category' => 'topic']);

    \Livewire\Livewire::test(EditTag::class, ['record' => $tag->getKey()])
        ->callAction('delete');

    expect(Tag::find($tag->id))->toBeNull();
});
