<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nick', 'avatar', 'platform', 'favorite_games', 'hours_played', 'achievements', 'bio'
    ];

    protected $casts = [
        'favorite_games' => 'array',
        'achievements' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
