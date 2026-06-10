<?php

use App\Models\Article;
use App\Models\Source;
use App\Models\User;

it('lists sources publicly, sorted by name', function () {
    Source::factory()->create(['name' => 'Zeit']);
    Source::factory()->create(['name' => 'AD']);
    Source::factory()->create(['name' => 'NOS']);

    $response = $this->getJson('/api/sources')->assertOk();

    expect(collect($response->json())->pluck('name')->toArray())
        ->toBe(['AD', 'NOS', 'Zeit']);
});

it('requires authentication to create a source', function () {
    $this->postJson('/api/sources', [
        'name' => 'NOS',
        'url'  => 'https://nos.nl',
    ])->assertUnauthorized();
});

it('rejects source creation for non-admin users', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/sources', [
            'name' => 'NOS',
            'url'  => 'https://nos.nl',
        ])
        ->assertForbidden();
});

it('allows admin to create a source', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin, 'sanctum')
        ->postJson('/api/sources', [
            'name'              => 'NOS',
            'url'               => 'https://nos.nl',
            'reliability_score' => 95,
        ])
        ->assertCreated();

    expect(Source::where('name', 'NOS')->exists())->toBeTrue();
});

it('validates source creation payload', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin, 'sanctum')
        ->postJson('/api/sources', [
            'name' => '',
            'url'  => 'niet-een-url',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'url']);
});

it('allows admin to update a source', function () {
    $admin  = User::factory()->admin()->create();
    $source = Source::factory()->create(['name' => 'Oude naam']);

    $this->actingAs($admin, 'sanctum')
        ->patchJson("/api/sources/{$source->id}", ['name' => 'Nieuwe naam'])
        ->assertOk();

    expect($source->fresh()->name)->toBe('Nieuwe naam');
});

it('rejects source update for non-admin users', function () {
    $user   = User::factory()->create(['role' => 'user']);
    $source = Source::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->patchJson("/api/sources/{$source->id}", ['name' => 'Hacker'])
        ->assertForbidden();
});

it('allows admin to delete a source', function () {
    $admin  = User::factory()->admin()->create();
    $source = Source::factory()->create();

    $this->actingAs($admin, 'sanctum')
        ->deleteJson("/api/sources/{$source->id}")
        ->assertSuccessful();

    expect(Source::find($source->id))->toBeNull();
});

it('returns sources attached to an article ordered by primary first', function () {
    $article  = Article::factory()->create(['status' => 'active']);
    $primary  = Source::factory()->create(['name' => 'Primair']);
    $secondary = Source::factory()->create(['name' => 'Secundair']);

    $article->sources()->attach($secondary->id, ['source_url' => 'https://b.nl', 'is_primary' => false]);
    $article->sources()->attach($primary->id, ['source_url' => 'https://a.nl', 'is_primary' => true]);

    $response = $this->getJson("/api/articles/{$article->id}/sources")->assertOk();

    expect($response->json('0.name'))->toBe('Primair');
});

it('rejects duplicate source attach on an article with 409', function () {
    $admin   = User::factory()->admin()->create();
    $article = Article::factory()->create();
    $source  = Source::factory()->create();

    $article->sources()->attach($source->id, ['source_url' => 'https://nos.nl', 'is_primary' => false]);

    $this->actingAs($admin, 'sanctum')
        ->postJson("/api/articles/{$article->id}/sources", [
            'source_id'  => $source->id,
            'source_url' => 'https://nos.nl/andere-pagina',
            'is_primary' => true,
        ])
        ->assertStatus(409);
});
