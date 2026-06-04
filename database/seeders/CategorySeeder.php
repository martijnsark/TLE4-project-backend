<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Voor jou', 'color' => '#6366f1', 'position' => 0],
            ['name' => 'Klimaat',  'color' => '#22c55e', 'position' => 1],
            ['name' => 'Politiek', 'color' => '#ef4444', 'position' => 2],
            ['name' => 'Sport',    'color' => '#f59e0b', 'position' => 3],
            ['name' => 'Tech',     'color' => '#3b82f6', 'position' => 4],
            ['name' => 'Wereld',   'color' => '#8b5cf6', 'position' => 5],
        ];

        foreach ($categories as $data) {
            Category::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
