<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id', 'sender_id', 'recipient_id', 'status'
    ];

    /**
     * Relación con el grupo al que pertenece la invitación.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Relación con el usuario que envía la invitación.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relación con el usuario que recibe la invitación.
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
