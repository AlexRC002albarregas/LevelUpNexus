<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Group;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentUser = auth()->user();
        $userId = auth()->id();
        
        // Obtener IDs de amigos del usuario autenticado
        $friendIds = DB::table('friendships')
            ->where(function($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('friend_id', $userId);
            })
            ->where('status', 'accepted')
            ->get()
            ->map(function($friendship) use ($userId) {
                return $friendship->user_id === $userId 
                    ? $friendship->friend_id 
                    : $friendship->user_id;
            })
            ->toArray();
        
        // Filtrar publicaciones según visibilidad del post y perfil del autor
        $posts = Post::with(['user', 'group', 'reactions.user', 'comments.user'])
            ->where(function($query) use ($userId, $friendIds) {
                // 1. Propias publicaciones (siempre visibles)
                $query->where('user_id', $userId)
                    // 2. Publicaciones públicas de perfiles públicos
                    ->orWhere(function($q) use ($userId, $friendIds) {
                        $q->where('visibility', 'public')
                            ->whereHas('user', function($userQ) use ($userId, $friendIds) {
                                $userQ->where('is_private', false)
                                    ->orWhereIn('id', $friendIds);
                            });
                    })
                    // 3. Publicaciones públicas de perfiles privados (solo si es amigo)
                    ->orWhere(function($q) use ($userId, $friendIds) {
                        $q->where('visibility', 'public')
                            ->whereHas('user', function($userQ) use ($friendIds) {
                                $userQ->where('is_private', true)
                                    ->whereIn('id', $friendIds);
                            });
                    })
                    // 4. Publicaciones privadas (solo si es amigo del autor)
                    ->orWhere(function($q) use ($friendIds) {
                        $q->where('visibility', 'private')
                            ->whereHas('user', function($userQ) use ($friendIds) {
                                $userQ->whereIn('id', $friendIds);
                            });
                    })
                    // 5. Publicaciones de grupo (solo si es miembro)
                    ->orWhere(function($q) use ($userId) {
                        $q->where('visibility', 'group')
                            ->whereHas('group', function($groupQ) use ($userId) {
                                $groupQ->whereHas('members', function($memberQ) use ($userId) {
                                    $memberQ->where('user_id', $userId);
                                });
                            });
                    });
            })
            ->orderByDesc('id')
            ->paginate(10);
        
        // Ordenar comentarios por fecha (más recientes primero) para cada post
        foreach($posts as $post) {
            $post->comments = $post->comments->sortByDesc('created_at')->values();
        }
        
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener grupos del usuario para seleccionar en qué grupo publicar
        $groups = auth()->user()->groups;
        
        return view('posts.create', compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $post = Post::create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'group_id' => $request->group_id,
            'visibility' => $request->visibility ?? 'public',
        ]);

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Publicación creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $currentUser = auth()->user();
        
        // Verificar si el usuario puede ver esta publicación
        // 1. Si es el propio autor, siempre puede ver
        if($post->user_id === auth()->id()) {
            $post->load(['user', 'group', 'comments.user', 'reactions.user']);
            return view('posts.show', compact('post'));
        }
        
        // 2. Verificar perfil privado del autor
        if(!$currentUser->canViewProfile($post->user)){
            abort(403, 'No tienes permiso para ver esta publicación');
        }
        
        // 3. Verificar visibilidad específica del post
        if($post->visibility === 'private') {
            // Solo amigos pueden ver publicaciones privadas
            if(!$currentUser->isFriendWith($post->user)) {
                abort(403, 'Esta es una publicación privada. Solo los amigos del autor pueden verla.');
            }
        } elseif($post->visibility === 'group') {
            // Solo miembros del grupo pueden ver
            if(!$post->group || !$post->group->members->contains(auth()->id())) {
                abort(403, 'Esta es una publicación de grupo. Solo los miembros pueden verla.');
            }
        }
        // 'public' y otros valores: ya pasaron la verificación del perfil
        
        $post->load(['user', 'group', 'comments.user', 'reactions.user']);
        
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        // Verificar autorización
        abort_unless(
            auth()->id() === $post->user_id || auth()->user()->role === 'admin',
            403
        );
        
        $groups = auth()->user()->groups;
        
        return view('posts.edit', compact('post', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update([
            'content' => $request->content,
            'group_id' => $request->group_id,
            'visibility' => $request->visibility ?? 'public',
        ]);

        return redirect()
            ->route('posts.show', $post)
            ->with('status', 'Publicación actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        // Verificar autorización
        abort_unless(
            auth()->id() === $post->user_id || auth()->user()->role === 'admin',
            403
        );
        
        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('status', 'Publicación eliminada correctamente');
    }
}
