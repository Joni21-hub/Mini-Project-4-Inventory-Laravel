<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'sku' => strtoupper(fake()->unique()->bothify('PRD-###-??')),
            'name' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 50000, 5000000),
            'stock' => fake()->numberBetween(0, 50),
            'minimum_stock' => fake()->numberBetween(3, 8),
        ];
    }
}
