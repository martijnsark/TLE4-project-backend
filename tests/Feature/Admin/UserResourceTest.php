<?php

use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

it('lists users in the table', function () {
    $users = User::factory()->count(3)->create();

    \Livewire\Livewire::test(ListUsers::class)
        ->assertCanSeeTableRecords($users);
});

it('changes a user role to admin', function () {
    $user = User::factory()->create(['role' => 'user']);

    \Livewire\Livewire::test(EditUser::class, ['record' => $user->getKey()])
        ->fillForm(['role' => 'admin'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->role)->toBe('admin');
});

it('does not expose a create page', function () {
    expect(array_keys(UserResource::getPages()))
        ->toContain('index', 'edit')
        ->not->toContain('create');
});
