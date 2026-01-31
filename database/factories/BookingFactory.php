<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Screening;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        $seatsCount = fake()->numberBetween(1, 5);
        $price = fake()->randomFloat(2, 500, 800);

        return [
            'user_id' => User::factory(),
            'screening_id' => Screening::factory(),
            'seats_count' => $seatsCount,
            'total_price' => $seatsCount * $price,
            'status' => fake()->randomElement(['pending', 'confirmed', 'cancelled']),
        ];
    }
}
