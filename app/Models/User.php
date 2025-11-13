<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Atributos que se pueden asignar masivamente.
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
     * Atributos que deben ocultarse en la serialización.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atributos que deben transformarse a tipos específicos.
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

    /**
     * Relación con el perfil extendido del usuario.
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Relación con los juegos asociados al usuario.
     */
    public function games()
    {
        return $this->hasMany(Game::class);
    }

    /**
     * Relación con las publicaciones creadas por el usuario.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Relación con los comentarios realizados por el usuario.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Relación con las reacciones emitidas por el usuario.
     */
    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    /**
     * Relación con los grupos en los que participa el usuario.
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class)->withPivot('member_role')->withTimestamps();
    }

    /**
     * Relación con los grupos que el usuario administra.
     */
    public function ownedGroups()
    {
        return $this->hasMany(Group::class, 'owner_id');
    }

    /**
     * Determina si el usuario tiene rol de administrador.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Comprueba si el usuario posee el rol indicado.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Relación con las solicitudes de amistad enviadas.
     */
    public function sentFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    /**
     * Relación con las solicitudes de amistad recibidas.
     */
    public function receivedFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'friend_id');
    }

    /**
     * Devuelve la colección de amistades aceptadas.
     */
    public function friends()
    {
        $sent = $this->sentFriendRequests()->where('status', 'accepted')->get()->pluck('friend_id');
        $received = $this->receivedFriendRequests()->where('status', 'accepted')->get()->pluck('user_id');
        return User::whereIn('id', $sent->merge($received))->get();
    }

    /**
     * Obtiene las solicitudes de amistad pendientes del usuario.
     */
    public function pendingFriendRequests()
    {
        return $this->receivedFriendRequests()->where('status', 'pending')->with('sender')->get();
    }

    /**
     * Relación con los mensajes enviados por el usuario.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Relación con los mensajes recibidos por el usuario.
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Calcula cuántos mensajes pendientes tiene el usuario.
     */
    public function unreadMessagesCount()
    {
        return $this->receivedMessages()->where('is_read', false)->count();
    }

    /**
     * Suma las notificaciones pendientes del usuario.
     */
    public function notificationsCount()
    {
        return $this->pendingFriendRequests()->count() + $this->unreadMessagesCount();
    }

    /**
     * Calcula cuántas invitaciones a grupos están pendientes.
     */
    public function pendingGroupInvitationsCount()
    {
        return GroupInvitation::where('recipient_id', $this->id)
            ->where('status', 'pending')
            ->count();
    }

    /**
     * Indica si el usuario puede visualizar el perfil indicado.
     */
    public function canViewProfile(User $targetUser): bool
    {
        if ($this->id === $targetUser->id) {
            return true;
        }

        if ($this->isAdmin()) {
            return true;
        }

        if (!$targetUser->is_private) {
            return true;
        }

        return $this->isFriendWith($targetUser);
    }

    /**
     * Comprueba si existe una amistad aceptada con el usuario dado.
     */
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
