<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScreeningFactory extends Factory
{
    public function definition(): array
    {
        return [
            'movie_id' => Movie::factory(),
            'screen_date' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'screen_time' => fake()->time('H:i:s'),
            'available_seats' => fake()->numberBetween(50, 150),
            'price' => fake()->randomFloat(2, 300, 1000),
        ];
    }
}
