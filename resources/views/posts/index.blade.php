<x-layouts.app :title="'Publicaciones - LevelUp Nexus'">
	<div class="max-w-4xl mx-auto">
		<div class="flex items-center justify-between mb-8">
			<h1 class="text-4xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">
				<i class="fas fa-newspaper"></i> Feed de Publicaciones
			</h1>
			<a href="{{ route('posts.create') }}" class="px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold text-white transition glow-sm">
				<i class="fas fa-plus"></i> Nueva Publicación
			</a>
		</div>

		<div class="space-y-6">
			@forelse($posts as $post)
				<div class="p-6 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm card-hover">
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
										@if($post->group)
											· <i class="fas fa-users"></i> {{ $post->group->name }}
										@endif
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

					<div class="text-purple-100 mb-4 whitespace-pre-wrap">{{ $post->content }}</div>

					<!-- Reacciones -->
					<div class="mb-4 pt-4 border-t border-purple-500/30">
						@php
							$userReaction = $post->reactions->where('user_id', auth()->id())->first();
						@endphp
						<div class="flex items-center gap-2 mb-3">
							<form method="POST" action="{{ route('reactions.toggle', $post) }}" class="inline">
								@csrf
								<input type="hidden" name="type" value="like">
								<button type="submit" class="px-3 py-1.5 rounded-lg {{ $userReaction && $userReaction->type === 'like' ? 'bg-blue-600/50' : 'bg-purple-600/30' }} hover:bg-purple-600/50 border border-purple-500/50 text-purple-200 text-xs font-semibold transition">
									<i class="fas fa-thumbs-up"></i> Like
								</button>
							</form>
							<form method="POST" action="{{ route('reactions.toggle', $post) }}" class="inline">
								@csrf
								<input type="hidden" name="type" value="love">
								<button type="submit" class="px-3 py-1.5 rounded-lg {{ $userReaction && $userReaction->type === 'love' ? 'bg-pink-600/50' : 'bg-purple-600/30' }} hover:bg-purple-600/50 border border-purple-500/50 text-purple-200 text-xs font-semibold transition">
									<i class="fas fa-heart"></i> Love
								</button>
							</form>
							<form method="POST" action="{{ route('reactions.toggle', $post) }}" class="inline">
								@csrf
								<input type="hidden" name="type" value="haha">
								<button type="submit" class="px-3 py-1.5 rounded-lg {{ $userReaction && $userReaction->type === 'haha' ? 'bg-yellow-600/50' : 'bg-purple-600/30' }} hover:bg-purple-600/50 border border-purple-500/50 text-purple-200 text-xs font-semibold transition">
									<i class="fas fa-laugh"></i> Haha
								</button>
							</form>
							@if($post->reactions->count() > 0)
								<div class="ml-2 text-xs text-purple-400">
									@foreach($post->reactions->groupBy('type') as $type => $reactions)
										<span class="mr-2">
											{{ $reactions->count() }} 
											<i class="fas fa-{{ $type === 'like' ? 'thumbs-up' : ($type === 'love' ? 'heart' : ($type === 'haha' ? 'laugh' : 'thumbs-down')) }}"></i>
										</span>
									@endforeach
								</div>
							@endif
						</div>
					</div>

					<!-- Formulario de comentario -->
					<div class="mb-4 pb-4 border-b border-purple-500/30">
						<form method="POST" action="{{ route('comments.store') }}" class="flex gap-2">
							@csrf
							<input type="hidden" name="post_id" value="{{ $post->id }}">
							<input type="hidden" name="from_index" value="1">
							<input 
								type="text" 
								name="content" 
								required 
								placeholder="Escribe un comentario..."
								class="flex-1 bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-2 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 text-sm"
							>
							<button type="submit" class="px-4 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold text-white transition text-sm">
								<i class="fas fa-paper-plane"></i>
							</button>
						</form>
						@error('content')
							@if(request()->has('post_id') && request()->post_id == $post->id)
								<p class="text-red-400 text-xs mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
							@endif
						@enderror
					</div>

					<!-- Comentarios recientes -->
					@if($post->comments->count() > 0)
						<div class="mb-4">
							<div class="text-xs text-purple-400 mb-2">
								<i class="fas fa-comments"></i> {{ $post->comments->count() }} comentario{{ $post->comments->count() !== 1 ? 's' : '' }}
								@if($post->comments->count() > 3)
									· <a href="{{ route('posts.show', $post) }}" class="hover:text-purple-300">Ver todos</a>
								@endif
							</div>
							<div class="space-y-2">
								@foreach($post->comments->take(3) as $comment)
									<div class="flex items-start gap-2 text-sm">
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
												<span class="text-purple-300 ml-2">{{ $comment->content }}</span>
											</div>
											<div class="text-xs text-purple-400 mt-0.5">{{ $comment->created_at->diffForHumans() }}</div>
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
					@endif

					<div class="flex items-center justify-between pt-4 border-t border-purple-500/30">
						<div class="flex items-center gap-4 text-sm text-purple-400">
							<span><i class="fas fa-heart"></i> {{ $post->reactions->count() }} reacciones</span>
							<span><i class="fas fa-comment"></i> {{ $post->comments->count() }} comentarios</span>
						</div>
						<a href="{{ route('posts.show', $post) }}" class="px-4 py-2 rounded-lg bg-purple-600/30 hover:bg-purple-600/50 border border-purple-500/50 text-purple-300 text-sm font-semibold transition">
							<i class="fas fa-eye"></i> Ver completo
						</a>
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
			{{ $posts->links() }}
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
</x-layouts.app>
