<x-layouts.app :title="'Mis juegos - LevelUp Nexus'">
	<div class="flex items-center justify-between mb-8">
		<h1 class="text-4xl font-black bg-gradient-to-r from-pink-400 to-purple-500 bg-clip-text text-transparent">
			<i class="fas fa-trophy"></i> Mi Biblioteca
		</h1>
		<a href="{{ route('games.create') }}" class="px-5 py-3 rounded-lg bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 font-bold glow transition">
			<i class="fas fa-plus"></i> Añadir juego
		</a>
	</div>

	<!-- Filtros -->
	<div class="mb-6 p-4 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm">
		<form method="GET" action="{{ route('games.index') }}" class="flex flex-wrap gap-4 items-end">
			<div class="flex-1 min-w-[200px]">
				<label class="block text-sm font-semibold text-purple-200 mb-2">
					<i class="fas fa-sort"></i> Ordenar por
				</label>
				<select name="sort" class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-2 text-white focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50" onchange="this.form.submit()">
					<option value="favorites" {{ request('sort') == 'favorites' || !request('sort') ? 'selected' : '' }}>Favoritos primero</option>
					<option value="released_newest" {{ request('sort') == 'released_newest' ? 'selected' : '' }}>Fecha de salida - Más nuevos</option>
					<option value="released_oldest" {{ request('sort') == 'released_oldest' ? 'selected' : '' }}>Fecha de salida - Más antiguos</option>
					<option value="hours_desc" {{ request('sort') == 'hours_desc' ? 'selected' : '' }}>Horas jugadas - Mayor a menor</option>
				</select>
			</div>
			
			<div class="flex-1 min-w-[200px]">
				<label class="block text-sm font-semibold text-purple-200 mb-2">
					<i class="fas fa-filter"></i> Filtrar por género
				</label>
				<select name="genre" class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-2 text-white focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50" onchange="this.form.submit()">
					<option value="">Todos los géneros</option>
					@foreach($genres as $genre)
						<option value="{{ $genre }}" {{ request('genre') == $genre ? 'selected' : '' }}>{{ $genre }}</option>
					@endforeach
				</select>
			</div>
			
			@if(request('sort') || request('genre'))
				<div>
					<a href="{{ route('games.index') }}" class="px-4 py-2 rounded-lg bg-red-600/20 border border-red-500 hover:bg-red-600/40 transition text-red-300">
						<i class="fas fa-times"></i> Limpiar filtros
					</a>
				</div>
			@endif
		</form>
	</div>
	<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
		@foreach($games as $g)
			<div class="p-6 rounded-xl bg-slate-800/50 border border-purple-500/30 card-hover backdrop-blur-sm">
				@if($g->rawg_image)
					<div class="aspect-video bg-slate-900 rounded-lg overflow-hidden mb-4">
						<img src="{{ $g->rawg_image }}" alt="{{ $g->title }}" class="w-full h-full object-cover">
					</div>
				@endif
				<div class="flex items-start justify-between mb-3">
					<div class="flex-1">
						<div class="font-bold text-xl text-purple-200">{{ $g->title }}</div>
						@if($g->rawg_rating)
							<div class="text-sm text-purple-400 mt-1">
								<i class="fas fa-star text-yellow-500"></i> {{ number_format($g->rawg_rating, 1) }}
								@if($g->released_date)
									· <span>{{ $g->released_date->format('Y') }}</span>
								@endif
							</div>
						@endif
					</div>
					@if($g->is_favorite)
						<i class="fas fa-star text-yellow-400 text-lg"></i>
					@endif
				</div>
				<div class="text-purple-400 text-sm mb-3">
					<i class="fas fa-gamepad"></i> {{ ucfirst($g->platform) }} 
					@if($g->genre)
						· {{ $g->genre }}
					@endif
				</div>
				<div class="flex items-center gap-2 text-sm text-purple-300 mb-4">
					<i class="fas fa-clock"></i>
					<span class="font-semibold">{{ $g->hours_played }}</span> horas jugadas
				</div>
				<div class="flex gap-2">
					<a href="{{ route('games.edit',$g) }}" class="flex-1 text-center px-4 py-2 rounded-lg bg-purple-600/20 border border-purple-500 hover:bg-purple-600/40 transition">
						<i class="fas fa-edit"></i> Editar
					</a>
					<button type="button" onclick="openDeleteGameModal({{ $g->id }}, '{{ addslashes($g->title) }}')" class="px-4 py-2 rounded-lg bg-red-600/20 border border-red-500 hover:bg-red-600/40 transition">
						<i class="fas fa-trash"></i>
					</button>
				</div>
			</div>
		@endforeach
	</div>
	<div class="mt-6">{{ $games->links() }}</div>

	<!-- Modal de Confirmación de Eliminación -->
	<div id="deleteGameModal" class="hidden fixed inset-0 bg-black/90 backdrop-blur-sm flex items-center justify-center z-50 p-4">
		<div class="bg-slate-900 rounded-2xl w-full max-w-md border-2 border-red-500 glow">
			<div class="p-6 border-b border-red-500/30">
				<div class="flex items-center gap-3 mb-2">
					<div class="w-12 h-12 rounded-full bg-red-600/20 border border-red-500 flex items-center justify-center">
						<i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
					</div>
					<h3 class="text-2xl font-bold text-red-400">
						Eliminar Juego
					</h3>
				</div>
				<p class="text-red-300 text-sm">
					¿Estás seguro de que deseas eliminar <strong id="deleteGameName"></strong> de tu biblioteca?
				</p>
				<p class="text-red-400 text-xs mt-2">
					Esta acción no se puede deshacer.
				</p>
			</div>
			
			<div class="p-6">
				<div class="flex gap-3">
					<button type="button" onclick="closeDeleteGameModal()" class="flex-1 px-6 py-3 rounded-lg border border-purple-500 hover:bg-purple-500/20 transition font-semibold text-purple-300">
						<i class="fas fa-times"></i> Cancelar
					</button>
					<button type="button" onclick="confirmDeleteGame()" class="flex-1 px-6 py-3 rounded-lg bg-red-600 hover:bg-red-700 font-bold text-white transition">
						<i class="fas fa-trash-alt"></i> Eliminar
					</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		let gameToDelete = null;

		function openDeleteGameModal(gameId, gameTitle) {
			gameToDelete = gameId;
			document.getElementById('deleteGameName').textContent = gameTitle;
			document.getElementById('deleteGameModal').classList.remove('hidden');
		}

		function closeDeleteGameModal() {
			gameToDelete = null;
			document.getElementById('deleteGameModal').classList.add('hidden');
		}

		async function confirmDeleteGame() {
			if (!gameToDelete) return;

			const deleteBtn = document.querySelector('#deleteGameModal button[onclick="confirmDeleteGame()"]');
			const originalText = deleteBtn.innerHTML;
			deleteBtn.disabled = true;
			deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';

			try {
				const response = await fetch(`/games/${gameToDelete}`, {
					method: 'DELETE',
					headers: {
						'X-CSRF-TOKEN': '{{ csrf_token() }}',
						'Accept': 'application/json',
						'X-Requested-With': 'XMLHttpRequest'
					},
					credentials: 'same-origin'
				});

				if (response.ok) {
					closeDeleteGameModal();
					// Recargar la página para mostrar los cambios
					window.location.reload();
				} else {
					const data = await response.json();
					alert('Error: ' + (data.message || 'No se pudo eliminar el juego'));
					deleteBtn.disabled = false;
					deleteBtn.innerHTML = originalText;
				}
			} catch (error) {
				console.error('Error:', error);
				alert('Error al eliminar el juego');
				deleteBtn.disabled = false;
				deleteBtn.innerHTML = originalText;
			}
		}

		// Cerrar modal al presionar ESC
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape') {
				closeDeleteGameModal();
			}
		});
	</script>
</x-layouts.app>



