<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'owner_id', 'avatar'
    ];

    /**
     * Relación con el usuario propietario del grupo.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relación con los miembros del grupo incluyendo su rol.
     */
    public function members()
    {
        return $this->belongsToMany(User::class)->withPivot('member_role')->withTimestamps();
    }

    /**
     * Relación con las publicaciones asociadas al grupo.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
