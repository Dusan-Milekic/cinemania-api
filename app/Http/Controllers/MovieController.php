<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    // Returns all films
    public function index()
    {
        $movies =  Movie::all();
        return response()->json($movies);
    }

    // Returns a single film by ID with all screenings
    public function show($id)
    {
        $movie = Movie::with('screenings')->find($id);

        if (!$movie) {
            return response()->json(['message' => 'Movie with id '.$id.' not found!'], 404);
        }

        return response()->json($movie);
    }
}
