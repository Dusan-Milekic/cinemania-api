<?php

namespace App\Http\Controllers;

use App\Models\Screening;
use Illuminate\Http\Request;

class ScreeningController extends Controller
{
    // Vraća sve projekcije sa filmom
    public function index()
    {
        $screenings = Screening::with('movie')->get();
        return response()->json($screenings);
    }

    // Vraća jednu projekciju sa filmom i rezervacijama
    public function show($id)
    {
        $screening = Screening::with('movie', 'bookings')->find($id);

        if (!$screening) {
            return response()->json(['message' => 'Screening not found'], 404);
        }

        return response()->json($screening);
    }
}
