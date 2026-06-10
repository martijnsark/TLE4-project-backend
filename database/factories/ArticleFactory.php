<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'           => fake()->sentence(6),
            'summary'         => fake()->paragraph(),
            'content'         => null,
            'body_paragraphs' => [
                ['value' => fake()->paragraph()],
                ['value' => fake()->paragraph()],
            ],
            'image_url'     => null,
            'tone'          => fake()->randomElement(['Live', 'Achtergrond', 'Reportage', 'Opinie']),
            'status'        => 'active',
            'published_at'  => now(),
            'author_id'     => User::factory()->admin(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft']);
    }

    public function goodNews(): static
    {
        return $this->afterCreating(fn (Article $article) =>
            $article->tags()->syncWithoutDetaching([
                Tag::firstOrCreate(['name' => 'Goed nieuws'], ['category' => 'flag'])->id,
            ])
        );
    }

    public function trending(): static
    {
        return $this->afterCreating(fn (Article $article) =>
            $article->tags()->syncWithoutDetaching([
                Tag::firstOrCreate(['name' => 'Trending'], ['category' => 'flag'])->id,
            ])
        );
    }
}
