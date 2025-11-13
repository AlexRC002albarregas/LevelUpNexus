<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, Friendship, Message};

class FriendController extends Controller
{
    /**
     * Muestra el listado de amistades y solicitudes pendientes.
     */
    public function index()
    {
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
    }

    /**
     * Envía una solicitud de amistad al usuario indicado.
     */
    public function send(Request $request)
    {
        $request->validate(['username' => 'required|string'], [
            'username.required' => 'Debes proporcionar un nombre de usuario o correo electrónico',
        ]);
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
        
        Friendship::create([
            'user_id' => auth()->id(),
            'friend_id' => $friend->id,
            'status' => 'pending'
        ]);
        
        return back()->with('status', 'Solicitud enviada a '.$friend->name);
    }

    /**
     * Acepta una solicitud de amistad recibida.
     */
    public function accept(Friendship $friendship)
    {
        if($friendship->friend_id !== auth()->id()){
            abort(403);
        }
        
        $friendship->update(['status' => 'accepted']);
        return back()->with('status', 'Solicitud aceptada');
    }

    /**
     * Rechaza una solicitud de amistad recibida.
     */
    public function decline(Friendship $friendship)
    {
        if($friendship->friend_id !== auth()->id()){
            abort(403);
        }
        
        $friendship->update(['status' => 'declined']);
        return back()->with('status', 'Solicitud rechazada');
    }

    /**
     * Elimina una amistad existente si pertenece al usuario.
     */
    public function destroy(Friendship $friendship)
    {
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
    }
}

