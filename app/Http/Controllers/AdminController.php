<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, Group, Post, Game};

class AdminController extends Controller
{
    /**
     * Muestra el panel principal de administración con listados básicos.
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        $groups = Group::latest()->paginate(10);
        $posts = Post::latest()->paginate(10);
        $games = Game::latest()->paginate(10);
        
        return view('admin.index', compact('users','groups','posts','games'));
    }
}

