<?php

namespace Database\Factories;

use App\Enums\TagCategory;
use App\Enums\TagIcon;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'     => fake()->unique()->word(),
            'category' => fake()->randomElement(TagCategory::cases())->value,
            'icon'     => fake()->randomElement(TagIcon::cases())->value,
        ];
    }
}
