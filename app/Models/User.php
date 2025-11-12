<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'bio',
        'favorite_games',
        'is_private',
        'is_active',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'favorite_games' => 'array',
        'is_private' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function games()
    {
        return $this->hasMany(Game::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class)->withPivot('member_role')->withTimestamps();
    }

    public function ownedGroups()
    {
        return $this->hasMany(Group::class, 'owner_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    // Amistades: solicitudes enviadas
    public function sentFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    // Amistades: solicitudes recibidas
    public function receivedFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'friend_id');
    }

    // Amigos aceptados (enviadas o recibidas con status accepted)
    public function friends()
    {
        $sent = $this->sentFriendRequests()->where('status', 'accepted')->get()->pluck('friend_id');
        $received = $this->receivedFriendRequests()->where('status', 'accepted')->get()->pluck('user_id');
        return User::whereIn('id', $sent->merge($received))->get();
    }

    // Solicitudes pendientes recibidas
    public function pendingFriendRequests()
    {
        return $this->receivedFriendRequests()->where('status', 'pending')->with('sender')->get();
    }

    // Mensajes enviados
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // Mensajes recibidos
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // Mensajes no leídos
    public function unreadMessagesCount()
    {
        return $this->receivedMessages()->where('is_read', false)->count();
    }

    // Total de notificaciones (solicitudes + mensajes)
    public function notificationsCount()
    {
        return $this->pendingFriendRequests()->count() + $this->unreadMessagesCount();
    }

    // Invitaciones pendientes a grupos
    public function pendingGroupInvitationsCount()
    {
        return GroupInvitation::where('recipient_id', $this->id)
            ->where('status', 'pending')
            ->count();
    }

    // Verificar si el usuario actual puede ver el perfil de otro usuario
    public function canViewProfile(User $targetUser): bool
    {
        // Si es el mismo usuario, siempre puede ver su propio perfil
        if ($this->id === $targetUser->id) {
            return true;
        }

        // Si es admin, puede ver todos los perfiles
        if ($this->isAdmin()) {
            return true;
        }

        // Si el perfil es público, todos pueden verlo
        if (!$targetUser->is_private) {
            return true;
        }

        // Si el perfil es privado, verificar si son amigos
        return $this->isFriendWith($targetUser);
    }

    // Verificar si dos usuarios son amigos
    public function isFriendWith(User $user): bool
    {
        return Friendship::where(function($query) use ($user) {
            $query->where('user_id', $this->id)
                  ->where('friend_id', $user->id);
        })->orWhere(function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('friend_id', $this->id);
        })->where('status', 'accepted')->exists();
    }
}
