<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RawgService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.rawg.io/api';

    public function __construct()
    {
        $this->apiKey = config('services.rawg.key');
    }

    /**
     * Buscar juegos por nombre
     */
    public function searchGames($query, $page = 1, $pageSize = 10)
    {
        $cacheKey = "rawg_search_{$query}_{$page}_{$pageSize}";
        
        return Cache::remember($cacheKey, 3600, function () use ($query, $page, $pageSize) {
            $response = Http::get("{$this->baseUrl}/games", [
                'key' => $this->apiKey,
                'search' => $query,
                'page' => $page,
                'page_size' => $pageSize,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['results' => []];
        });
    }

    /**
     * Obtener detalles de un juego por ID
     */
    public function getGameDetails($gameId)
    {
        $cacheKey = "rawg_game_{$gameId}";
        
        return Cache::remember($cacheKey, 86400, function () use ($gameId) {
            $response = Http::get("{$this->baseUrl}/games/{$gameId}", [
                'key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        });
    }

    /**
     * Obtener juegos populares
     */
    public function getPopularGames($page = 1, $pageSize = 20)
    {
        $cacheKey = "rawg_popular_{$page}_{$pageSize}";
        
        return Cache::remember($cacheKey, 3600, function () use ($page, $pageSize) {
            $response = Http::get("{$this->baseUrl}/games", [
                'key' => $this->apiKey,
                'page' => $page,
                'page_size' => $pageSize,
                'ordering' => '-rating',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['results' => []];
        });
    }

    /**
     * Obtener información de múltiples juegos por sus IDs
     */
    public function getGamesByIds($gameIds)
    {
        if (empty($gameIds)) {
            return [];
        }

        $games = [];
        foreach ($gameIds as $id) {
            $game = $this->getGameDetails($id);
            if ($game) {
                $games[] = $game;
            }
        }

        return $games;
    }
}

