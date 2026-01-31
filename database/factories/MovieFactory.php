<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MovieFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'duration' => fake()->numberBetween(90, 180),
            'poster_url' => fake()->imageUrl(300, 450, 'movies'),
            'release_date' => fake()->date(),
            'genre' => fake()->randomElement(['Action', 'Drama', 'Comedy', 'Sci-Fi', 'Horror', 'Thriller']),
        ];
    }
}
