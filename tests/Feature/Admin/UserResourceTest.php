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

it('creates a user via the table create modal with username + password', function () {
    \Livewire\Livewire::test(ListUsers::class)
        ->callAction('create', data: [
            'username' => 'fresh_admin',
            'name'     => 'Fresh Admin',
            'email'    => 'fresh@impakt.test',
            'password' => 'supersecret',
            'role'     => 'user',
        ])
        ->assertHasNoActionErrors();

    $user = User::where('email', 'fresh@impakt.test')->first();
    expect($user)->not->toBeNull();
    expect($user->username)->toBe('fresh_admin');
    expect(\Illuminate\Support\Facades\Hash::check('supersecret', $user->password))->toBeTrue();
});

it('keeps existing password when edit submits with empty password field', function () {
    $user = User::factory()->create(['password' => \Illuminate\Support\Facades\Hash::make('oldsecret')]);
    $originalHash = $user->password;

    \Livewire\Livewire::test(EditUser::class, ['record' => $user->getKey()])
        ->fillForm([
            'name'     => 'Updated Name',
            'email'    => $user->email,
            'password' => '',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->password)->toBe($originalHash);
    expect($user->fresh()->name)->toBe('Updated Name');
});

it('disables username on edit so it cannot be changed', function () {
    $user = User::factory()->create(['username' => 'original_handle']);

    \Livewire\Livewire::test(EditUser::class, ['record' => $user->getKey()])
        ->fillForm(['username' => 'tampered_handle'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->username)->toBe('original_handle');
});
