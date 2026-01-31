<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Screening;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // Vraća sve rezervacije trenutnog korisnika
    public function index()
    {
        $bookings = auth()->user()->bookings()->with('screening.movie')->get();
        return response()->json($bookings);
    }

    // Vraća jednu rezervaciju
    public function show($id)
    {
        $booking = Booking::with('screening.movie', 'user')->find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        // Provera da li je ovo rezervacija trenutnog korisnika
        if ($booking->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($booking);
    }

    // Kreira novu rezervaciju
    public function store(Request $request)
    {
        $validated = $request->validate([
            'screening_id' => 'required|exists:screenings,id',
            'seats_count' => 'required|integer|min:1|max:10',
        ]);

        // Dohvati projekciju
        $screening = Screening::find($validated['screening_id']);

        // Proveri da li ima dovoljno mesta
        if ($screening->available_seats < $validated['seats_count']) {
            return response()->json([
                'message' => 'Not enough available seats',
                'available' => $screening->available_seats,
                'requested' => $validated['seats_count']
            ], 400);
        }

        // Izračunaj ukupnu cenu
        $totalPrice = $screening->price * $validated['seats_count'];

        // Kreiraj rezervaciju
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'screening_id' => $validated['screening_id'],
            'seats_count' => $validated['seats_count'],
            'total_price' => $totalPrice,
            'status' => 'pending'
        ]);

        // Smanji broj dostupnih mesta
        $screening->decrement('available_seats', $validated['seats_count']);

        // Vrati rezervaciju sa relacionim podacima
        return response()->json(
            Booking::with('screening.movie')->find($booking->id),
            201
        );
    }

    // Briše rezervaciju
    public function destroy($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        // Provera da li je ovo rezervacija trenutnog korisnika
        if ($booking->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Vrati mesta nazad
        $screening = Screening::find($booking->screening_id);
        $screening->increment('available_seats', $booking->seats_count);

        // Obriši rezervaciju
        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully'], 200);
    }
}
