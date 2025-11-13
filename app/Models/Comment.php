<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'post_id', 'content'
    ];

    /**
     * Relación con el usuario autor del comentario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la publicación comentada.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Relación polimórfica con las reacciones del comentario.
     */
    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }
}
