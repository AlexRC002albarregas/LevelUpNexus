<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RawgService;

class RawgController extends Controller
{
    protected $rawgService;

    public function __construct(RawgService $rawgService)
    {
        $this->rawgService = $rawgService;
    }

    /**
     * Test endpoint for RAWG API (temporal).
     */
    public function test(Request $request)
    {
        $data = $this->rawgService->searchGames('zelda', 1, 5);
        return response()->json($data);
    }

    /**
     * Search games in RAWG API.
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            if(strlen($query) < 2){
                return response()->json(['results' => []]);
            }
            $data = $this->rawgService->searchGames($query, 1, 10);
            return response()->json($data);
        } catch(\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'results' => []
            ], 500);
        }
    }

    /**
     * Get popular games from RAWG API.
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
     * Add a game to favorites.
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
     * Remove a game from favorites.
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
     * Get favorite games.
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

