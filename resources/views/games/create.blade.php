<x-layouts.app :title="'Añadir juego - LevelUp Nexus'">
	<div class="max-w-6xl mx-auto">
		<div class="flex items-center justify-between mb-8">
			<h1 class="text-4xl font-black bg-gradient-to-r from-pink-400 to-purple-500 bg-clip-text text-transparent">
				<i class="fas fa-gamepad"></i> Añadir Juego a mi Biblioteca
			</h1>
			<a href="{{ route('games.web.index') }}" class="px-5 py-3 rounded-lg bg-slate-800 border border-purple-500/50 hover:bg-purple-500/20 text-purple-300 transition">
				<i class="fas fa-arrow-left"></i> Volver
			</a>
		</div>

		<!-- Instrucciones -->
		<div class="mb-6 p-4 rounded-xl bg-gradient-to-r from-purple-900/30 to-pink-900/30 border border-purple-500/50">
			<p class="text-purple-200 text-sm">
				<i class="fas fa-info-circle"></i> Busca un juego desde la API de RAWG y añádelo a tu biblioteca. Luego puedes añadir tus horas jugadas y otros detalles.
			</p>
		</div>

		<!-- Buscador -->
		<div class="mb-6">
			<div class="relative">
				<input 
					type="text" 
					id="gameSearch" 
					placeholder="Buscar juegos (ej: GTA, Zelda, FIFA...)" 
					class="w-full bg-slate-800 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
					autocomplete="off"
				>
			</div>
		</div>

		<!-- Juegos populares / Resultados de búsqueda -->
		<div id="gamesSection" class="mb-6">
			<h3 id="gamesSectionTitle" class="text-xl font-bold text-purple-200 mb-4">
				<i class="fas fa-fire"></i> Juegos Populares
			</h3>
			<div id="popularGamesGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
				<p class="text-purple-400 text-sm col-span-full text-center py-4">Cargando juegos populares...</p>
			</div>
		</div>

		<!-- Formulario de detalles (oculto hasta seleccionar juego) -->
		<div id="gameDetailsForm" class="hidden p-6 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm">
			<h3 id="gameDetailsHeading" class="text-2xl font-bold text-purple-200 mb-4">
				<i class="fas fa-edit"></i> Completa los detalles del juego
			</h3>
			
			<form id="addGameForm" method="POST" action="{{ route('games.web.store') }}" class="space-y-4">
				@csrf
				<input type="hidden" id="rawg_id" name="rawg_id">
				<input type="hidden" id="rawg_image" name="rawg_image">
				<input type="hidden" id="rawg_rating" name="rawg_rating">
				<input type="hidden" id="released_date" name="released_date">
				<input type="hidden" id="rawg_slug" name="rawg_slug">
				<input type="hidden" id="game_title" name="title">
				
				<!-- Preview del juego seleccionado -->
				<div id="selectedGamePreview" class="p-4 rounded-lg bg-slate-900/50 border border-purple-500/30 mb-4"></div>
				
				<div class="grid md:grid-cols-2 gap-4">
					<div>
						<label class="block text-sm font-semibold text-purple-200 mb-2">
							<i class="fas fa-gamepad"></i> Plataforma
						</label>
						<select name="platform" id="platform" required class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50">
							<!-- Las opciones se llenarán dinámicamente con JavaScript -->
							<option value="">Selecciona una plataforma...</option>
						</select>
						<p class="text-xs text-purple-400 mt-1" id="platformHelp"></p>
					</div>
					
					<div>
						<label class="block text-sm font-semibold text-purple-200 mb-2">
							<i class="fas fa-clock"></i> Horas jugadas
						</label>
						<input 
							name="hours_played" 
							type="number" 
							min="0" 
							value="0" 
							class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
						>
					</div>
				</div>
				
				<div>
					<label class="block text-sm font-semibold text-purple-200 mb-2">
						<i class="fas fa-tags"></i> Género (opcional)
					</label>
					<input 
						name="genre" 
						id="genre"
						class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
						placeholder="Ej: Acción, RPG, Aventura..."
					>
				</div>
				
				<div class="flex items-center gap-2">
					<input id="fav" name="is_favorite" type="checkbox" value="1" class="rounded w-5 h-5 text-purple-600">
					<label for="fav" class="text-purple-200 font-semibold">
						<i class="fas fa-star text-yellow-500"></i> Marcar como favorito
					</label>
				</div>
				
				<div class="flex gap-3 pt-4">
					<button 
						type="button" 
						onclick="resetForm()" 
						class="px-6 py-3 rounded-lg bg-slate-700 hover:bg-slate-600 text-white font-bold transition"
					>
						<i class="fas fa-times"></i> Cancelar
					</button>
					<button 
						type="submit" 
						class="px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold glow transition"
					>
						<i class="fas fa-save"></i> Guardar Juego
					</button>
				</div>
			</form>
		</div>
	</div>

	<script>
		let searchTimeout = null;
		let selectedGame = null;

		// Cargar juegos populares al inicio
		window.addEventListener('DOMContentLoaded', () => {
			loadPopularGames();
		});

		// Búsqueda en tiempo real
		document.getElementById('gameSearch').addEventListener('input', function(e) {
			const query = e.target.value.trim();
			
			if(query.length < 2) {
				// Si la búsqueda está vacía, volver a mostrar juegos populares
				loadPopularGames();
				return;
			}
			
			clearTimeout(searchTimeout);
			searchTimeout = setTimeout(() => searchGames(query), 500);
		});

		// Limpiar búsqueda cuando se presiona ESC
		document.getElementById('gameSearch').addEventListener('keydown', function(e) {
			if(e.key === 'Escape') {
				this.value = '';
				loadPopularGames();
			}
		});

		async function loadPopularGames() {
			const grid = document.getElementById('popularGamesGrid');
			const title = document.getElementById('gamesSectionTitle');
			
			// Cambiar título
			title.innerHTML = '<i class="fas fa-fire"></i> Juegos Populares';
			
			try {
				const res = await fetch('/rawg/popular', {
					credentials: 'same-origin',
					headers: {
						'Accept': 'application/json',
						'X-Requested-With': 'XMLHttpRequest'
					}
				});
				
				const data = await res.json();
				
				if(!data.results || data.results.length === 0) {
					grid.innerHTML = '<p class="text-purple-400 col-span-full text-center py-4">No se pudieron cargar juegos populares</p>';
					return;
				}
				
				grid.innerHTML = data.results.slice(0, 8).map(game => createGameCard(game)).join('');
			} catch(error) {
				console.error('Error cargando juegos populares:', error);
				grid.innerHTML = '<p class="text-red-400 col-span-full text-center py-4">Error al cargar juegos populares</p>';
			}
		}

		async function searchGames(query) {
			const grid = document.getElementById('popularGamesGrid');
			const title = document.getElementById('gamesSectionTitle');
			
			// Cambiar título a "Resultados de búsqueda"
			title.innerHTML = `<i class="fas fa-search"></i> Resultados de búsqueda para "${query}"`;
			
			// Mostrar estado de carga
			grid.innerHTML = '<p class="text-purple-400 col-span-full text-center py-4">Buscando...</p>';
			
			try {
				const res = await fetch(`/rawg/search?q=${encodeURIComponent(query)}`, {
					credentials: 'same-origin',
					headers: {
						'Accept': 'application/json',
						'X-Requested-With': 'XMLHttpRequest'
					}
				});
				
				const data = await res.json();
				
				// El backend ahora devuelve 'games' en lugar de 'results'
				const results = data.games || data.results || [];
				
				if(results.length === 0) {
					grid.innerHTML = '<p class="text-purple-400 col-span-full text-center py-4">No se encontraron juegos. Intenta con otro término de búsqueda.</p>';
					return;
				}
				
				// Adaptar formato si viene del nuevo endpoint
				const gamesData = results.map(game => {
					return {
						id: game.id,
						name: game.name,
						slug: game.slug || '',
						background_image: game.image || game.background_image,
						released: game.released,
						rating: game.rating,
						platforms: game.platforms || [],
						parent_platforms: game.parent_platforms || [],
						genres: game.genres || []
					};
				});
				
				// Mostrar resultados usando las mismas tarjetas que los juegos populares
				grid.innerHTML = gamesData.map(game => createGameCard(game)).join('');
			} catch(error) {
				console.error('Error buscando juegos:', error);
				grid.innerHTML = '<p class="text-red-400 col-span-full text-center py-4">Error al buscar juegos</p>';
			}
		}

		function createSearchResultItem(game) {
			return `
				<div onclick="selectGame(${JSON.stringify(game).replace(/"/g, '&quot;')})" class="p-3 hover:bg-purple-500/20 cursor-pointer border-b border-purple-500/20 transition">
					<div class="flex items-center gap-3">
						<img src="${game.background_image || 'https://via.placeholder.com/50'}" 
							class="w-12 h-12 rounded object-cover"
							alt="${game.name}">
						<div class="flex-1">
							<div class="font-bold text-purple-200">${game.name}</div>
							<div class="text-xs text-purple-400">
								${game.released ? game.released.split('-')[0] : 'N/A'} 
								${game.rating ? '· ⭐ ' + game.rating.toFixed(1) : ''}
							</div>
						</div>
					</div>
				</div>
			`;
		}

		function createGameCard(game) {
			return `
				<div onclick="selectGame(${JSON.stringify(game).replace(/"/g, '&quot;')})" class="group rounded-xl bg-slate-800/50 border border-purple-500/30 hover:border-purple-500 transition overflow-hidden card-hover cursor-pointer">
					<div class="aspect-video bg-slate-900 overflow-hidden">
						<img src="${game.background_image || 'https://via.placeholder.com/300x200'}" 
							class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" 
							alt="${game.name}">
					</div>
					<div class="p-3">
						<h6 class="font-bold text-sm text-purple-200 mb-1 truncate" title="${game.name}">${game.name}</h6>
						<div class="flex items-center justify-between text-xs text-purple-400">
							<span>${game.released ? game.released.split('-')[0] : 'N/A'}</span>
							${game.rating ? `<span><i class="fas fa-star text-yellow-500"></i> ${game.rating.toFixed(1)}</span>` : ''}
						</div>
					</div>
				</div>
			`;
		}

		function selectGame(game) {
			selectedGame = game;
			
			// Limpiar búsqueda al seleccionar un juego
			document.getElementById('gameSearch').value = '';
			
			// Llenar campos ocultos
			document.getElementById('rawg_id').value = game.id;
			document.getElementById('rawg_image').value = game.background_image || '';
			document.getElementById('rawg_rating').value = game.rating || '';
			document.getElementById('released_date').value = game.released || '';
			document.getElementById('rawg_slug').value = game.slug || '';
			document.getElementById('game_title').value = game.name;
			
			// Procesar plataformas disponibles
			updatePlatformSelect(game);
			
			// Mostrar preview con plataformas disponibles
			const preview = document.getElementById('selectedGamePreview');
			let platformsText = '';
			if (game.platforms && game.platforms.length > 0) {
				const platformNames = game.platforms
					.map(p => p.platform?.name)
					.filter(Boolean)
					.slice(0, 5);
				if (platformNames.length > 0) {
					platformsText = `<div class="text-xs text-purple-300 mt-2">
						<i class="fas fa-gamepad"></i> ${platformNames.join(', ')}${platformNames.length < game.platforms.length ? '...' : ''}
					</div>`;
				}
			}
			
			preview.innerHTML = `
				<div class="flex items-center gap-4">
					<img src="${game.background_image || 'https://via.placeholder.com/100'}" 
						class="w-20 h-20 rounded-lg object-cover"
						alt="${game.name}">
					<div class="flex-1">
						<h4 class="font-bold text-xl text-purple-200">${game.name}</h4>
						<div class="text-sm text-purple-400 mt-1">
							${game.released ? '<span><i class="fas fa-calendar"></i> ' + game.released.split('-')[0] + '</span>' : ''}
							${game.rating ? ' · <span><i class="fas fa-star text-yellow-500"></i> ' + game.rating.toFixed(1) + '</span>' : ''}
						</div>
						${platformsText}
					</div>
				</div>
			`;
			
			// Llenar género si está disponible
			const genreField = document.getElementById('genre');
			let genreText = '';
			if(Array.isArray(game.genres) && game.genres.length > 0) {
				genreText = game.genres.map(g => g.name ?? g).join(', ');
			} else if(Array.isArray(game.genre_names) && game.genre_names.length > 0) {
				genreText = game.genre_names.join(', ');
			} else if(game.genre) {
				genreText = game.genre;
			}
			if(genreText) {
				genreField.value = genreText;
			} else {
				genreField.value = '';
			}
			
			// Mostrar formulario
			const detailsForm = document.getElementById('gameDetailsForm');
			detailsForm.classList.remove('hidden');

			const detailsHeading = document.getElementById('gameDetailsHeading');
			(detailsHeading || detailsForm).scrollIntoView({ behavior: 'smooth', block: 'center' });
			
			// Volver a mostrar juegos populares
			loadPopularGames();
		}

		// Función para mapear plataformas de RAWG a nuestras categorías
		function mapRawgPlatformToCategory(platformSlug, platformName) {
			const slug = platformSlug.toLowerCase();
			const name = platformName.toLowerCase();
			
			// PC
			if (slug.includes('pc') || slug.includes('linux') || slug.includes('mac')) {
				return 'pc';
			}
			
			// Xbox
			if (slug.includes('xbox') || name.includes('xbox')) {
				return 'xbox';
			}
			
			// PlayStation
			if (slug.includes('playstation') || slug.includes('ps') || name.includes('playstation')) {
				return 'playstation';
			}
			
			// Nintendo Switch
			if (slug.includes('switch') || slug.includes('nintendo') || name.includes('nintendo')) {
				return 'switch';
			}
			
			// Mobile
			if (slug.includes('ios') || slug.includes('android') || slug.includes('iphone') || slug.includes('ipad') || name.includes('mobile')) {
				return 'mobile';
			}
			
			// Por defecto: other
			return 'other';
		}

		// Actualizar select de plataformas basado en las plataformas disponibles del juego
		function updatePlatformSelect(game) {
			const platformSelect = document.getElementById('platform');
			const platformHelp = document.getElementById('platformHelp');
			
			// Mapeo de nuestras categorías a nombres legibles
			const categoryNames = {
				'pc': 'PC',
				'xbox': 'Xbox',
				'playstation': 'PlayStation',
				'switch': 'Nintendo Switch',
				'mobile': 'Mobile',
				'other': 'Otra'
			};
			
			// Obtener plataformas disponibles del juego
			const availablePlatforms = new Set();
			
			const collectPlatform = (entry) => {
				if(!entry) return;
				// Manejar diferentes estructuras posibles
				const slug = entry.platform?.slug ?? entry.slug ?? '';
				const name = entry.platform?.name ?? entry.name ?? '';
				if(slug || name) {
					const category = mapRawgPlatformToCategory(slug, name);
					availablePlatforms.add(category);
				}
			};
			
			if (game.platforms && game.platforms.length > 0) {
				// Extraer plataformas de la estructura de RAWG
				game.platforms.forEach(collectPlatform);
			}
			
			// Si no hay plataformas en platforms, intentar con parent_platforms
			if (availablePlatforms.size === 0 && game.parent_platforms && game.parent_platforms.length > 0) {
				game.parent_platforms.forEach(collectPlatform);
			}
			
			// Limpiar select
			platformSelect.innerHTML = '';
			
			if (availablePlatforms.size > 0) {
				// Agregar opciones solo para plataformas disponibles
				const sortedPlatforms = Array.from(availablePlatforms).sort();
				sortedPlatforms.forEach(category => {
					const option = document.createElement('option');
					option.value = category;
					option.textContent = categoryNames[category] || category;
					platformSelect.appendChild(option);
				});
				
				platformHelp.textContent = `Plataformas disponibles para este juego`;
				platformHelp.className = 'text-xs text-purple-400 mt-1';
			} else {
				// Si no hay plataformas detectadas, mostrar todas
				Object.keys(categoryNames).forEach(category => {
					const option = document.createElement('option');
					option.value = category;
					option.textContent = categoryNames[category];
					platformSelect.appendChild(option);
				});
				
				platformHelp.textContent = 'No se detectaron plataformas específicas. Selecciona manualmente.';
				platformHelp.className = 'text-xs text-yellow-400 mt-1';
			}
		}

		function resetForm() {
			selectedGame = null;
			document.getElementById('gameDetailsForm').classList.add('hidden');
			document.getElementById('addGameForm').reset();
			document.getElementById('selectedGamePreview').innerHTML = '';
			document.getElementById('gameSearch').value = '';
			
			// Resetear select de plataformas
			const platformSelect = document.getElementById('platform');
			platformSelect.innerHTML = '<option value="">Selecciona una plataforma...</option>';
			document.getElementById('platformHelp').textContent = '';
		}

		// Manejar envío del formulario
		document.getElementById('addGameForm').addEventListener('submit', async function(e) {
			e.preventDefault();
			
			const formData = new FormData(this);
			const submitBtn = this.querySelector('button[type="submit"]');
			const originalText = submitBtn.innerHTML;
			
			submitBtn.disabled = true;
			submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
			
			try {
				const res = await fetch(this.action, {
					method: 'POST',
					body: formData,
					credentials: 'same-origin',
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'Accept': 'application/json'
					}
				});
				
				const data = await res.json();
				
				if(res.ok) {
					window.location.href = '{{ route("games.web.index") }}';
				} else {
					alert('Error: ' + (data.message || 'Error al guardar el juego'));
					submitBtn.disabled = false;
					submitBtn.innerHTML = originalText;
				}
			} catch(error) {
				console.error('Error:', error);
				alert('Error al guardar el juego');
				submitBtn.disabled = false;
				submitBtn.innerHTML = originalText;
			}
		});
	</script>
</x-layouts.app>
