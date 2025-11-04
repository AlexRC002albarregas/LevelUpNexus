<x-layouts.app :title="$user->name . ' - LevelUp Nexus'">
	@php
		$canView = auth()->user()->canViewProfile($user);
		$isOwnProfile = auth()->id() === $user->id;
		$isFriend = !$isOwnProfile && auth()->user()->isFriendWith($user);
	@endphp

	<div class="max-w-4xl mx-auto">
		<!-- Header del perfil -->
		<div class="mb-6 p-8 rounded-2xl bg-gradient-to-r from-purple-900/50 to-pink-900/50 border border-purple-500 backdrop-blur-sm glow">
			<div class="flex items-start gap-6">
				<!-- Avatar -->
				<div class="w-32 h-32 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-black text-5xl border-4 border-purple-400 glow overflow-hidden flex-shrink-0">
					@if($user->avatar)
						<img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover" alt="{{ $user->name }}">
					@else
						{{ strtoupper(substr($user->name, 0, 1)) }}
					@endif
				</div>

				<!-- Información -->
				<div class="flex-1">
					<div class="flex items-start justify-between">
						<div>
							<h1 class="text-4xl font-black text-white mb-2">{{ $user->name }}</h1>
							@if($canView || $isOwnProfile)
								<div class="text-purple-300 mb-3">
									<i class="fas fa-envelope"></i> {{ $user->email }}
								</div>
							@endif
							<div class="flex gap-2">
								<span class="px-3 py-1 rounded-full bg-purple-600/30 border border-purple-500 text-sm font-semibold">
									<i class="fas fa-crown"></i> {{ ucfirst($user->role) }}
								</span>
								@if($user->is_private)
									<span class="px-3 py-1 rounded-full bg-pink-600/30 border border-pink-500 text-sm font-semibold">
										<i class="fas fa-lock"></i> Privado
									</span>
								@endif
							</div>
						</div>

						@if($isOwnProfile)
							<a href="{{ route('account.edit') }}" class="px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold transition">
								<i class="fas fa-edit"></i> Editar perfil
							</a>
						@endif
					</div>

					@if($canView && $user->bio)
						<div class="mt-4 p-4 rounded-lg bg-slate-800/50 border border-purple-500/30">
							<p class="text-purple-200 text-sm leading-relaxed">{{ $user->bio }}</p>
						</div>
					@endif
				</div>
			</div>

			<!-- Juegos favoritos -->
			@if($canView && $user->favorite_games && count($user->favorite_games) > 0)
				<div class="mt-6 pt-6 border-t border-purple-500/30">
					<h3 class="font-bold text-lg text-purple-200 mb-4">
						<i class="fas fa-gamepad"></i> Juegos Favoritos
					</h3>
					<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3" id="userFavoriteGames">
						<p class="text-purple-400 text-sm col-span-full">Cargando juegos...</p>
					</div>
				</div>
			@endif
		</div>

		@if(!$canView)
			<!-- Mensaje de perfil privado -->
			<div class="text-center py-16 p-8 rounded-2xl bg-slate-800/50 border border-pink-500 backdrop-blur-sm">
				<i class="fas fa-lock text-7xl text-pink-500 mb-6"></i>
				<h2 class="text-3xl font-black text-white mb-3">Perfil Privado</h2>
				<p class="text-purple-300 mb-6">Este usuario ha configurado su perfil como privado. Solo sus amigos pueden ver su información y publicaciones.</p>
				@if(!$isFriend)
					<p class="text-purple-400 text-sm">Añade a {{ $user->name }} como amigo para ver su contenido.</p>
				@endif
			</div>
		@else
			<!-- Estadísticas (solo para perfiles visibles) -->
			@php
				$visiblePostsCount = $isOwnProfile 
					? $user->posts()->count() 
					: ($user->is_private && !$isFriend 
						? $user->posts()->where('visibility', 'public')->count() 
						: $user->posts()->count());
				$friendsCount = ($isOwnProfile || $isFriend) ? $user->friends()->count() : null;
			@endphp
			<div class="grid grid-cols-3 gap-4 mb-6">
				<div class="p-4 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm text-center">
					<div class="text-3xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">
						{{ $visiblePostsCount }}
					</div>
					<div class="text-sm text-purple-400 mt-1">Publicaciones</div>
				</div>
				@if($friendsCount !== null)
					<div class="p-4 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm text-center">
						<div class="text-3xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">
							{{ $friendsCount }}
						</div>
						<div class="text-sm text-purple-400 mt-1">Amigos</div>
					</div>
				@else
					<div class="p-4 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm text-center">
						<div class="text-3xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">
							<i class="fas fa-lock"></i>
						</div>
						<div class="text-sm text-purple-400 mt-1">Amigos</div>
					</div>
				@endif
				<div class="p-4 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm text-center">
					<div class="text-3xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">
						{{ $user->comments()->count() }}
					</div>
					<div class="text-sm text-purple-400 mt-1">Comentarios</div>
				</div>
			</div>

			<!-- Publicaciones del usuario -->
			<div>
				<h2 class="text-2xl font-black text-white mb-4">
					<i class="fas fa-stream"></i> Publicaciones de {{ $isOwnProfile ? 'tu perfil' : $user->name }}
				</h2>

				@if($posts->count() > 0)
					<div class="space-y-4">
						@foreach($posts as $post)
							<div class="p-6 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm card-hover">
								<div class="flex items-start gap-3 mb-3">
									<div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold overflow-hidden flex-shrink-0">
										@if($user->avatar)
											<img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover" alt="{{ $user->name }}">
										@else
											{{ strtoupper(substr($user->name, 0, 1)) }}
										@endif
									</div>
									<div class="flex-1">
										<div class="font-bold text-purple-200">{{ $user->name }}</div>
										<div class="text-xs text-purple-400">{{ $post->created_at->diffForHumans() }}</div>
									</div>
								</div>
								
								<p class="text-purple-200 mb-4">{{ Str::limit($post->content, 200) }}</p>

								<div class="flex items-center gap-4 text-sm text-purple-400">
									<span><i class="fas fa-comments"></i> {{ $post->comments()->count() }} comentarios</span>
									<span><i class="fas fa-heart"></i> {{ $post->reactions()->count() }} reacciones</span>
								</div>

								<a href="{{ route('posts.show', $post) }}" class="mt-4 inline-block px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold transition text-sm">
									<i class="fas fa-eye"></i> Ver publicación
								</a>
							</div>
						@endforeach
					</div>

					<!-- Paginación -->
					<div class="mt-6">
						{{ $posts->links() }}
					</div>
				@else
					<div class="text-center py-12 p-8 rounded-xl bg-slate-800/30 border border-purple-500/20">
						<i class="fas fa-pen-alt text-6xl text-purple-500/50 mb-4"></i>
						<p class="text-purple-400">{{ $isOwnProfile ? 'Aún no has creado' : $user->name . ' aún no ha creado' }} ninguna publicación.</p>
					</div>
				@endif
			</div>
		@endif
	</div>

	@if($canView && $user->favorite_games && count($user->favorite_games) > 0)
	<script>
		// Cargar juegos favoritos del usuario
		document.addEventListener('DOMContentLoaded', function() {
			const gameIds = @json($user->favorite_games);
			const container = document.getElementById('userFavoriteGames');
			
			if(!gameIds || gameIds.length === 0) {
				container.innerHTML = '<p class="text-purple-400 text-sm">No hay juegos favoritos.</p>';
				return;
			}

			fetch('/rawg/favorites?ids=' + gameIds.join(','))
				.then(res => res.json())
				.then(data => {
					if(data.games && data.games.length > 0) {
						container.innerHTML = data.games.map(game => `
							<div class="group rounded-xl bg-slate-800/50 border border-purple-500/30 hover:border-purple-500 transition overflow-hidden card-hover">
								<div class="aspect-video bg-slate-900 overflow-hidden">
									<img src="${game.background_image || 'https://via.placeholder.com/300x200'}" 
										class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" 
										alt="${game.name}">
								</div>
								<div class="p-3">
									<h6 class="font-bold text-sm text-purple-200 mb-1 truncate" title="${game.name}">${game.name}</h6>
									<div class="flex items-center justify-between text-xs text-purple-400">
										<span><i class="fas fa-star text-yellow-500"></i> ${game.rating || 'N/A'}</span>
										${game.released ? `<span class="text-purple-500">${game.released.split('-')[0]}</span>` : ''}
									</div>
								</div>
							</div>
						`).join('');
					} else {
						container.innerHTML = '<p class="text-purple-400 text-sm col-span-full">No se pudieron cargar los juegos.</p>';
					}
				})
				.catch(error => {
					console.error('Error cargando juegos:', error);
					container.innerHTML = '<p class="text-red-400 text-sm col-span-full">Error al cargar juegos favoritos</p>';
				});
		});
	</script>
	@endif
</x-layouts.app>

