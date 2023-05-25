<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $category = Category::factory()->create();
        $brand = Brand::factory()->create();

        $data = [
            'uuid' => fake()->uuid(),
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(10),
            'price' => fake()->unique()->randomFloat(2, 10, 100),
            'category_uuid' => $category->uuid,
            'metadata' => [
                'brand' => $brand->uuid,
            ],
        ];

        return $data;
    }
}
