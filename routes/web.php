<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');

// Ruta de prueba RAWG (temporal)
Route::get('/test-rawg', [\App\Http\Controllers\RawgController::class, 'test'])->name('test.rawg');

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

// Auth web
Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('auth.login');
Route::get('/register', [\App\Http\Controllers\AuthController::class, 'showRegisterForm'])->name('auth.register');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('auth.login.post');
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('auth.register.post');
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('auth.logout');

// Editar cuenta
Route::middleware('auth:web')->group(function(){
    Route::get('/account/edit', [\App\Http\Controllers\AccountController::class, 'edit'])->name('account.edit');
    Route::post('/account/update', [\App\Http\Controllers\AccountController::class, 'update'])->name('account.update');
    Route::delete('/account', [\App\Http\Controllers\AccountController::class, 'destroy'])->name('account.delete');
});

// Perfiles de usuarios
Route::middleware('auth:web')->group(function(){
    Route::get('/users/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('users.show');
});

// Perfiles (legacy)
Route::get('/profiles', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profiles.index');
Route::get('/profiles/{profile}', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profiles.show');
Route::middleware('auth:web')->group(function(){
    Route::get('/me/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profiles.edit');
});

// Amigos
Route::middleware('auth:web')->group(function(){
    Route::get('/friends', [\App\Http\Controllers\FriendController::class, 'index'])->name('friends.index');
    Route::post('/friends/send', [\App\Http\Controllers\FriendController::class, 'send'])->name('friends.send');
    Route::post('/friends/{friendship}/accept', [\App\Http\Controllers\FriendController::class, 'accept'])->name('friends.accept');
    Route::post('/friends/{friendship}/decline', [\App\Http\Controllers\FriendController::class, 'decline'])->name('friends.decline');
    Route::delete('/friends/{friendship}', [\App\Http\Controllers\FriendController::class, 'destroy'])->name('friends.delete');
    
    // Mensajería
    Route::get('/messages/{friend}', [\App\Http\Controllers\MessageController::class, 'index'])->name('messages.get');
    Route::post('/messages/{friend}', [\App\Http\Controllers\MessageController::class, 'store'])->name('messages.send');
    
    // Contador de notificaciones en tiempo real
    Route::get('/api/notifications/count', [\App\Http\Controllers\NotificationController::class, 'count'])->name('notifications.count');
    
    // Buscar usuarios para autocompletado (amigos e invitaciones)
    Route::get('/api/users/search', [\App\Http\Controllers\UserSearchController::class, 'search'])->name('users.search');
});

// API de búsqueda de juegos RAWG
Route::get('/rawg/search', [\App\Http\Controllers\RawgController::class, 'search'])->name('rawg.search');
Route::get('/rawg/popular', [\App\Http\Controllers\RawgController::class, 'popular'])->name('rawg.popular');
Route::post('/rawg/favorites/add', [\App\Http\Controllers\RawgController::class, 'addFavorite'])->name('rawg.favorites.add');
Route::post('/rawg/favorites/remove', [\App\Http\Controllers\RawgController::class, 'removeFavorite'])->name('rawg.favorites.remove');
Route::get('/rawg/favorites', [\App\Http\Controllers\RawgController::class, 'favorites'])->name('rawg.favorites');

// Juegos
Route::middleware('auth:web')->group(function(){
    Route::get('/games', [\App\Http\Controllers\GameController::class, 'index'])->name('games.index');
    Route::get('/games/create', [\App\Http\Controllers\GameController::class, 'create'])->name('games.create');
    Route::post('/games', [\App\Http\Controllers\GameController::class, 'store'])->name('games.store');
    Route::get('/games/{game}/edit', [\App\Http\Controllers\GameController::class, 'edit'])->name('games.edit');
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
    Route::get('/admin', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin.index');
});

/* Fin de rutas */
