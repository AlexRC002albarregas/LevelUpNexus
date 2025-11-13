<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RawgService;

class RawgController extends Controller
{
    protected $rawgService;

    /**
     * Inyecta el servicio que interactúa con la API de RAWG.
     */
    public function __construct(RawgService $rawgService)
    {
        $this->rawgService = $rawgService;
    }

    /**
     * Punto de prueba temporal para la API de RAWG.
     */
    public function test(Request $request)
    {
        $data = $this->rawgService->searchGames('zelda', 1, 5);
        return response()->json($data);
    }

    /**
     * Busca juegos en RAWG y adapta los datos al frontend.
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            if(strlen($query) < 2){
                return response()->json(['games' => []]);
            }
            
            $data = $this->rawgService->searchGames($query, 1, 10);
            
            // Adaptar formato para el frontend
            $games = [];
            if(isset($data['results'])) {
                foreach($data['results'] as $game) {
                    $platforms = [];
                    if(isset($game['platforms'])) {
                        foreach($game['platforms'] as $platform) {
                            $platforms[] = $platform['platform']['name'];
                        }
                    }
                    
                    $games[] = [
                        'id' => $game['id'],
                        'name' => $game['name'],
                        'image' => $game['background_image'] ?? null,
                        'background_image' => $game['background_image'] ?? null, // Para compatibilidad
                        'platforms' => $platforms,
                        'released' => $game['released'] ?? null,
                        'rating' => $game['rating'] ?? null,
                    ];
                }
            }
            
            return response()->json(['games' => $games]);
        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'games' => []
            ], 500);
        }
    }

    /**
     * Obtiene la lista de juegos populares desde RAWG.
     */
    public function popular()
    {
        try {
            $data = $this->rawgService->getPopularGames(1, 20);
            return response()->json($data);
        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'results' => []
            ], 500);
        }
    }

    /**
     * Añade un juego al listado de favoritos del usuario.
     */
    public function addFavorite(Request $request)
    {
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
    }

    /**
     * Elimina un juego del listado de favoritos del usuario.
     */
    public function removeFavorite(Request $request)
    {
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
    }

    /**
     * Recupera juegos favoritos propios u ofrecidos por ID.
     */
    public function favorites(Request $request)
    {
        // Si hay parámetro 'ids', devolver esos juegos (para ver perfiles de otros usuarios)
        if($request->has('ids')) {
            $ids = explode(',', $request->ids);
            $ids = array_map('intval', $ids);
            $games = $this->rawgService->getGamesByIds($ids);
            return response()->json(['games' => $games]);
        }
        
        // Si no, devolver los favoritos del usuario autenticado
        $user = auth()->user();
        if(!$user) {
            return response()->json(['error' => 'No autenticado', 'games' => []], 401);
        }
        
        $favoriteIds = $user->favorite_games ?? [];
        $games = $this->rawgService->getGamesByIds($favoriteIds);
        return response()->json(['games' => $games]);
    }
}

