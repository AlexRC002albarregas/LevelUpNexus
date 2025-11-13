<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'friend_id', 'status'];

    /**
     * Relación con el usuario que envió la solicitud.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el usuario que recibe la solicitud.
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
