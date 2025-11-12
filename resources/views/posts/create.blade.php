<x-layouts.app :title="'Crear Publicación - LevelUp Nexus'">
	<div class="max-w-3xl mx-auto">
		<h1 class="text-4xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent mb-8">
			<i class="fas fa-plus-circle"></i> Crear Nueva Publicación
		</h1>

		<form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" class="p-8 rounded-2xl bg-slate-800/50 border border-purple-500 backdrop-blur-sm glow">
			@csrf
			
			<div class="mb-6">
				<label class="block text-sm mb-2 text-purple-300 font-semibold">
					<i class="fas fa-edit"></i> Contenido de la publicación
				</label>
				<textarea 
					name="content" 
					required 
					rows="8"
					class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 resize-none"
					placeholder="¿Qué quieres compartir con la comunidad? (máx. 5000 caracteres)"
				>{{ old('content') }}</textarea>
				@error('content')
					<p class="text-red-400 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
				@enderror
			</div>

				<div class="mb-6">
				<label class="block text-sm mb-2 text-purple-300 font-semibold">
					<i class="fas fa-gamepad"></i> Sobre qué juego trata (opcional)
				</label>
				
				<!-- Campos ocultos para almacenar datos del juego -->
				<input type="hidden" name="rawg_game_id" id="rawg_game_id">
				<input type="hidden" name="game_title" id="game_title">
				<input type="hidden" name="game_image" id="game_image">
				<input type="hidden" name="game_platform" id="game_platform">
				
				<div class="relative">
					<input 
						type="text" 
						id="gameSearch"
						autocomplete="off"
						placeholder="Buscar juego..."
						class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
					>
					
					<!-- Resultados de búsqueda -->
					<div id="gameResults" class="hidden absolute z-50 w-full mt-2 bg-slate-900 border border-purple-500/50 rounded-lg max-h-96 overflow-y-auto shadow-2xl"></div>
				</div>
				
				<!-- Preview del juego seleccionado -->
				<div id="gamePreview" class="hidden mt-3 p-3 rounded-lg bg-gradient-to-r from-purple-900/30 to-pink-900/30 border border-purple-500/30">
					<div class="flex items-center gap-3">
						<img id="previewImage" src="" alt="" class="w-16 h-16 rounded-lg object-cover">
						<div class="flex-1">
							<div class="font-bold text-purple-100" id="previewTitle"></div>
							<div class="text-sm text-purple-400" id="previewPlatform"></div>
						</div>
						<button type="button" onclick="clearGameSelection()" class="px-3 py-2 rounded-lg bg-red-600/30 hover:bg-red-600/50 border border-red-500/50 text-red-300 transition text-sm">
							<i class="fas fa-times"></i> Quitar
						</button>
					</div>
				</div>
				
				<p class="text-sm text-purple-400 mt-1">
					<i class="fas fa-info-circle"></i> Busca cualquier juego de la base de datos de RAWG
				</p>
				@error('rawg_game_id')
					<p class="text-red-400 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
				@enderror
			</div>

			<div class="mb-6">
				<label class="block text-sm mb-2 text-purple-300 font-semibold">
					<i class="fas fa-image"></i> Imagen (opcional)
				</label>
				<input 
					type="file" 
					name="image" 
					accept="image/*"
					class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700"
				>
				<p class="text-sm text-purple-400 mt-1">
					<i class="fas fa-info-circle"></i> Máximo 5MB (formatos: JPG, PNG, GIF, WebP)
				</p>
				@error('image')
					<p class="text-red-400 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
				@enderror
			</div>

			<div class="mb-6">
				<label class="block text-sm mb-2 text-purple-300 font-semibold">
					<i class="fas fa-eye"></i> Visibilidad
				</label>
				<select 
					name="visibility" 
					class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
				>
					<option value="public" {{ old('visibility', 'public') == 'public' ? 'selected' : '' }}>
						<i class="fas fa-globe"></i> Pública (todos pueden verla)
					</option>
					<option value="private" {{ old('visibility') == 'private' ? 'selected' : '' }}>
						<i class="fas fa-lock"></i> Privada (solo amigos)
					</option>
				</select>
			</div>

			<div class="flex gap-4">
				<button type="submit" class="flex-1 px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold text-white transition glow-sm">
					<i class="fas fa-paper-plane"></i> Publicar
				</button>
				<a href="{{ route('posts.index') }}" class="px-6 py-3 rounded-lg border border-purple-500 hover:bg-purple-500/20 transition font-semibold text-purple-300">
					<i class="fas fa-times"></i> Cancelar
				</a>
			</div>
		</form>
	</div>

	<script>
		let searchTimeout;
		const gameSearch = document.getElementById('gameSearch');
		const gameResults = document.getElementById('gameResults');

		// Búsqueda con debouncing
		gameSearch.addEventListener('input', function(e) {
			const query = e.target.value.trim();
			
			clearTimeout(searchTimeout);
			
			if(query.length < 2) {
				gameResults.classList.add('hidden');
				return;
			}

			searchTimeout = setTimeout(async () => {
				try {
					// Usar la ruta de Laravel que actúa como proxy
					const res = await fetch(`/rawg/search?q=${encodeURIComponent(query)}`);
					
					if (!res.ok) {
						console.error('Error HTTP:', res.status, res.statusText);
						gameResults.innerHTML = '<div class="p-3 text-red-400 text-center">Error al buscar juegos. Intenta de nuevo.</div>';
						gameResults.classList.remove('hidden');
						return;
					}
					
					const data = await res.json();
					
					if(data.games && data.games.length > 0) {
						gameResults.innerHTML = '';
						data.games.forEach(game => {
							const platforms = game.platforms?.join(', ') || 'N/A';
							const div = document.createElement('div');
							div.className = 'p-3 hover:bg-purple-500/20 cursor-pointer border-b border-purple-500/30 last:border-0 transition';
							div.onclick = () => selectGame(game);
							div.innerHTML = `
								<div class="flex items-center gap-3">
									<img src="${game.image || '/placeholder.png'}" alt="${game.name}" class="w-12 h-12 rounded object-cover">
									<div class="flex-1">
										<div class="font-semibold text-purple-200">${game.name}</div>
										<div class="text-xs text-purple-400">${platforms}</div>
									</div>
								</div>
							`;
							gameResults.appendChild(div);
						});
						gameResults.classList.remove('hidden');
					} else {
						gameResults.innerHTML = '<div class="p-3 text-purple-400 text-center">No se encontraron juegos</div>';
						gameResults.classList.remove('hidden');
					}
				} catch (error) {
					console.error('Error completo:', error);
					gameResults.innerHTML = '<div class="p-3 text-red-400 text-center">Error al buscar</div>';
					gameResults.classList.remove('hidden');
				}
			}, 300);
		});

		// Seleccionar juego
		function selectGame(game) {
			const platforms = game.platforms?.join(', ') || 'N/A';
			
			// Guardar datos en campos ocultos
			document.getElementById('rawg_game_id').value = game.id;
			document.getElementById('game_title').value = game.name;
			document.getElementById('game_image').value = game.image || '';
			document.getElementById('game_platform').value = platforms;
			
			// Mostrar preview
			document.getElementById('previewImage').src = game.image || '/placeholder.png';
			document.getElementById('previewTitle').textContent = game.name;
			document.getElementById('previewPlatform').textContent = platforms;
			document.getElementById('gamePreview').classList.remove('hidden');
			
			// Limpiar búsqueda
			gameSearch.value = '';
			gameResults.classList.add('hidden');
		}

		// Limpiar selección
		function clearGameSelection() {
			document.getElementById('rawg_game_id').value = '';
			document.getElementById('game_title').value = '';
			document.getElementById('game_image').value = '';
			document.getElementById('game_platform').value = '';
			document.getElementById('gamePreview').classList.add('hidden');
			gameSearch.value = '';
		}

		// Ocultar resultados al hacer clic fuera
		document.addEventListener('click', function(e) {
			if(!gameSearch.contains(e.target) && !gameResults.contains(e.target)) {
				gameResults.classList.add('hidden');
			}
		});
	</script>
</x-layouts.app>

