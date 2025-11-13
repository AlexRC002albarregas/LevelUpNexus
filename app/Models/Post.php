<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'group_id', 'game_id', 'content', 'visibility', 'image',
        'rawg_game_id', 'game_title', 'game_image', 'game_platform'
    ];

    /**
     * Relación con el autor de la publicación.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el grupo al que pertenece la publicación.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Relación con el juego vinculado a la publicación.
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Relación con los comentarios que recibe la publicación.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Relación polimórfica con las reacciones del post.
     */
    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }
}
