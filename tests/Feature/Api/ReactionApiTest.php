<?php

use App\Models\Article;
use App\Models\Meme;
use App\Models\User;

it('saves a smile reaction for an authenticated user and counts it in the article shape', function () {
    $article = Article::factory()->create(['status' => 'active']);
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/articles/{$article->id}/reaction", ['reaction' => 'smile'])
        ->assertOk()
        ->assertJsonPath('reaction.reaction', 'smile');

    $this->assertDatabaseHas('reactions', [
        'user_id' => $user->id,
        'reactionable_id' => $article->id,
        'reactionable_type' => Article::class,
        'reaction' => 'smile',
    ]);

    $this->getJson('/api/articles')
        ->assertOk()
        ->assertJsonPath('data.0.reactions.smile', 1)
        ->assertJsonPath('data.0.reactions.meh', 0)
        ->assertJsonPath('data.0.reactions.frown', 0);
});

it('rejects a legacy happy/shocked/sad value with 422', function () {
    $article = Article::factory()->create(['status' => 'active']);
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/articles/{$article->id}/reaction", ['reaction' => 'happy'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('reaction');

    $this->assertDatabaseCount('reactions', 0);
});

it('updates the existing reaction instead of creating a second one', function () {
    $article = Article::factory()->create(['status' => 'active']);
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/articles/{$article->id}/reaction", ['reaction' => 'smile'])
        ->assertOk();

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/articles/{$article->id}/reaction", ['reaction' => 'meh'])
        ->assertOk();

    $this->assertDatabaseCount('reactions', 1);
    $this->assertDatabaseHas('reactions', [
        'user_id' => $user->id,
        'reactionable_id' => $article->id,
        'reaction' => 'meh',
    ]);
});

it('requires authentication to post a reaction', function () {
    $article = Article::factory()->create(['status' => 'active']);

    $this->postJson("/api/articles/{$article->id}/reaction", ['reaction' => 'smile'])
        ->assertUnauthorized();
});

it('returns reaction stats with smile/meh/frown count keys and my_reaction', function () {
    $article = Article::factory()->create(['status' => 'active']);
    $u1 = User::factory()->create();
    $u2 = User::factory()->create();

    $article->reactions()->create(['user_id' => $u1->id, 'reaction' => 'smile']);
    $article->reactions()->create(['user_id' => $u2->id, 'reaction' => 'frown']);

    $this->getJson("/api/articles/{$article->id}/reactions")
        ->assertOk()
        ->assertJsonStructure(['counts' => ['smile', 'meh', 'frown'], 'my_reaction'])
        ->assertJsonPath('counts.smile', 1)
        ->assertJsonPath('counts.meh', 0)
        ->assertJsonPath('counts.frown', 1)
        ->assertJsonPath('my_reaction', null);
});

it('deletes the reaction and 404s on a second delete', function () {
    $article = Article::factory()->create(['status' => 'active']);
    $user = User::factory()->create();
    $article->reactions()->create(['user_id' => $user->id, 'reaction' => 'smile']);

    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/articles/{$article->id}/reaction")
        ->assertOk();

    $this->assertDatabaseMissing('reactions', [
        'user_id' => $user->id,
        'reactionable_id' => $article->id,
    ]);

    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/articles/{$article->id}/reaction")
        ->assertNotFound();
});

it('saves a smile reaction on a meme', function () {
    $meme = Meme::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/memes/{$meme->id}/reaction", ['reaction' => 'smile'])
        ->assertOk();

    $this->assertDatabaseHas('reactions', [
        'user_id' => $user->id,
        'reactionable_id' => $meme->id,
        'reactionable_type' => Meme::class,
        'reaction' => 'smile',
    ]);
});
