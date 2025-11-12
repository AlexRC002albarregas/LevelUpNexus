<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsApiService
{
    protected $apiKey;
    protected $baseUrl = 'https://newsapi.org/v2';

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key');
    }

    /**
     * Obtener noticias de videojuegos
     */
    public function getGamingNews($pageSize = 20)
    {
        try {
            $response = Http::get("{$this->baseUrl}/everything", [
                'q' => 'videogames OR gaming OR esports OR "video games"',
                'language' => 'es',
                'sortBy' => 'publishedAt',
                'pageSize' => $pageSize,
                'apiKey' => $this->apiKey,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('NewsAPI Error: ' . $response->body());
            return ['articles' => [], 'totalResults' => 0];
        } catch (\Exception $e) {
            Log::error('NewsAPI Exception: ' . $e->getMessage());
            return ['articles' => [], 'totalResults' => 0];
        }
    }
}

