<x-layouts.app :title="'Mi Cuenta - LevelUp Nexus'">
	<div class="max-w-3xl mx-auto">
		<h1 class="text-4xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent mb-8">
			<i class="fas fa-user-cog"></i> Configuración de Cuenta
		</h1>

		<div class="grid md:grid-cols-3 gap-8">
			<!-- Avatar Preview -->
			<div class="md:col-span-1">
				<div class="p-6 rounded-2xl bg-slate-800/50 border border-purple-500 backdrop-blur-sm glow text-center">
					<div class="w-32 h-32 mx-auto rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-black text-4xl border-4 border-purple-400 glow mb-4 overflow-hidden">
						@if(auth()->user()->avatar)
							<img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="w-full h-full object-cover" alt="Avatar">
						@else
							{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
						@endif
					</div>
					<h3 class="font-bold text-xl text-purple-200">{{ auth()->user()->name }}</h3>
					<div class="text-sm text-purple-400 mt-1">{{ auth()->user()->email }}</div>
					<div class="mt-4 px-3 py-1 rounded-full bg-purple-600/20 border border-purple-500 text-sm font-semibold inline-block">
						<i class="fas fa-crown"></i> {{ ucfirst(auth()->user()->role) }}
					</div>
				</div>
			</div>

			<!-- Formulario -->
			<div class="md:col-span-2">
				<form method="POST" action="{{ route('account.update') }}" enctype="multipart/form-data" class="space-y-6 p-8 rounded-2xl bg-slate-800/50 border border-purple-500 backdrop-blur-sm glow">
					@csrf
					
					<div>
						<label class="block text-sm mb-2 text-purple-300 font-semibold">
							<i class="fas fa-user"></i> Nombre de usuario
						</label>
						<input name="name" value="{{ old('name', auth()->user()->name) }}" required class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50">
					</div>

					<div>
						<label class="block text-sm mb-2 text-purple-300 font-semibold">
							<i class="fas fa-envelope"></i> Email
						</label>
						<input name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50">
					</div>

					<div>
						<label class="block text-sm mb-2 text-purple-300 font-semibold">
							<i class="fas fa-image"></i> Foto de perfil
						</label>
						<input name="avatar" type="file" accept="image/*" class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50">
						<p class="text-xs text-purple-400 mt-1">Opcional: JPG, PNG, GIF o WEBP (máx. 2MB)</p>
					</div>

					<div>
						<label class="block text-sm mb-2 text-purple-300 font-semibold">
							<i class="fas fa-pen"></i> Biografía
						</label>
						<textarea name="bio" rows="4" maxlength="500" placeholder="Cuéntanos sobre ti, tus logros, tu estilo de juego..." class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 resize-none">{{ old('bio', auth()->user()->bio) }}</textarea>
						<p class="text-xs text-purple-400 mt-1">Máximo 500 caracteres</p>
					</div>

					<div>
						<label class="block text-sm mb-2 text-purple-300 font-semibold">
							<i class="fas fa-gamepad"></i> Juegos favoritos
						</label>
						<button type="button" onclick="openFavoritesModal()" class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-left text-purple-300 hover:border-purple-500 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 transition">
							<span id="favoritesCount">{{ count(auth()->user()->favorite_games ?? []) }}</span> juego(s) seleccionado(s) - Click para editar
						</button>
						<div id="favoritesList" class="mt-2 flex flex-wrap gap-2"></div>
					</div>

					<!-- Privacidad del perfil -->
					<div class="p-4 rounded-xl bg-gradient-to-r from-purple-900/30 to-pink-900/30 border border-purple-500/50">
						<div class="flex items-center justify-between">
							<div>
								<label class="block text-sm font-semibold text-purple-200 mb-1">
									<i class="fas fa-shield-alt"></i> Privacidad del Perfil
								</label>
								<p class="text-xs text-purple-400">Los perfiles privados solo son visibles para tus amigos</p>
							</div>
							<label class="relative inline-flex items-center cursor-pointer">
								<input type="checkbox" name="is_private" value="1" {{ auth()->user()->is_private ? 'checked' : '' }} class="sr-only peer">
								<div class="w-14 h-7 bg-slate-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-600"></div>
								<span class="ms-3 text-sm font-medium text-purple-300">
									{{ auth()->user()->is_private ? 'Privado' : 'Público' }}
								</span>
							</label>
						</div>
					</div>

					<div class="border-t border-purple-500/30 pt-6">
						<h3 class="font-bold text-lg text-purple-200 mb-4">
							<i class="fas fa-lock"></i> Cambiar Contraseña
						</h3>
						
						<div class="space-y-4">
							<div>
								<label class="block text-sm mb-2 text-purple-300 font-semibold">Nueva contraseña</label>
								<input name="password" type="password" class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50" placeholder="Dejar vacío para no cambiar">
							</div>

							<div>
								<label class="block text-sm mb-2 text-purple-300 font-semibold">Confirmar contraseña</label>
								<input name="password_confirmation" type="password" class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50" placeholder="Repetir nueva contraseña">
							</div>
						</div>
					</div>

					<div class="flex gap-3 pt-4">
						<button type="submit" class="flex-1 px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold glow transition">
							<i class="fas fa-save"></i> Guardar Cambios
						</button>
						<a href="{{ route('landing') }}" class="px-6 py-3 rounded-lg border border-purple-500 hover:bg-purple-500/20 transition">
							<i class="fas fa-times"></i> Cancelar
						</a>
					</div>
				</form>
				
				<!-- Zona de peligro: Eliminar cuenta -->
				<div class="mt-6 p-6 rounded-2xl bg-red-900/20 border border-red-500/50 backdrop-blur-sm">
					<h3 class="font-bold text-lg text-red-400 mb-2">
						<i class="fas fa-exclamation-triangle"></i> Zona de Peligro
					</h3>
					<p class="text-sm text-red-300 mb-4">
						Una vez que elimines tu cuenta, no hay vuelta atrás. Todos tus datos serán eliminados permanentemente.
					</p>
					<button type="button" onclick="document.getElementById('deleteAccountModal').classList.remove('hidden')" class="px-6 py-3 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold transition">
						<i class="fas fa-trash-alt"></i> Eliminar mi cuenta
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal de Juegos Favoritos -->
	<div id="favoritesModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
		<div class="bg-slate-900 rounded-2xl w-full max-w-5xl border border-purple-500 glow flex flex-col" style="height: 85vh;">
			<div class="p-4 border-b border-purple-500/30 flex items-center justify-between flex-shrink-0">
				<h3 class="text-xl font-bold bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">
					<i class="fas fa-gamepad"></i> Selecciona tus juegos favoritos
				</h3>
				<button onclick="closeFavoritesModal()" class="text-purple-400 hover:text-white transition">
					<i class="fas fa-times text-2xl"></i>
				</button>
			</div>
			
			<!-- Buscador fijo arriba -->
			<div class="p-4 border-b border-purple-500/30 flex-shrink-0">
				<input type="text" id="gameSearch" placeholder="Buscar juegos (ej: GTA, Zelda, FIFA...)" class="w-full bg-slate-800 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50">
			</div>
			
			<div class="flex-1 overflow-y-auto">
				<!-- Mis favoritos actuales (compacto) -->
				<div id="currentFavorites" class="p-4 border-b border-purple-500/30">
					<h4 class="font-bold text-sm text-purple-300 mb-2 flex items-center gap-2">
						<i class="fas fa-star text-yellow-500"></i> 
						Mis favoritos (<span id="favCount">0</span>)
					</h4>
					<div id="currentFavoritesGrid" class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-6 gap-2"></div>
				</div>
				
				<!-- Resultados de búsqueda (más espacio) -->
				<div id="searchResults" class="p-4">
					<h4 class="font-bold text-sm text-purple-300 mb-3"><i class="fas fa-search"></i> Resultados</h4>
					<div id="searchResultsGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3"></div>
				</div>
			</div>
			
			<div class="p-6 border-t border-purple-500/30">
				<button onclick="closeFavoritesModal()" class="w-full px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold glow transition">
					<i class="fas fa-check"></i> Guardar y cerrar
				</button>
			</div>
		</div>
	</div>

	<script>
		let searchTimeout = null;
		let userFavorites = @json(auth()->user()->favorite_games ?? []);

		// Cargar favoritos al cargar la página
		window.addEventListener('DOMContentLoaded', () => {
			updateFavoritesDisplay(); // Cargar favoritos en la vista principal
		});

		function openFavoritesModal() {
			document.getElementById('favoritesModal').classList.remove('hidden');
			loadFavorites();
			loadPopularGames();
		}

		function closeFavoritesModal() {
			document.getElementById('favoritesModal').classList.add('hidden');
			updateFavoritesDisplay();
		}

		async function loadFavorites() {
			console.log('Cargando favoritos del usuario...');
			const grid = document.getElementById('currentFavoritesGrid');
			const counter = document.getElementById('favCount');
			
			try {
				const res = await fetch('/rawg/favorites', {
					credentials: 'same-origin',
					headers: {
						'Accept': 'application/json',
						'X-Requested-With': 'XMLHttpRequest'
					}
				});
				console.log('Response status (favorites):', res.status);
				const data = await res.json();
				console.log('Favoritos recibidos:', data);
				
				if(counter) counter.textContent = data.games ? data.games.length : 0;
				
				if(!data.games || data.games.length === 0) {
					grid.innerHTML = '<p class="text-purple-400 col-span-full text-center py-2 text-sm">No tienes juegos favoritos aún</p>';
					return;
				}
				
				grid.innerHTML = data.games.map(game => createCompactCard(game)).join('');
			} catch(error) {
				console.error('Error cargando favoritos:', error);
				grid.innerHTML = '<p class="text-red-400 col-span-full text-center py-2 text-sm">Error al cargar favoritos</p>';
			}
		}

		async function loadPopularGames() {
			console.log('Cargando juegos populares...');
			const grid = document.getElementById('searchResultsGrid');
			grid.innerHTML = '<p class="text-purple-400 col-span-full text-center py-4">Cargando juegos populares...</p>';
			
			try {
				const res = await fetch('/rawg/popular', {
					credentials: 'same-origin',
					headers: {
						'Accept': 'application/json',
						'X-Requested-With': 'XMLHttpRequest'
					}
				});
				console.log('Response status (popular):', res.status);
				const data = await res.json();
				console.log('Juegos populares recibidos:', data);
				
				if(!data.results || data.results.length === 0) {
					grid.innerHTML = '<p class="text-purple-400 col-span-full text-center py-4">No se pudieron cargar juegos populares</p>';
					return;
				}
				
				grid.innerHTML = data.results.map(game => createGameCard(game, userFavorites.includes(game.id))).join('');
			} catch(error) {
				console.error('Error cargando juegos populares:', error);
				grid.innerHTML = '<p class="text-red-400 col-span-full text-center py-4">Error al cargar juegos. Verifica la consola.</p>';
			}
		}

		// Búsqueda con debounce
		document.addEventListener('DOMContentLoaded', () => {
			const searchInput = document.getElementById('gameSearch');
			if(searchInput) {
				searchInput.addEventListener('input', (e) => {
					clearTimeout(searchTimeout);
					const query = e.target.value.trim();
					
					if(query.length < 2) {
						loadPopularGames();
						return;
					}
					
					searchTimeout = setTimeout(() => searchGames(query), 500);
				});
			}
		});

		async function searchGames(query) {
			console.log('Buscando:', query);
			const grid = document.getElementById('searchResultsGrid');
			grid.innerHTML = '<p class="text-purple-400 col-span-full text-center py-4">Buscando...</p>';
			
			try {
				const res = await fetch(`/rawg/search?q=${encodeURIComponent(query)}`, {
					credentials: 'same-origin',
					headers: {
						'Accept': 'application/json',
						'X-Requested-With': 'XMLHttpRequest'
					}
				});
				console.log('Response status:', res.status);
				
				// Obtener el texto primero para debugging
				const text = await res.text();
				console.log('Response text (primeros 200 chars):', text.substring(0, 200));
				
				// Intentar parsear como JSON
				let data;
				try {
					data = JSON.parse(text);
				} catch(e) {
					console.error('Error parseando JSON:', e);
					console.error('Texto completo de respuesta:', text);
					grid.innerHTML = '<p class="text-red-400 col-span-full text-center py-4">Error: La respuesta no es JSON válido</p>';
					return;
				}
				
				console.log('Data parseada:', data);
				
				if(!data.results || data.results.length === 0) {
					grid.innerHTML = '<p class="text-purple-400 col-span-full text-center py-4">No se encontraron juegos</p>';
					return;
				}
				
				grid.innerHTML = data.results.map(game => createGameCard(game, userFavorites.includes(game.id))).join('');
			} catch(error) {
				console.error('Error buscando juegos:', error);
				grid.innerHTML = '<p class="text-red-400 col-span-full text-center py-4">Error al buscar juegos. Verifica la consola.</p>';
			}
		}

		// Tarjeta compacta para favoritos
		function createCompactCard(game) {
			return `
				<div class="relative group cursor-pointer" onclick="toggleFavorite(${game.id})" title="${game.name}">
					<img src="${game.background_image || 'https://via.placeholder.com/100?text=No+Image'}" class="w-full aspect-square object-cover rounded-lg border-2 border-pink-500" alt="${game.name}">
					<div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center">
						<i class="fas fa-times text-white text-xl"></i>
					</div>
				</div>
			`;
		}

		// Tarjeta normal para resultados de búsqueda
		function createGameCard(game, isFavorite) {
			return `
				<div class="p-3 rounded-xl bg-slate-800/50 border ${isFavorite ? 'border-pink-500' : 'border-purple-500/30'} hover:border-purple-500 transition cursor-pointer" onclick="toggleFavorite(${game.id})">
					<img src="${game.background_image || 'https://via.placeholder.com/300x200?text=No+Image'}" class="w-full h-24 object-cover rounded-lg mb-2" alt="${game.name}">
					<h5 class="font-bold text-sm text-purple-200 truncate">${game.name}</h5>
					<div class="flex items-center justify-between mt-2">
						<span class="text-xs text-purple-400"><i class="fas fa-star text-yellow-500"></i> ${game.rating || 'N/A'}</span>
						<button class="text-sm ${isFavorite ? 'text-pink-500' : 'text-purple-400'}">
							<i class="fas fa-heart${isFavorite ? '' : '-o'}"></i>
						</button>
					</div>
				</div>
			`;
		}

		async function toggleFavorite(gameId) {
			const isFavorite = userFavorites.includes(gameId);
			const endpoint = isFavorite ? '/rawg/favorites/remove' : '/rawg/favorites/add';
			
			const res = await fetch(endpoint, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				body: JSON.stringify({ game_id: gameId })
			});
			
			if(res.ok) {
				const data = await res.json();
				userFavorites = data.favorites;
				loadFavorites();
				
				// Recargar búsqueda actual
				const searchInput = document.getElementById('gameSearch');
				const query = searchInput.value.trim();
				if(query.length >= 2) {
					searchGames(query);
				} else {
					loadPopularGames();
				}
			}
		}

		function updateFavoritesDisplay() {
			document.getElementById('favoritesCount').textContent = userFavorites.length;
			
			// Actualizar preview de favoritos en el formulario
			const container = document.getElementById('favoritesList');
			
			if(userFavorites.length === 0) {
				container.innerHTML = '<p class="text-purple-400 text-sm">No has seleccionado juegos favoritos aún. Click en el botón de arriba para añadir.</p>';
				return;
			}
			
			container.innerHTML = '<p class="text-purple-400 text-sm mb-3">Cargando tus juegos favoritos...</p>';
			
			fetch('/rawg/favorites').then(res => res.json()).then(data => {
				if(data.games && data.games.length > 0) {
					container.innerHTML = `
						<div class="flex flex-wrap gap-2">
							${data.games.map(game => `
								<div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-800/50 border border-pink-500/50 hover:border-pink-500 transition group">
									<img src="${game.background_image || 'https://via.placeholder.com/50'}" class="w-10 h-10 rounded object-cover flex-shrink-0" alt="${game.name}">
									<div class="flex-1 min-w-0">
										<h6 class="font-bold text-xs text-purple-200 truncate" title="${game.name}">${game.name}</h6>
										<div class="flex items-center gap-2 text-xs text-purple-400">
											<span><i class="fas fa-star text-yellow-500"></i> ${game.rating || 'N/A'}</span>
											<i class="fas fa-heart text-pink-500"></i>
										</div>
									</div>
								</div>
							`).join('')}
						</div>
					`;
				} else {
					container.innerHTML = '<p class="text-purple-400 text-sm">No has seleccionado juegos favoritos aún.</p>';
				}
			}).catch(error => {
				console.error('Error cargando favoritos:', error);
				container.innerHTML = '<p class="text-red-400 text-sm">Error al cargar juegos favoritos</p>';
			});
		}

		// Actualizar texto del toggle de privacidad dinámicamente
		const privacyToggle = document.querySelector('input[name="is_private"]');
		const privacyLabel = privacyToggle.nextElementSibling.nextElementSibling;
		
		privacyToggle.addEventListener('change', function() {
			privacyLabel.textContent = this.checked ? 'Privado' : 'Público';
		});
	</script>

	<!-- Modal de Confirmación de Eliminación de Cuenta -->
	<div id="deleteAccountModal" class="hidden fixed inset-0 bg-black/90 backdrop-blur-sm flex items-center justify-center z-50 p-4">
		<div class="bg-slate-900 rounded-2xl w-full max-w-md border-2 border-red-500 glow">
			<div class="p-6 border-b border-red-500/30">
				<div class="flex items-center gap-3 mb-2">
					<div class="w-12 h-12 rounded-full bg-red-600/20 border border-red-500 flex items-center justify-center">
						<i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
					</div>
					<h3 class="text-2xl font-bold text-red-400">
						Eliminar Cuenta
					</h3>
				</div>
				<p class="text-red-300 text-sm">
					Esta acción es <strong>irreversible</strong>. Todos tus datos serán eliminados permanentemente.
				</p>
			</div>
			
			<form method="POST" action="{{ route('account.delete') }}" class="p-6">
				@csrf
				@method('DELETE')
				
				<div class="space-y-4 mb-6">
					<div>
						<label class="block text-sm mb-2 text-red-300 font-semibold">
							<i class="fas fa-envelope"></i> Confirma tu correo electrónico
						</label>
						<input name="email" type="email" required class="w-full bg-slate-800 border border-red-500/50 rounded-lg px-4 py-3 text-white placeholder-red-400/50 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/50" placeholder="{{ auth()->user()->email }}">
						<p class="text-xs text-red-400 mt-1">Introduce tu correo para confirmar</p>
					</div>
					
					<div>
						<label class="block text-sm mb-2 text-red-300 font-semibold">
							<i class="fas fa-lock"></i> Confirma tu contraseña
						</label>
						<input name="password" type="password" required class="w-full bg-slate-800 border border-red-500/50 rounded-lg px-4 py-3 text-white placeholder-red-400/50 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/50" placeholder="Tu contraseña actual">
						<p class="text-xs text-red-400 mt-1">Introduce tu contraseña para confirmar</p>
					</div>
				</div>
				
				<div class="p-4 rounded-lg bg-red-900/30 border border-red-500/50 mb-6">
					<h4 class="font-bold text-red-300 mb-2">Se eliminarán:</h4>
					<ul class="text-sm text-red-400 space-y-1">
						<li><i class="fas fa-check"></i> Tu perfil y datos personales</li>
						<li><i class="fas fa-check"></i> Todas tus publicaciones y comentarios</li>
						<li><i class="fas fa-check"></i> Tus amigos y mensajes</li>
						<li><i class="fas fa-check"></i> Tus juegos y configuraciones</li>
					</ul>
				</div>
				
				<div class="flex gap-3">
					<button type="button" onclick="document.getElementById('deleteAccountModal').classList.add('hidden')" class="flex-1 px-6 py-3 rounded-lg border border-purple-500 hover:bg-purple-500/20 transition">
						<i class="fas fa-times"></i> Cancelar
					</button>
					<button type="submit" class="flex-1 px-6 py-3 rounded-lg bg-red-600 hover:bg-red-700 font-bold text-white transition">
						<i class="fas fa-trash-alt"></i> Eliminar Cuenta
					</button>
				</div>
			</form>
		</div>
	</div>
</x-layouts.app>

