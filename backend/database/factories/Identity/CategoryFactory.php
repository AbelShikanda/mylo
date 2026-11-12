<?php

namespace Database\Factories\Identity;

use App\Models\Identity\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'name' => $this->faker->unique()->word(),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->sentence(),
            'image' => null,
            'is_featured' => false,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
