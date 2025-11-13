<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserSearchController extends Controller
{
    /**
     * Busca usuarios para autocompletar en solicitudes y amistades.
     */
    public function search(Request $request)
    {
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
    }
}

