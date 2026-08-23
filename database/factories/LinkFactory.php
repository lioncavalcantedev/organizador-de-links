<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Link>
 */
class LinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'url' => fake()->url(),
            'image_url' => fake()->imageUrl(96, 96),
            'category' => fake()->word(),
            'category_variant' => fake()->randomElement(['blue', 'green', 'purple']),
            'position' => 1,
        ];
    }
}
