<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    /**
     * Display the specified user profile.
     */
    public function show(User $user)
    {
        // Verificar si el usuario autenticado puede ver este perfil
        $canView = auth()->user()->canViewProfile($user);
        
        if (!$canView) {
            $posts = collect([]); // Colección vacía si no puede ver
        } else {
            // Filtrar posts: solo mostrar públicos si el perfil es privado y no es amigo
            $posts = $user->posts()
                ->where(function($query) use ($user) {
                    // Si es el propio usuario, mostrar todos sus posts
                    if(auth()->id() === $user->id) {
                        return;
                    }
                    
                    // Si el perfil es privado y no es amigo, solo mostrar posts públicos
                    if($user->is_private && !auth()->user()->isFriendWith($user)) {
                        $query->where('visibility', 'public');
                    }
                })
                ->latest()
                ->paginate(10);
        }
        
        return view('users.show', compact('user', 'posts', 'canView'));
    }
}

