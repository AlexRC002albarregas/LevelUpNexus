<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'platform', 'genre', 'hours_played', 'is_favorite',
        'rawg_id', 'rawg_image', 'rawg_rating', 'released_date', 'rawg_slug'
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'rawg_rating' => 'decimal:2',
        'released_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
