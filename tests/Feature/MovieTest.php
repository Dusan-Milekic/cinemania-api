<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Movie;
use App\Models\Screening;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MovieTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_movies()
    {
        // Napravi test podatke
        Movie::factory()->count(3)->create();

        // Pozovi API
        $response = $this->getJson('/api/movies');

        // Proveri odgovor
        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_get_single_movie_with_screenings()
    {
        $movie = Movie::factory()->create(['title' => 'Test Movie']);
        Screening::factory()->count(2)->create(['movie_id' => $movie->id]);

        $response = $this->getJson("/api/movies/{$movie->id}");

        $response->assertStatus(200)
            ->assertJsonPath('title', 'Test Movie')
            ->assertJsonCount(2, 'screenings');
    }

    public function test_returns_404_for_nonexistent_movie()
    {
        $response = $this->getJson('/api/movies/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Movie not found']);
    }
}
