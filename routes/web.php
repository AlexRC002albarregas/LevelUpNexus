<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\{Profile, Game, Post, Group, User, Friendship, Message};
use App\Services\RawgService;

Route::view('/', 'landing')->name('landing');

// Ruta de prueba RAWG (temporal)
Route::get('/test-rawg', function(RawgService $rawg){
    $data = $rawg->searchGames('zelda', 1, 5);
    return response()->json($data);
});

// Prueba de autenticación
Route::get('/test-auth', function(){
    if(auth()->check()) {
        return response()->json([
            'authenticated' => true,
            'user' => auth()->user()->name,
            'id' => auth()->id()
        ]);
    }
    return response()->json(['authenticated' => false]);
});

// Auth web (sesión simple, usando AuthController API como backend via POST)
Route::get('/login', function () { return view('auth.login'); })->name('auth.login');
Route::get('/register', function () { return view('auth.register'); })->name('auth.register');
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required','email'],
        'password' => ['required','string'],
    ]);
    if (Auth::attempt($credentials, true)) {
        $request->session()->regenerate();
        return redirect()->route('landing')->with('status', 'Has iniciado sesión');
    }
    return back()->withErrors(['email' => 'Credenciales inválidas'])->onlyInput('email');
})->name('auth.login.post');
Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required','string','max:255'],
        'email' => ['required','email','unique:users,email'],
        'password' => ['required','string','min:8'],
    ]);
    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => 'player',
    ]);
    Auth::login($user);
    $request->session()->regenerate();
    return redirect()->route('landing')->with('status', 'Cuenta creada');
})->name('auth.register.post');

// Editar cuenta
Route::middleware('auth:web')->group(function(){
    Route::get('/account/edit', function(){
        return view('account.edit');
    })->name('account.edit');
    
    Route::post('/account/update', function(Request $request){
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','unique:users,email,'.auth()->id()],
            'avatar' => ['nullable','image','mimes:jpeg,jpg,png,gif,webp','max:2048'],
            'bio' => ['nullable','string','max:500'],
            'password' => ['nullable','string','min:8','confirmed'],
        ]);
        
        $user = auth()->user();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        // Manejar subida de avatar
        if($request->hasFile('avatar')){
            // Eliminar avatar anterior si existe
            if($user->avatar && Storage::disk('public')->exists($user->avatar)){
                Storage::disk('public')->delete($user->avatar);
            }
            // Guardar nuevo avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }
        
        if(isset($validated['bio'])){
            $user->bio = $validated['bio'];
        }
        
        // Actualizar privacidad (checkbox)
        $user->is_private = $request->has('is_private');
        
        if($validated['password'] ?? false){
            $user->password = Hash::make($validated['password']);
        }
        $user->save();
        
        return back()->with('status', 'Cuenta actualizada correctamente');
    })->name('account.update');
    
    Route::delete('/account', function(Request $request){
        $validated = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);
        
        $user = auth()->user();
        
        // Verificar que el email coincide
        if($validated['email'] !== $user->email){
            return back()->withErrors(['email' => 'El correo electrónico no coincide con tu cuenta']);
        }
        
        // Verificar la contraseña
        if(!Hash::check($validated['password'], $user->password)){
            return back()->withErrors(['password' => 'La contraseña es incorrecta']);
        }
        
        // Eliminar avatar si existe
        if($user->avatar && Storage::disk('public')->exists($user->avatar)){
            Storage::disk('public')->delete($user->avatar);
        }
        
        // Eliminar datos relacionados (opcional: cascada)
        $user->friendships()->delete(); // Amistades donde es el que envía
        Friendship::where('friend_id', $user->id)->delete(); // Amistades donde es el receptor
        $user->sentMessages()->delete();
        $user->receivedMessages()->delete();
        $user->posts()->delete();
        $user->comments()->delete();
        $user->reactions()->delete();
        $user->games()->delete();
        
        // Eliminar usuario
        $userId = $user->id;
        $user->delete();
        
        // Cerrar sesión
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('landing')->with('status', 'Tu cuenta ha sido eliminada permanentemente');
    })->name('account.delete');
});

Route::post('/logout', function (Request $request) {
    if (auth()->check()) {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
    return redirect()->route('landing')->with('status', 'Sesión cerrada');
})->name('auth.logout');

// Perfiles de usuarios
Route::middleware('auth:web')->group(function(){
    Route::get('/users/{user}', function (User $user) {
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
    })->name('users.show');
});

// Perfiles (legacy - podemos mantenerlo o eliminarlo)
Route::get('/profiles', function () {
    $profiles = Profile::with('user')->paginate(12);
    return view('profiles.index', compact('profiles'));
})->name('profiles.index');
Route::get('/profiles/{profile}', function (Profile $profile) {
    $profile->load('user');
    return view('profiles.show', compact('profile'));
})->name('profiles.show');
Route::middleware('auth:web')->group(function(){
    Route::get('/me/profile/edit', function () {
        $profile = auth()->user()->profile;
        return view('profiles.edit', compact('profile'));
    })->name('profiles.edit');
});

// Amigos
Route::middleware('auth:web')->group(function(){
    Route::get('/friends', function(){
        // Obtener amigos con información de la amistad
        $friendshipsData = Friendship::where(function($q){
            $q->where('user_id', auth()->id())->orWhere('friend_id', auth()->id());
        })->where('status', 'accepted')->get()->map(function($friendship){
            $friend = $friendship->user_id === auth()->id() ? $friendship->recipient : $friendship->sender;
            $friend->friendship_id = $friendship->id;
            return $friend;
        });
        
        $friends = $friendshipsData;
        $pending = auth()->user()->pendingFriendRequests();
        
        // Contar mensajes no leídos por cada amigo
        $unreadCounts = [];
        foreach($friends as $friend){
            $unreadCounts[$friend->id] = Message::where('sender_id', $friend->id)
                ->where('receiver_id', auth()->id())
                ->where('is_read', false)
                ->count();
        }
        
        return view('friends.index', compact('friends','pending','unreadCounts'));
    })->name('friends.index');
    Route::post('/friends/send', function(Request $request){
        $request->validate(['username' => 'required|string']);
        $friend = User::where('name', $request->username)->orWhere('email', $request->username)->first();
        if(!$friend || $friend->id === auth()->id()){
            return back()->withErrors(['username' => 'Usuario no encontrado']);
        }
        $exists = Friendship::where(function($q) use ($friend){
            $q->where('user_id', auth()->id())->where('friend_id', $friend->id);
        })->orWhere(function($q) use ($friend){
            $q->where('user_id', $friend->id)->where('friend_id', auth()->id());
        })->exists();
        if($exists){
            return back()->withErrors(['username' => 'Ya existe una solicitud o amistad con este usuario']);
        }
        Friendship::create(['user_id' => auth()->id(), 'friend_id' => $friend->id, 'status' => 'pending']);
        return back()->with('status', 'Solicitud enviada a '.$friend->name);
    })->name('friends.send');
    Route::post('/friends/{friendship}/accept', function(Friendship $friendship){
        if($friendship->friend_id !== auth()->id()){
            abort(403);
        }
        $friendship->update(['status' => 'accepted']);
        return back()->with('status', 'Solicitud aceptada');
    })->name('friends.accept');
    Route::post('/friends/{friendship}/decline', function(Friendship $friendship){
        if($friendship->friend_id !== auth()->id()){
            abort(403);
        }
        $friendship->update(['status' => 'declined']);
        return back()->with('status', 'Solicitud rechazada');
    })->name('friends.decline');
    
    Route::delete('/friends/{friendship}', function(Friendship $friendship){
        // Verificar que el usuario autenticado es parte de esta amistad
        if($friendship->user_id !== auth()->id() && $friendship->friend_id !== auth()->id()){
            abort(403);
        }
        
        // Solo eliminar si la amistad está aceptada
        if($friendship->status === 'accepted'){
            $friendship->delete();
            return back()->with('status', 'Amigo eliminado correctamente');
        }
        
        abort(403);
    })->name('friends.delete');
    
    // Mensajería
    Route::get('/messages/{friend}', function(User $friend){
        // Verificar que son amigos
        $areFriends = Friendship::where(function($q) use ($friend){
            $q->where('user_id', auth()->id())->where('friend_id', $friend->id)->where('status', 'accepted');
        })->orWhere(function($q) use ($friend){
            $q->where('user_id', $friend->id)->where('friend_id', auth()->id())->where('status', 'accepted');
        })->exists();
        
        if(!$areFriends) abort(403);
        
        $messages = Message::where(function($q) use ($friend){
            $q->where('sender_id', auth()->id())->where('receiver_id', $friend->id);
        })->orWhere(function($q) use ($friend){
            $q->where('sender_id', $friend->id)->where('receiver_id', auth()->id());
        })->orderBy('created_at')->get();
        
        // Marcar como leídos
        Message::where('sender_id', $friend->id)->where('receiver_id', auth()->id())->update(['is_read' => true]);
        
        return response()->json(['messages' => $messages, 'friend' => $friend]);
    })->name('messages.get');
    
    Route::post('/messages/{friend}', function(Request $request, User $friend){
        $request->validate(['content' => 'required|string|max:1000']);
        
        // Verificar que son amigos
        $areFriends = Friendship::where(function($q) use ($friend){
            $q->where('user_id', auth()->id())->where('friend_id', $friend->id)->where('status', 'accepted');
        })->orWhere(function($q) use ($friend){
            $q->where('user_id', $friend->id)->where('friend_id', auth()->id())->where('status', 'accepted');
        })->exists();
        
        if(!$areFriends) abort(403);
        
        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $friend->id,
            'content' => $request->content,
        ]);
        
        return response()->json(['message' => $message->load('sender')]);
    })->name('messages.send');
    
    // Contador de notificaciones en tiempo real
    Route::get('/api/notifications/count', function(){
        $count = auth()->user()->notificationsCount();
        return response()->json(['count' => $count]);
    });
    
    // Buscar usuarios para autocompletado (amigos e invitaciones)
    Route::get('/api/users/search', function(Request $request){
        if(!auth()->check()) {
            return response()->json(['error' => 'No autenticado', 'results' => []], 401);
        }
        
        $query = $request->get('q', '');
        if(strlen($query) < 2){
            return response()->json(['results' => []]);
        }
        
        // Buscar usuarios por nombre o email (excluyendo al usuario actual)
        $users = User::where('id', '!=', auth()->id())
            ->where(function($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('email', 'like', '%' . $query . '%');
            })
            ->limit(10)
            ->get(['id', 'name', 'email', 'avatar', 'role']);
        
        return response()->json(['results' => $users]);
    });
    
});

// API de búsqueda de juegos RAWG (cambiado de /api/ a /rawg/ para evitar conflicto)
Route::get('/rawg/search', function(Request $request, RawgService $rawg){
    try {
        $query = $request->get('q', '');
        if(strlen($query) < 2){
            return response()->json(['results' => []]);
        }
        $data = $rawg->searchGames($query, 1, 10);
        return response()->json($data);
    } catch(\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'results' => []
        ], 500);
    }
});

Route::get('/rawg/popular', function(RawgService $rawg){
    try {
        $data = $rawg->getPopularGames(1, 20);
        return response()->json($data);
    } catch(\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'results' => []
        ], 500);
    }
});

Route::post('/rawg/favorites/add', function(Request $request){
    $request->validate(['game_id' => 'required|integer']);
    $user = auth()->user();
    if(!$user) {
        return response()->json(['error' => 'No autenticado'], 401);
    }
    $favorites = $user->favorite_games ?? [];
    
    if(!in_array($request->game_id, $favorites)){
        $favorites[] = $request->game_id;
        $user->favorite_games = $favorites;
        $user->save();
    }
    
    return response()->json(['success' => true, 'favorites' => $favorites]);
});

Route::post('/rawg/favorites/remove', function(Request $request){
    $request->validate(['game_id' => 'required|integer']);
    $user = auth()->user();
    if(!$user) {
        return response()->json(['error' => 'No autenticado'], 401);
    }
    $favorites = $user->favorite_games ?? [];
    
    $favorites = array_values(array_filter($favorites, fn($id) => $id != $request->game_id));
    $user->favorite_games = $favorites;
    $user->save();
    
    return response()->json(['success' => true, 'favorites' => $favorites]);
});

Route::get('/rawg/favorites', function(RawgService $rawg, Request $request){
    // Si hay parámetro 'ids', devolver esos juegos (para ver perfiles de otros usuarios)
    if($request->has('ids')) {
        $ids = explode(',', $request->ids);
        $ids = array_map('intval', $ids);
        $games = $rawg->getGamesByIds($ids);
        return response()->json(['games' => $games]);
    }
    
    // Si no, devolver los favoritos del usuario autenticado
    $user = auth()->user();
    if(!$user) {
        return response()->json(['error' => 'No autenticado', 'games' => []], 401);
    }
    $favoriteIds = $user->favorite_games ?? [];
    $games = $rawg->getGamesByIds($favoriteIds);
    return response()->json(['games' => $games]);
});

// Juegos
Route::middleware('auth:web')->group(function(){
    Route::get('/games', function (Request $request) {
        $query = Game::where('user_id', auth()->id());
        
        // Filtro por género
        if ($request->has('genre') && $request->genre !== '') {
            $query->where('genre', 'like', '%' . $request->genre . '%');
        }
        
        // Ordenamiento
        $sortBy = $request->get('sort', 'favorites');
        
        switch ($sortBy) {
            case 'released_newest':
                $query->orderByDesc('released_date');
                break;
            case 'released_oldest':
                $query->orderBy('released_date');
                break;
            case 'hours_desc':
                $query->orderByDesc('hours_played');
                break;
            case 'favorites':
            default:
                $query->orderByDesc('is_favorite')->orderBy('title');
                break;
        }
        
        $games = $query->paginate(12)->withQueryString();
        
        // Obtener géneros únicos para el filtro
        $genres = Game::where('user_id', auth()->id())
            ->whereNotNull('genre')
            ->where('genre', '!=', '')
            ->distinct()
            ->pluck('genre')
            ->flatMap(function($genre) {
                // Si hay múltiples géneros separados por coma, dividirlos
                return explode(', ', $genre);
            })
            ->unique()
            ->sort()
            ->values();
        
        return view('games.index', compact('games', 'genres'));
    })->name('games.index');
    Route::get('/games/create', function () {
        return view('games.create');
    })->name('games.create');
    Route::post('/games', [\App\Http\Controllers\GameController::class, 'store'])->name('games.store');
    Route::get('/games/{game}/edit', function (Game $game) {
        abort_unless($game->user_id === auth()->id() || auth()->user()->isAdmin(), 403);
        return view('games.edit', compact('game'));
    })->name('games.edit');
    Route::put('/games/{game}', [\App\Http\Controllers\GameController::class, 'update'])->name('games.update');
    Route::delete('/games/{game}', [\App\Http\Controllers\GameController::class, 'destroy'])->name('games.destroy');
});

// Publicaciones (CRUD completo)
Route::middleware('auth:web')->group(function(){
    Route::get('/posts', [\App\Http\Controllers\PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [\App\Http\Controllers\PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [\App\Http\Controllers\PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [\App\Http\Controllers\PostController::class, 'show'])->name('posts.show');
    Route::get('/posts/{post}/edit', [\App\Http\Controllers\PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [\App\Http\Controllers\PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [\App\Http\Controllers\PostController::class, 'destroy'])->name('posts.destroy');
    
    // Comentarios
    Route::post('/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');
    
    // Reacciones
    Route::post('/posts/{post}/reactions', [\App\Http\Controllers\ReactionController::class, 'toggle'])->name('reactions.toggle');
    Route::delete('/reactions/{reaction}', [\App\Http\Controllers\ReactionController::class, 'destroy'])->name('reactions.destroy');
});

// Grupos
Route::get('/groups', [\App\Http\Controllers\GroupController::class, 'index'])->name('groups.index');
Route::middleware('auth:web')->group(function(){
    Route::get('/groups/create', [\App\Http\Controllers\GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [\App\Http\Controllers\GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{group}', [\App\Http\Controllers\GroupController::class, 'show'])->name('groups.show');
    Route::get('/groups/{group}/edit', [\App\Http\Controllers\GroupController::class, 'edit'])->name('groups.edit');
    Route::put('/groups/{group}', [\App\Http\Controllers\GroupController::class, 'update'])->name('groups.update');
    Route::delete('/groups/{group}', [\App\Http\Controllers\GroupController::class, 'destroy'])->name('groups.destroy');
    Route::post('/groups/{group}/join', [\App\Http\Controllers\GroupController::class, 'join'])->name('groups.join');
    Route::post('/groups/{group}/leave', [\App\Http\Controllers\GroupController::class, 'leave'])->name('groups.leave');
    
    // Invitaciones a grupos
    Route::post('/groups/{group}/invitations', [\App\Http\Controllers\GroupInvitationController::class, 'store'])->name('group-invitations.store');
    Route::post('/group-invitations/{groupInvitation}/accept', [\App\Http\Controllers\GroupInvitationController::class, 'accept'])->name('group-invitations.accept');
    Route::post('/group-invitations/{groupInvitation}/decline', [\App\Http\Controllers\GroupInvitationController::class, 'decline'])->name('group-invitations.decline');
    Route::post('/group-invitations/{groupInvitation}/cancel', [\App\Http\Controllers\GroupInvitationController::class, 'cancel'])->name('group-invitations.cancel');
    Route::delete('/group-invitations/{groupInvitation}', [\App\Http\Controllers\GroupInvitationController::class, 'destroy'])->name('group-invitations.destroy');
});

// Panel admin (listados básicos)
Route::middleware(['auth','role:admin'])->group(function(){
    Route::get('/admin', function(){
        $users = User::latest()->paginate(10);
        $groups = Group::latest()->paginate(10);
        $posts = Post::latest()->paginate(10);
        $games = Game::latest()->paginate(10);
        return view('admin.index', compact('users','groups','posts','games'));
    })->name('admin.index');
});

/* Fin de rutas */
