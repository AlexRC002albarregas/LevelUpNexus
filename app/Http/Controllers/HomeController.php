<?php

namespace App\Http\Controllers;

use App\Services\NewsApiService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $newsApiService;

    public function __construct(NewsApiService $newsApiService)
    {
        $this->newsApiService = $newsApiService;
    }

    /**
     * Página de inicio
     */
    public function index()
    {
        // Si el usuario no está autenticado, mostrar landing
        if (!auth()->check()) {
            return view('landing');
        }

        // Si está autenticado, mostrar página de inicio con carrusel de noticias
        $newsData = $this->newsApiService->getGamingNews(20);
        $articles = $newsData['articles'] ?? [];

        return view('home.index', compact('articles'));
    }
}

