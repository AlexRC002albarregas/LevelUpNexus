<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Lista los juegos del usuario aplicando filtros y ordenaciones.
     */
    public function index(Request $request)
    {
        // Si es una petición AJAX/API, devolver JSON
        if($request->expectsJson() || $request->ajax()) {
            $games = Game::query()
                ->when($request->user(), fn($q) => $q->where('user_id', $request->user()->id))
                ->orderByDesc('id')
                ->paginate(10);
            return response()->json($games);
        }
        
        // Para peticiones web normales, devolver la vista con filtros
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
    }

    /**
     * Muestra el formulario de creación de un juego.
     */
    public function create()
    {
        return view('games.create');
    }

    /**
     * Guarda un nuevo juego para el usuario autenticado.
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
     * Devuelve los datos de un juego concreto tras autorizar el acceso.
     */
    public function show(Game $game)
    {
		$this->authorize('view', $game);
		return response()->json($game);
    }

    /**
     * Muestra el formulario de edición de un juego.
     */
    public function edit(Game $game)
    {
        $this->authorize('update', $game);
        return view('games.edit', compact('game'));
    }

    /**
     * Actualiza los campos de un juego existente.
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
     * Elimina un juego y responde según el tipo de petición.
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
