<?php

use App\Models\User;

it('redirects guests to the admin login page', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('forbids non-admin users from accessing the panel', function () {
    $user = User::factory()->create(); // role=user by default

    $this->actingAs($user)
        ->get('/admin')
        ->assertForbidden();
});

it('allows admin users to access the panel', function () {
    $admin = User::factory()->admin()->create();

    // /admin heeft geen dashboard meer — Filament redirect naar de eerste resource
    $this->actingAs($admin)
        ->get('/admin')
        ->assertRedirect();
});
