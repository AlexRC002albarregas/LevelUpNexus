<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, Message, Friendship};

class MessageController extends Controller
{
    /**
     * Recupera la conversación completa con un amigo.
     */
    public function index(User $friend)
    {
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
    }

    /**
     * Envía un nuevo mensaje privado a un amigo.
     */
    public function store(Request $request, User $friend)
    {
        $request->validate(['content' => 'required|string|max:1000'], [
            'content.required' => 'El mensaje no puede estar vacío',
            'content.max' => 'El mensaje no puede exceder los 1000 caracteres',
        ]);
        
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
    }
}

