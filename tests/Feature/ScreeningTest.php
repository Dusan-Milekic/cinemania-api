<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Movie;
use App\Models\Screening;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScreeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_screenings_with_movies()
    {
        $movie = Movie::factory()->create();
        Screening::factory()->count(3)->create(['movie_id' => $movie->id]);

        $response = $this->getJson('/api/screenings');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'movie_id', 'screen_date', 'screen_time', 'available_seats', 'price', 'movie']
            ]);
    }

    public function test_can_get_single_screening_with_movie_and_bookings()
    {
        $movie = Movie::factory()->create();
        $screening = Screening::factory()->create(['movie_id' => $movie->id]);

        $response = $this->getJson("/api/screenings/{$screening->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['id', 'movie', 'bookings']);
    }

    public function test_returns_404_for_nonexistent_screening()
    {
        $response = $this->getJson('/api/screenings/999');

        $response->assertStatus(404);
    }
}
