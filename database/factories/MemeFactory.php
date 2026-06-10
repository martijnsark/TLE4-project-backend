<?php

namespace Database\Factories;

use App\Models\Meme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Meme>
 */
class MemeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'article_id'  => null,
            'editor_id'   => null,
            'title'       => fake()->sentence(4),
            'image_url'   => 'https://placekitten.com/400/300',
            'author'      => '@' . fake()->userName(),
            'author_name' => fake()->name(),
            'top'         => fake()->sentence(),
            'bot'         => fake()->sentence(),
            'cat'         => strtoupper(fake()->word()),
        ];
    }
}
