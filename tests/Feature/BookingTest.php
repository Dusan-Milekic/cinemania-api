<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Movie;
use App\Models\Screening;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_booking()
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();
        $screening = Screening::factory()->create([
            'movie_id' => $movie->id,
            'available_seats' => 50,
            'price' => 500
        ]);

        $response = $this->actingAs($user)->postJson('/api/bookings', [
            'screening_id' => $screening->id,
            'seats_count' => 2
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('seats_count', 2)
            ->assertJsonPath('total_price', 1000);

        // Proveri da su mesta smanjena
        $this->assertEquals(48, $screening->fresh()->available_seats);
    }

    public function test_cannot_book_more_seats_than_available()
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();
        $screening = Screening::factory()->create([
            'movie_id' => $movie->id,
            'available_seats' => 5,
            'price' => 500
        ]);

        $response = $this->actingAs($user)->postJson('/api/bookings', [
            'screening_id' => $screening->id,
            'seats_count' => 10
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Not enough available seats']);
    }

    public function test_user_can_get_their_bookings()
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();
        $screening = Screening::factory()->create(['movie_id' => $movie->id]);

        Booking::factory()->count(3)->create([
            'user_id' => $user->id,
            'screening_id' => $screening->id
        ]);

        $response = $this->actingAs($user)->getJson('/api/bookings');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_user_can_delete_their_booking()
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();
        $screening = Screening::factory()->create([
            'movie_id' => $movie->id,
            'available_seats' => 50,
            'price' => 500
        ]);

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'screening_id' => $screening->id,
            'seats_count' => 3
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/bookings/{$booking->id}");

        $response->assertStatus(200);

        // Proveri da su mesta vraćena
        $this->assertEquals(53, $screening->fresh()->available_seats);
        $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    }

    public function test_user_cannot_delete_other_users_booking()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $movie = Movie::factory()->create();
        $screening = Screening::factory()->create(['movie_id' => $movie->id]);

        $booking = Booking::factory()->create([
            'user_id' => $user1->id,
            'screening_id' => $screening->id
        ]);

        $response = $this->actingAs($user2)->deleteJson("/api/bookings/{$booking->id}");

        $response->assertStatus(403);
    }
}
