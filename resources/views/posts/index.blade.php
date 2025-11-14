<x-layouts.app :title="'Publicaciones - LevelUp Nexus'">
	<div class="max-w-7xl mx-auto">
		<div class="flex items-center justify-between mb-8 w-full gap-4">
			<h1 class="text-4xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">
				<i class="fas fa-newspaper"></i> Feed de Publicaciones
			</h1>
			<a href="{{ route('posts.create') }}" class="ml-auto px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold text-white transition glow-sm">
				<i class="fas fa-plus"></i> Nueva Publicación
			</a>
		</div>

		<div class="mb-8">
			<form method="GET" action="{{ route('posts.index') }}" class="p-3 bg-slate-900/60 border border-purple-500/30 rounded-xl backdrop-blur-sm grid gap-3 md:grid-cols-[repeat(auto-fit,minmax(180px,1fr))] items-end text-xs">
				<div>
					<label for="sort" class="font-semibold text-purple-200 block mb-1 uppercase tracking-wide">
						<i class="fas fa-history text-[10px]"></i> Ordenar
					</label>
					<select 
						id="sort" 
						name="sort" 
						class="w-full bg-slate-950/60 border border-purple-500/40 rounded-lg px-2.5 py-1.5 text-xs text-purple-100 focus:border-purple-400 focus:ring-1 focus:ring-purple-500/40 transition"
					>
						<option value="recent" {{ ($currentSort ?? 'recent') === 'recent' ? 'selected' : '' }}>Más recientes</option>
						<option value="oldest" {{ ($currentSort ?? 'recent') === 'oldest' ? 'selected' : '' }}>Más antiguas</option>
					</select>
				</div>
				<div>
					<label for="game" class="font-semibold text-purple-200 block mb-1 uppercase tracking-wide">
						<i class="fas fa-layer-group text-[10px]"></i> Categoría
					</label>
					<select 
						id="game" 
						name="game" 
						class="w-full bg-slate-950/60 border border-purple-500/40 rounded-lg px-2.5 py-1.5 text-xs text-purple-100 focus:border-purple-400 focus:ring-1 focus:ring-purple-500/40 transition"
					>
						<option value="">Todas</option>
						@foreach($availableGames ?? [] as $gameName)
							<option value="{{ $gameName }}" {{ ($currentGame ?? '') === $gameName ? 'selected' : '' }}>
								{{ $gameName }}
							</option>
						@endforeach
					</select>
				</div>
				<div class="flex gap-2">
					<button type="submit" class="flex-1 px-3 py-1.5 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white text-xs font-semibold transition">
						<i class="fas fa-filter"></i> Filtrar
					</button>
					@if(request()->has('sort') || request()->has('game'))
						<a href="{{ route('posts.index') }}" class="px-3 py-1.5 rounded-lg border border-purple-500/40 text-purple-200 text-xs font-semibold hover:bg-purple-500/10 transition text-center">
							Reiniciar
						</a>
					@endif
				</div>
			</form>
		</div>

		<div class="space-y-12">
			@forelse($posts as $post)
				<div class="max-w-3xl mx-auto w-full">
					<div class="p-1 sm:p-2">
						<div class="p-6 rounded-2xl bg-slate-800/60 border border-purple-500/40 backdrop-blur-sm card-hover shadow-xl shadow-purple-900/20">
					<div class="flex items-start justify-between mb-4">
						<div class="flex items-center gap-3">
							<a href="{{ route('users.show', $post->user) }}" class="flex items-center gap-3 hover:opacity-80 transition">
								<div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold border-2 border-purple-400 overflow-hidden">
									@if($post->user->avatar)
										<img src="{{ asset('storage/' . $post->user->avatar) }}" class="w-full h-full object-cover" alt="{{ $post->user->name }}">
									@else
										{{ strtoupper(substr($post->user->name, 0, 1)) }}
									@endif
								</div>
								<div>
									<div class="font-bold text-purple-200">{{ $post->user->name }}</div>
									<div class="text-sm text-purple-400">
										<i class="fas fa-clock"></i> {{ $post->created_at->diffForHumans() }}
									</div>
								</div>
							</a>
						</div>
						@if(auth()->id() === $post->user_id || auth()->user()->isAdmin())
							<div class="flex gap-2">
								<a href="{{ route('posts.edit', $post) }}" class="px-3 py-2 rounded-lg bg-blue-600/30 hover:bg-blue-600/50 border border-blue-500/50 text-blue-300 text-sm transition" title="Editar">
									<i class="fas fa-edit"></i>
								</a>
								<form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('¿Estás seguro de eliminar esta publicación?');" class="inline">
									@csrf
									@method('DELETE')
									<button type="submit" class="px-3 py-2 rounded-lg bg-red-600/30 hover:bg-red-600/50 border border-red-500/50 text-red-300 text-sm transition" title="Eliminar">
										<i class="fas fa-trash-alt"></i>
									</button>
								</form>
							</div>
						@endif
					</div>

					@if($post->rawg_game_id || $post->game)
						@php
							// Priorizar datos de RAWG si existen
							$gameTitle = $post->game_title ?? $post->game->title ?? 'Juego';
							$gameImage = $post->game_image ?? $post->game->rawg_image ?? null;
							$gamePlatform = $post->game_platform ?? $post->game->platform ?? null;
						@endphp
						<div class="mb-4 p-3 rounded-lg bg-gradient-to-r from-purple-900/30 to-pink-900/30 border border-purple-500/30 flex items-center gap-3">
							@if($gameImage)
								<img src="{{ $gameImage }}" alt="{{ $gameTitle }}" class="w-16 h-16 rounded-lg object-cover">
							@endif
							<div class="flex-1">
								<div class="text-sm text-purple-300 mb-1">
									<i class="fas fa-gamepad"></i> Hablando sobre:
								</div>
								<div class="font-bold text-purple-100">{{ $gameTitle }}</div>
								@if($gamePlatform)
									<div class="text-xs text-purple-400">
										<i class="fas fa-desktop"></i> {{ $gamePlatform }}
									</div>
								@endif
							</div>
						</div>
					@endif

					<div class="text-purple-100 mb-4 whitespace-pre-wrap">{{ $post->content }}</div>

					@if($post->image)
						<div class="mb-4">
							<img 
								src="{{ asset('storage/' . $post->image) }}" 
								alt="Imagen de la publicación" 
								class="max-w-md rounded-lg border border-purple-500/30 cursor-pointer hover:opacity-80 transition"
								onclick="openImageModal('{{ asset('storage/' . $post->image) }}')"
							>
						</div>
					@endif

					<!-- Reacciones -->
					<div class="mb-4 pt-4 border-t border-purple-500/30">
						@php
							$userReaction = $post->reactions->where('user_id', auth()->id())->first();
						@endphp
						<div class="flex items-center gap-2 mb-3">
							<form method="POST" action="{{ route('reactions.toggle', $post) }}" class="inline reaction-form" data-post-id="{{ $post->id }}">
								@csrf
								<input type="hidden" name="type" value="like">
								<button type="submit" class="{{ $userReaction && $userReaction->type === 'like' ? 'reaction-btn px-3 py-1.5 rounded-lg bg-blue-600/50 border border-purple-500/50 text-purple-100 text-xs font-semibold transition' : 'reaction-btn px-3 py-1.5 rounded-lg bg-purple-600/30 hover:bg-purple-600/50 border border-purple-500/50 text-purple-200 text-xs font-semibold transition' }}" data-post-id="{{ $post->id }}" data-reaction-type="like" data-default-class="reaction-btn px-3 py-1.5 rounded-lg bg-purple-600/30 hover:bg-purple-600/50 border border-purple-500/50 text-purple-200 text-xs font-semibold transition" data-active-class="reaction-btn px-3 py-1.5 rounded-lg bg-blue-600/50 border border-purple-500/50 text-purple-100 text-xs font-semibold transition">
									<i class="fas fa-thumbs-up"></i> Like
								</button>
							</form>
							<form method="POST" action="{{ route('reactions.toggle', $post) }}" class="inline reaction-form" data-post-id="{{ $post->id }}">
								@csrf
								<input type="hidden" name="type" value="love">
								<button type="submit" class="{{ $userReaction && $userReaction->type === 'love' ? 'reaction-btn px-3 py-1.5 rounded-lg bg-pink-600/50 border border-purple-500/50 text-purple-100 text-xs font-semibold transition' : 'reaction-btn px-3 py-1.5 rounded-lg bg-purple-600/30 hover:bg-purple-600/50 border border-purple-500/50 text-purple-200 text-xs font-semibold transition' }}" data-post-id="{{ $post->id }}" data-reaction-type="love" data-default-class="reaction-btn px-3 py-1.5 rounded-lg bg-purple-600/30 hover:bg-purple-600/50 border border-purple-500/50 text-purple-200 text-xs font-semibold transition" data-active-class="reaction-btn px-3 py-1.5 rounded-lg bg-pink-600/50 border border-purple-500/50 text-purple-100 text-xs font-semibold transition">
								<i class="fas fa-heart"></i> Love
							</button>
						</form>
						<form method="POST" action="{{ route('reactions.toggle', $post) }}" class="inline reaction-form" data-post-id="{{ $post->id }}">
							@csrf
							<input type="hidden" name="type" value="haha">
							<button type="submit" class="{{ $userReaction && $userReaction->type === 'haha' ? 'reaction-btn px-3 py-1.5 rounded-lg bg-yellow-600/50 border border-purple-500/50 text-purple-900 text-xs font-semibold transition' : 'reaction-btn px-3 py-1.5 rounded-lg bg-purple-600/30 hover:bg-purple-600/50 border border-purple-500/50 text-purple-200 text-xs font-semibold transition' }}" data-post-id="{{ $post->id }}" data-reaction-type="haha" data-default-class="reaction-btn px-3 py-1.5 rounded-lg bg-purple-600/30 hover:bg-purple-600/50 border border-purple-500/50 text-purple-200 text-xs font-semibold transition" data-active-class="reaction-btn px-3 py-1.5 rounded-lg bg-yellow-600/50 border border-purple-500/50 text-purple-900 text-xs font-semibold transition">
								<i class="fas fa-laugh"></i> Haha
							</button>
						</form>
						<div class="ml-2 text-xs text-purple-400" data-reaction-summary="{{ $post->id }}">
							@foreach($post->reactions->groupBy('type') as $type => $reactions)
								<span class="mr-2" data-reaction-summary-item="{{ $type }}">
									{{ $reactions->count() }}
									<i class="fas fa-{{ $type === 'like' ? 'thumbs-up' : ($type === 'love' ? 'heart' : ($type === 'haha' ? 'laugh' : 'thumbs-down')) }}"></i>
								</span>
							@endforeach
						</div>
					</div>

					<!-- Formulario de comentario -->
					<div class="mb-4 pb-4 border-b border-purple-500/30">
						<form method="POST" action="{{ route('comments.store') }}" class="flex gap-2 comment-form" data-post-id="{{ $post->id }}" data-comments-limit="3">
							@csrf
							<input type="hidden" name="post_id" value="{{ $post->id }}">
							<input type="hidden" name="from_index" value="1">
							<input 
								type="text" 
								name="content" 
								required 
								placeholder="Escribe un comentario..."
								data-comment-input="{{ $post->id }}"
								class="flex-1 bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-2 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 text-sm"
							>
							<button type="submit" class="px-4 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold text-white transition text-sm">
								<i class="fas fa-paper-plane"></i>
							</button>
						</form>
						<p class="text-red-400 text-xs mt-1 hidden" data-comment-error="{{ $post->id }}"></p>
						@error('content')
							@if(request()->has('post_id') && request()->post_id == $post->id)
								<p class="text-red-400 text-xs mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
							@endif
						@enderror
					</div>

					<!-- Comentarios recientes -->
					@if($post->comments->count() > 0)
						<div class="mb-4" data-comments-wrapper="{{ $post->id }}">
							<div class="text-xs text-purple-400 mb-2" data-comment-summary="{{ $post->id }}">
								<i class="fas fa-comments"></i> 
								<span data-comment-count="{{ $post->id }}">{{ $post->comments->count() }}</span> 
								<span data-comment-label="{{ $post->id }}" data-singular="comentario" data-plural="comentarios">{{ $post->comments->count() === 1 ? 'comentario' : 'comentarios' }}</span>
								<span class="{{ $post->comments->count() > 3 ? '' : 'hidden' }}" data-view-all="{{ $post->id }}"> · <a href="{{ route('posts.show', $post) }}" class="hover:text-purple-300">Ver todos</a></span>
							</div>
							<div class="space-y-2" data-comments-list="{{ $post->id }}" data-comment-layout="compact" data-comments-limit="3">
								@foreach($post->comments->take(3) as $comment)
									<div class="flex items-start gap-2 text-sm" data-comment-item>
										<a href="{{ route('users.show', $comment->user) }}" class="flex-shrink-0">
											<div class="w-6 h-6 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold text-xs border border-purple-400 overflow-hidden">
												@if($comment->user->avatar)
													<img src="{{ asset('storage/' . $comment->user->avatar) }}" class="w-full h-full object-cover" alt="{{ $comment->user->name }}">
												@else
													{{ strtoupper(substr($comment->user->name, 0, 1)) }}
												@endif
											</div>
										</a>
										<div class="flex-1 min-w-0">
											<div>
												<a href="{{ route('users.show', $comment->user) }}" class="font-semibold text-purple-200 hover:text-purple-100 text-xs">
													{{ $comment->user->name }}
												</a>
												<span class="text-purple-300 ml-2" data-comment-content>{{ $comment->content }}</span>
											</div>
											<div class="text-xs text-purple-400 mt-0.5" data-comment-date>{{ $comment->created_at->diffForHumans() }}</div>
										</div>
										@if(auth()->id() === $comment->user_id || auth()->id() === $post->user_id)
											<button 
												type="button" 
												onclick="openDeleteCommentModal({{ $comment->id }}, {{ $post->id }}, this)" 
												data-comment-preview="{{ htmlspecialchars(substr($comment->content, 0, 50) . '...', ENT_QUOTES, 'UTF-8') }}"
												class="text-red-400 hover:text-red-300 text-xs delete-comment-btn" 
												title="Eliminar">
												<i class="fas fa-trash-alt"></i>
											</button>
										@endif
									</div>
								@endforeach
							</div>
						</div>
					@else
						<div class="mb-4" data-comments-wrapper="{{ $post->id }}">
							<div class="text-xs text-purple-400 mb-2" data-comment-summary="{{ $post->id }}">
								<i class="fas fa-comments"></i> 
								<span data-comment-count="{{ $post->id }}">0</span> 
								<span data-comment-label="{{ $post->id }}" data-singular="comentario" data-plural="comentarios">comentarios</span>
								<span class="hidden" data-view-all="{{ $post->id }}"> · <a href="{{ route('posts.show', $post) }}" class="hover:text-purple-300">Ver todos</a></span>
							</div>
							<div class="space-y-2" data-comments-list="{{ $post->id }}" data-comment-layout="compact" data-comments-limit="3"></div>
							<p class="text-purple-400 text-sm italic" data-no-comments="{{ $post->id }}">
								<i class="fas fa-comment-slash"></i> Sé el primero en comentar.
							</p>
						</div>
					@endif

							<div class="flex items-center justify-between pt-4 border-t border-purple-500/30">
								<div class="flex items-center gap-4 text-sm text-purple-400">
									<span><i class="fas fa-heart"></i> <span data-reaction-total="{{ $post->id }}">{{ $post->reactions->count() }}</span> reacciones</span>
									<span><i class="fas fa-comment"></i> <span data-comment-count="{{ $post->id }}">{{ $post->comments->count() }}</span> <span data-comment-label="{{ $post->id }}" data-singular="comentario" data-plural="comentarios">{{ $post->comments->count() === 1 ? 'comentario' : 'comentarios' }}</span></span>
								</div>
								<a href="{{ route('posts.show', $post) }}" class="px-4 py-2 rounded-lg bg-purple-600/30 hover:bg-purple-600/50 border border-purple-500/50 text-purple-300 text-sm font-semibold transition">
									<i class="fas fa-eye"></i> Ver completo
								</a>
							</div>
						</div>
					</div>
				</div>
			@empty
				<div class="p-12 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm text-center">
					<i class="fas fa-inbox text-6xl text-purple-400 mb-4"></i>
					<p class="text-xl text-purple-300 mb-2">No hay publicaciones aún</p>
					<p class="text-purple-400 mb-6">¡Sé el primero en compartir algo con la comunidad!</p>
					<a href="{{ route('posts.create') }}" class="inline-block px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold text-white transition glow-sm">
						<i class="fas fa-plus"></i> Crear primera publicación
					</a>
				</div>
			@endforelse
		</div>

		<div class="mt-8">
			{{ $posts->appends(request()->query())->links() }}
		</div>
	</div>

	<!-- Modal de Imagen -->
	<div id="imageModal" class="hidden fixed inset-0 bg-black/90 backdrop-blur-sm flex items-center justify-center z-[200] p-4" onclick="closeImageModal()">
		<div class="relative max-w-7xl max-h-[90vh] w-full h-full flex items-center justify-center">
			<button 
				type="button" 
				onclick="closeImageModal()" 
				class="absolute top-4 right-4 w-12 h-12 rounded-full bg-red-600 hover:bg-red-700 text-white transition flex items-center justify-center z-10 glow"
			>
				<i class="fas fa-times text-xl"></i>
			</button>
			<img 
				id="modalImage" 
				src="" 
				alt="Imagen ampliada" 
				class="max-w-full max-h-full object-contain rounded-lg"
				onclick="event.stopPropagation()"
			>
		</div>
	</div>

	<!-- Modal de Confirmación de Eliminación de Comentario -->
	<div id="deleteCommentModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
		<div class="bg-slate-900 rounded-2xl w-full max-w-md border-2 border-red-500 glow">
			<div class="p-6 border-b border-red-500/30">
				<div class="flex items-center gap-3">
					<div class="w-12 h-12 rounded-full bg-red-600/20 border border-red-500 flex items-center justify-center">
						<i class="fas fa-comment-slash text-red-500 text-2xl"></i>
					</div>
					<h3 class="text-xl font-bold text-red-400">
						Eliminar Comentario
					</h3>
				</div>
			</div>
			
			<div class="p-6">
				<p class="text-purple-200 mb-4">
					¿Estás seguro de que quieres eliminar este comentario?
				</p>
				<div class="p-3 mb-4 rounded-lg bg-slate-800/50 border border-purple-500/30">
					<p class="text-sm text-purple-300 italic" id="deleteCommentPreview"></p>
				</div>
				<p class="text-sm text-purple-400 mb-6">
					Esta acción no se puede deshacer.
				</p>
				
				<form id="deleteCommentForm" method="POST" action="">
					@csrf
					@method('DELETE')
					<input type="hidden" name="from_index" value="1" id="deleteCommentFromIndex">
				</form>
				
				<div class="flex gap-3">
					<button type="button" onclick="closeDeleteCommentModal()" class="flex-1 px-6 py-3 rounded-lg border border-purple-500 hover:bg-purple-500/20 transition font-semibold text-purple-300">
						<i class="fas fa-times"></i> Cancelar
					</button>
					<button type="button" onclick="confirmDeleteComment()" class="flex-1 px-6 py-3 rounded-lg bg-red-600 hover:bg-red-700 font-bold text-white transition">
						<i class="fas fa-trash-alt"></i> Eliminar
					</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		// Modal de imagen
		function openImageModal(imageSrc) {
			document.getElementById('modalImage').src = imageSrc;
			document.getElementById('imageModal').classList.remove('hidden');
			document.body.style.overflow = 'hidden';
		}

		function closeImageModal() {
			document.getElementById('imageModal').classList.add('hidden');
			document.body.style.overflow = 'auto';
		}

		// Cerrar modal con tecla ESC
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape') {
				closeImageModal();
			}
		});

		// Modal de eliminar comentario
		let deleteCommentId = null;
		let deleteCommentPostId = null;
		let deleteCommentFromIndex = false;

		function openDeleteCommentModal(commentId, postId, btnElement) {
			deleteCommentId = commentId;
			deleteCommentPostId = postId;
			deleteCommentFromIndex = true;
			
			// Obtener el preview del comentario desde el atributo data
			const btn = btnElement || document.querySelector('.delete-comment-btn[data-comment-preview]');
			const commentPreview = btn ? btn.getAttribute('data-comment-preview') : 'Este comentario';
			
			document.getElementById('deleteCommentPreview').textContent = commentPreview;
			document.getElementById('deleteCommentForm').action = `/comments/${commentId}`;
			document.getElementById('deleteCommentFromIndex').value = '1';
			document.getElementById('deleteCommentModal').classList.remove('hidden');
		}

		function closeDeleteCommentModal() {
			document.getElementById('deleteCommentModal').classList.add('hidden');
			deleteCommentId = null;
			deleteCommentPostId = null;
		}

		function confirmDeleteComment() {
			if(deleteCommentId) {
				document.getElementById('deleteCommentForm').submit();
			}
		}
	</script>

@push('scripts')
	@include('posts.partials.post-interactions-script')
@endpush
</x-layouts.app>

