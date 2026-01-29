<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'release_date', 'duration', 'poster_url', 'genre'];

    public function screenings()
    {
        return $this->hasMany(Screening::class);
    }
}
