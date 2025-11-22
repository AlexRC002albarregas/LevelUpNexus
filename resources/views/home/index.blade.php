<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent mb-2">
                <i class="fas fa-home"></i> Bienvenido, {{ auth()->user()->name }}
            </h1>
            <p class="text-purple-300">¿Qué quieres hacer hoy?</p>
        </div>

        <!-- Accesos rápidos -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <a href="{{ route('posts.index') }}" class="p-6 rounded-xl bg-gradient-to-br from-purple-600/30 to-purple-800/30 border border-purple-500/50 hover:border-purple-400 transition group">
                <div class="text-4xl mb-3 text-purple-400 group-hover:scale-110 transition">
                    <i class="fas fa-newspaper"></i>
                </div>
                <h3 class="font-bold text-lg text-purple-100">Publicaciones</h3>
                <p class="text-sm text-purple-300">Ver el feed</p>
            </a>

            <a href="{{ route('friends.index') }}" class="p-6 rounded-xl bg-gradient-to-br from-pink-600/30 to-pink-800/30 border border-pink-500/50 hover:border-pink-400 transition group">
                <div class="text-4xl mb-3 text-pink-400 group-hover:scale-110 transition">
                    <i class="fas fa-user-friends"></i>
                </div>
                <h3 class="font-bold text-lg text-pink-100">Amigos</h3>
                <p class="text-sm text-pink-300">Ver amigos</p>
            </a>

            <a href="{{ route('groups.index') }}" class="p-6 rounded-xl bg-gradient-to-br from-blue-600/30 to-blue-800/30 border border-blue-500/50 hover:border-blue-400 transition group">
                <div class="text-4xl mb-3 text-blue-400 group-hover:scale-110 transition">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="font-bold text-lg text-blue-100">Grupos</h3>
                <p class="text-sm text-blue-300">Mis grupos</p>
            </a>

            <a href="{{ route('games.web.index') }}" class="p-6 rounded-xl bg-gradient-to-br from-green-600/30 to-green-800/30 border border-green-500/50 hover:border-green-400 transition group">
                <div class="text-4xl mb-3 text-green-400 group-hover:scale-110 transition">
                    <i class="fas fa-gamepad"></i>
                </div>
                <h3 class="font-bold text-lg text-green-100">Biblioteca</h3>
                <p class="text-sm text-green-300">Mis juegos</p>
            </a>
        </div>

        <!-- Carrusel de Noticias -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-purple-100">
                    <i class="fas fa-newspaper"></i> Últimas Noticias de Gaming
                </h2>
                <div class="flex gap-2">
                    <button onclick="scrollNews('left')" class="w-10 h-10 rounded-full bg-purple-600/50 hover:bg-purple-600 border border-purple-500 text-white transition flex items-center justify-center">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button onclick="scrollNews('right')" class="w-10 h-10 rounded-full bg-purple-600/50 hover:bg-purple-600 border border-purple-500 text-white transition flex items-center justify-center">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            @if(count($articles) > 0)
                <div class="relative">
                    <div id="newsCarousel" class="flex gap-4 overflow-x-auto scroll-smooth pb-4 scrollbar-hide" style="scrollbar-width: none;">
                        @foreach($articles as $article)
                            <article class="flex-shrink-0 w-80 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm overflow-hidden hover:border-purple-500/60 transition group">
                                <!-- Imagen -->
                                @if($article['urlToImage'])
                                    <div class="relative h-44 overflow-hidden bg-slate-900">
                                        <img 
                                            src="{{ $article['urlToImage'] }}" 
                                            alt="{{ $article['title'] }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                            onerror="this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-purple-900 to-pink-900 flex items-center justify-center\'><i class=\'fas fa-image text-4xl text-purple-400/50\'></i></div>'"
                                        >
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>
                                    </div>
                                @else
                                    <div class="h-44 bg-gradient-to-br from-purple-900 to-pink-900 flex items-center justify-center">
                                        <i class="fas fa-newspaper text-4xl text-purple-400/50"></i>
                                    </div>
                                @endif

                                <!-- Contenido -->
                                <div class="p-4">
                                    <!-- Fuente y Fecha -->
                                    <div class="flex items-center justify-between text-xs text-purple-400 mb-2">
                                        <span class="flex items-center gap-1 truncate">
                                            <i class="fas fa-newspaper"></i>
                                            <span class="truncate">{{ $article['source']['name'] ?? 'Desconocido' }}</span>
                                        </span>
                                        <span class="flex items-center gap-1 flex-shrink-0 ml-2">
                                            <i class="fas fa-clock"></i>
                                            {{ \Carbon\Carbon::parse($article['publishedAt'])->diffForHumans() }}
                                        </span>
                                    </div>

                                    <!-- Título -->
                                    <h3 class="font-bold text-purple-100 mb-2 line-clamp-2 min-h-[3rem] group-hover:text-purple-300 transition text-sm">
                                        {{ $article['title'] }}
                                    </h3>

                                    <!-- Descripción -->
                                    @if($article['description'])
                                        <p class="text-xs text-purple-300 mb-3 line-clamp-2">
                                            {{ $article['description'] }}
                                        </p>
                                    @endif

                                    <!-- Botón Leer Más -->
                                    <a 
                                        href="{{ $article['url'] }}" 
                                        target="_blank" 
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-2 text-xs font-semibold text-purple-400 hover:text-purple-300 transition"
                                    >
                                        Leer más
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm p-8 text-center">
                    <i class="fas fa-newspaper text-4xl text-purple-400/50 mb-3"></i>
                    <p class="text-purple-300">No se pudieron cargar las noticias en este momento.</p>
                    <p class="text-purple-400 text-sm mt-1">Verifica tu configuración de NEWS_API_KEY</p>
                </div>
            @endif
        </div>

        <!-- Info -->
        <div class="p-4 rounded-lg bg-purple-900/20 border border-purple-500/30 text-center">
            <p class="text-sm text-purple-400">
                <i class="fas fa-info-circle"></i> 
                Noticias proporcionadas por NewsAPI • Actualizadas en tiempo real
            </p>
        </div>
    </div>

    <style>
        /* Ocultar scrollbar en el carrusel */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <script>
        function scrollNews(direction) {
            const carousel = document.getElementById('newsCarousel');
            const scrollAmount = 336; // 320px (card width) + 16px (gap)
            
            if (direction === 'left') {
                carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            } else {
                carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }
        }

        // Auto-scroll cada 5 segundos
        let autoScrollInterval = setInterval(() => {
            const carousel = document.getElementById('newsCarousel');
            const maxScroll = carousel.scrollWidth - carousel.clientWidth;
            
            if (carousel.scrollLeft >= maxScroll) {
                carousel.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                scrollNews('right');
            }
        }, 5000);

        // Pausar auto-scroll cuando el usuario interactúa
        const carousel = document.getElementById('newsCarousel');
        if (carousel) {
            carousel.addEventListener('mouseenter', () => {
                clearInterval(autoScrollInterval);
            });

            carousel.addEventListener('mouseleave', () => {
                autoScrollInterval = setInterval(() => {
                    const maxScroll = carousel.scrollWidth - carousel.clientWidth;
                    if (carousel.scrollLeft >= maxScroll) {
                        carousel.scrollTo({ left: 0, behavior: 'smooth' });
                    } else {
                        scrollNews('right');
                    }
                }, 5000);
            });
        }
    </script>
</x-layouts.app>

