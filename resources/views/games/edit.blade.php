<x-layouts.app :title="'Editar juego - LevelUp Nexus'">
	<div class="max-w-4xl mx-auto">
		<div class="flex items-center justify-between mb-8">
			<h1 class="text-4xl font-black bg-gradient-to-r from-pink-400 to-purple-500 bg-clip-text text-transparent">
				<i class="fas fa-edit"></i> Editar Juego
			</h1>
			<a href="{{ route('games.index') }}" class="px-5 py-3 rounded-lg bg-slate-800 border border-purple-500/50 hover:bg-purple-500/20 text-purple-300 transition">
				<i class="fas fa-arrow-left"></i> Volver
			</a>
		</div>

		<!-- Preview del juego actual -->
		@if($game->rawg_image)
			<div class="mb-6 p-4 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm">
				<div class="flex items-center gap-4">
					<img src="{{ $game->rawg_image }}" alt="{{ $game->title }}" class="w-20 h-20 rounded-lg object-cover">
					<div class="flex-1">
						<h3 class="font-bold text-xl text-purple-200">{{ $game->title }}</h3>
						@if($game->rawg_rating)
							<div class="text-sm text-purple-400 mt-1">
								<i class="fas fa-star text-yellow-500"></i> {{ number_format($game->rawg_rating, 1) }}
								@if($game->released_date)
									· <span>{{ $game->released_date->format('Y') }}</span>
								@endif
							</div>
						@endif
					</div>
				</div>
			</div>
		@endif

		<form id="editGameForm" method="POST" action="{{ route('games.update', $game) }}" class="space-y-4 p-6 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm">
			@csrf
			@method('PUT')
			
			<input type="hidden" name="rawg_id" value="{{ $game->rawg_id }}">
			<input type="hidden" name="rawg_image" value="{{ $game->rawg_image }}">
			<input type="hidden" name="rawg_rating" value="{{ $game->rawg_rating }}">
			<input type="hidden" name="released_date" value="{{ $game->released_date ? $game->released_date->format('Y-m-d') : '' }}">
			<input type="hidden" name="rawg_slug" value="{{ $game->rawg_slug }}">
			
			<div>
				<label class="block text-sm font-semibold text-purple-200 mb-2">
					<i class="fas fa-heading"></i> Título
				</label>
				<input name="title" value="{{ $game->title }}" required class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50">
			</div>
			
			<div class="grid md:grid-cols-2 gap-4">
				<div>
					<label class="block text-sm font-semibold text-purple-200 mb-2">
						<i class="fas fa-gamepad"></i> Plataforma
					</label>
					<select name="platform" class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50">
						@foreach(['pc','xbox','playstation','switch','mobile','other'] as $opt)
							<option value="{{ $opt }}" @selected($game->platform===$opt)>{{ ucfirst($opt) }}</option>
						@endforeach
					</select>
				</div>
				
				<div>
					<label class="block text-sm font-semibold text-purple-200 mb-2">
						<i class="fas fa-clock"></i> Horas jugadas
					</label>
					<input name="hours_played" type="number" min="0" value="{{ $game->hours_played }}" class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50">
				</div>
			</div>
			
			<div>
				<label class="block text-sm font-semibold text-purple-200 mb-2">
					<i class="fas fa-tags"></i> Género (opcional)
				</label>
				<input name="genre" value="{{ $game->genre }}" class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50" placeholder="Ej: Acción, RPG, Aventura...">
			</div>
			
			<div class="flex items-center gap-2">
				<input id="fav" name="is_favorite" type="checkbox" value="1" @checked($game->is_favorite) class="rounded w-5 h-5 text-purple-600">
				<label for="fav" class="text-purple-200 font-semibold">
					<i class="fas fa-star text-yellow-500"></i> Marcar como favorito
				</label>
			</div>
			
			<div class="flex gap-3 pt-4">
				<a href="{{ route('games.index') }}" class="px-6 py-3 rounded-lg bg-slate-700 hover:bg-slate-600 text-white font-bold transition">
					<i class="fas fa-times"></i> Cancelar
				</a>
				<button type="submit" class="px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold glow transition">
					<i class="fas fa-save"></i> Guardar Cambios
				</button>
			</div>
		</form>
	</div>

	<script>
		document.getElementById('editGameForm').addEventListener('submit', async function(e) {
			e.preventDefault();
			
			const formData = new FormData(this);
			const submitBtn = this.querySelector('button[type="submit"]');
			const originalText = submitBtn.innerHTML;
			
			submitBtn.disabled = true;
			submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
			
			try {
				// Para PUT necesitamos usar _method y método POST
				formData.append('_method', 'PUT');
				
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
					window.location.href = '{{ route("games.index") }}';
				} else {
					alert('Error: ' + (data.message || 'Error al actualizar el juego'));
					submitBtn.disabled = false;
					submitBtn.innerHTML = originalText;
				}
			} catch(error) {
				console.error('Error:', error);
				alert('Error al actualizar el juego');
				submitBtn.disabled = false;
				submitBtn.innerHTML = originalText;
			}
		});
	</script>
</x-layouts.app>



