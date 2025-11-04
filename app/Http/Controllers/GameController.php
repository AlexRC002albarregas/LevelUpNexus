<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
		$games = Game::query()
			->when($request->user(), fn($q) => $q->where('user_id', $request->user()->id))
			->orderByDesc('id')
			->paginate(10);
		return response()->json($games);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGameRequest $request)
    {
        $validated = $request->validated();
        
        // Asegurar que is_favorite sea booleano
        if(isset($validated['is_favorite'])) {
            $validated['is_favorite'] = filter_var($validated['is_favorite'], FILTER_VALIDATE_BOOLEAN);
        } else {
            $validated['is_favorite'] = false;
        }
        
        $game = Game::create(array_merge($validated, ['user_id' => auth()->id()]));
        
        // Si es una petición AJAX, devolver JSON
        if($request->expectsJson() || $request->ajax()) {
            return response()->json($game, 201);
        }
        
        // Si no, redirigir a la lista de juegos
        return redirect()->route('games.index')->with('status', 'Juego añadido correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
		$this->authorize('view', $game);
		return response()->json($game);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGameRequest $request, Game $game)
    {
		$this->authorize('update', $game);
		
		$validated = $request->validated();
		
		// Asegurar que is_favorite sea booleano
		if(isset($validated['is_favorite'])) {
			$validated['is_favorite'] = filter_var($validated['is_favorite'], FILTER_VALIDATE_BOOLEAN);
		}
		
		$game->update($validated);
		
		// Si es una petición AJAX, devolver JSON
		if($request->expectsJson() || $request->ajax()) {
			return response()->json($game);
		}
		
		// Si no, redirigir a la lista de juegos
		return redirect()->route('games.index')->with('status', 'Juego actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
		$this->authorize('delete', $game);
		$game->delete();
		
		// Si es una petición AJAX, devolver JSON
		if(request()->expectsJson() || request()->ajax()) {
			return response()->json(['message' => 'Juego eliminado correctamente']);
		}
		
		// Si no, redirigir a la lista de juegos
		return redirect()->route('games.index')->with('status', 'Juego eliminado correctamente');
    }
}
