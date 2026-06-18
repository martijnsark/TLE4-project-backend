<?php

use App\Models\Tag;

it('exposes the icon field on the tags endpoint', function () {
    Tag::create([
        'name' => 'Sport',
        'category' => 'navigation',
        'icon' => 'trend',
    ]);

    $this->getJson('/api/tags')
        ->assertOk()
        ->assertJsonFragment([
            'name' => 'Sport',
            'icon' => 'trend',
        ]);
});

it('returns a null icon when none is set', function () {
    Tag::create([
        'name' => 'Naamloos',
        'category' => 'topic',
    ]);

    $this->getJson('/api/tags')
        ->assertOk()
        ->assertJsonFragment([
            'name' => 'Naamloos',
            'icon' => null,
        ]);
});
