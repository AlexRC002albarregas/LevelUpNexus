<x-layouts.app :title="'Publicación - LevelUp Nexus'">
	<div class="max-w-4xl mx-auto">
		<div class="mb-6">
			<a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 text-purple-400 hover:text-purple-300 transition mb-4">
				<i class="fas fa-arrow-left"></i> Volver al feed
			</a>
		</div>

		<article class="p-6 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm mb-6">
			<div class="flex items-start justify-between mb-4">
				<div class="flex items-center gap-3">
					<a href="{{ route('users.show', $post->user) }}" class="flex items-center gap-3 hover:opacity-80 transition">
						<div class="w-14 h-14 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold text-xl border-2 border-purple-400 overflow-hidden">
							@if($post->user->avatar)
								<img src="{{ asset('storage/' . $post->user->avatar) }}" class="w-full h-full object-cover" alt="{{ $post->user->name }}">
							@else
								{{ strtoupper(substr($post->user->name, 0, 1)) }}
							@endif
						</div>
						<div>
							<div class="font-bold text-lg text-purple-200">{{ $post->user->name }}</div>
							<div class="text-sm text-purple-400">
								<i class="fas fa-clock"></i> {{ $post->created_at->format('d/m/Y H:i') }}
								@if($post->group)
									· <i class="fas fa-users"></i> {{ $post->group->name }}
								@endif
								@if($post->visibility === 'private')
									· <i class="fas fa-lock"></i> Privada
								@endif
							</div>
						</div>
					</a>
				</div>
				@if(auth()->id() === $post->user_id || auth()->user()->isAdmin())
					<div class="flex gap-2">
						<a href="{{ route('posts.edit', $post) }}" class="px-4 py-2 rounded-lg bg-blue-600/30 hover:bg-blue-600/50 border border-blue-500/50 text-blue-300 transition" title="Editar">
							<i class="fas fa-edit"></i> Editar
						</a>
						<form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('¿Estás seguro de eliminar esta publicación?');" class="inline">
							@csrf
							@method('DELETE')
							<button type="submit" class="px-4 py-2 rounded-lg bg-red-600/30 hover:bg-red-600/50 border border-red-500/50 text-red-300 transition" title="Eliminar">
								<i class="fas fa-trash-alt"></i> Eliminar
							</button>
						</form>
					</div>
				@endif
			</div>

			<div class="text-purple-100 mb-6 whitespace-pre-wrap text-lg">{{ $post->content }}</div>

			<div class="flex items-center gap-6 pt-4 border-t border-purple-500/30 text-sm text-purple-400">
				<span><i class="fas fa-heart"></i> {{ $post->reactions->count() }} reacciones</span>
				<span><i class="fas fa-comment"></i> {{ $post->comments->count() }} comentarios</span>
			</div>
		</article>

		<!-- Reacciones -->
		<section class="p-6 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm mb-6">
			<h3 class="font-bold text-xl text-purple-200 mb-4">
				<i class="fas fa-heart"></i> Reacciones ({{ $post->reactions->count() }})
			</h3>
			
			<div class="flex flex-wrap gap-3 mb-4">
				@php
					$userReaction = $post->reactions->where('user_id', auth()->id())->first();
				@endphp
				<form method="POST" action="{{ route('reactions.toggle', $post) }}" class="inline">
					@csrf
					<input type="hidden" name="type" value="like">
					<button type="submit" class="px-4 py-2 rounded-lg {{ $userReaction && $userReaction->type === 'like' ? 'bg-blue-600/50' : 'bg-purple-600/30' }} hover:bg-purple-600/50 border border-purple-500/50 text-purple-200 text-sm font-semibold transition">
						<i class="fas fa-thumbs-up"></i> Like
					</button>
				</form>
				<form method="POST" action="{{ route('reactions.toggle', $post) }}" class="inline">
					@csrf
					<input type="hidden" name="type" value="love">
					<button type="submit" class="px-4 py-2 rounded-lg {{ $userReaction && $userReaction->type === 'love' ? 'bg-pink-600/50' : 'bg-purple-600/30' }} hover:bg-purple-600/50 border border-purple-500/50 text-purple-200 text-sm font-semibold transition">
						<i class="fas fa-heart"></i> Love
					</button>
				</form>
				<form method="POST" action="{{ route('reactions.toggle', $post) }}" class="inline">
					@csrf
					<input type="hidden" name="type" value="haha">
					<button type="submit" class="px-4 py-2 rounded-lg {{ $userReaction && $userReaction->type === 'haha' ? 'bg-yellow-600/50' : 'bg-purple-600/30' }} hover:bg-purple-600/50 border border-purple-500/50 text-purple-200 text-sm font-semibold transition">
						<i class="fas fa-laugh"></i> Haha
					</button>
				</form>
			</div>
			
			@if($post->reactions->count() > 0)
				<div class="flex flex-wrap gap-2 pt-4 border-t border-purple-500/30">
					@foreach($post->reactions->groupBy('type') as $type => $reactions)
						<div class="px-3 py-1 rounded-full bg-purple-600/30 border border-purple-500/50 text-sm text-purple-200">
							{{ $reactions->count() }} 
							<i class="fas fa-{{ $type === 'like' ? 'thumbs-up' : ($type === 'love' ? 'heart' : ($type === 'haha' ? 'laugh' : 'thumbs-down')) }}"></i>
						</div>
					@endforeach
				</div>
			@endif
		</section>

		<!-- Formulario de comentario -->
		<section class="p-6 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm mb-6">
			<h3 class="font-bold text-xl text-purple-200 mb-4">
				<i class="fas fa-comments"></i> Comentar
			</h3>
			<form method="POST" action="{{ route('comments.store') }}">
				@csrf
				<input type="hidden" name="post_id" value="{{ $post->id }}">
				<textarea 
					name="content" 
					required 
					rows="3"
					class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 resize-none mb-3"
					placeholder="Escribe tu comentario..."
				></textarea>
				@error('content')
					<p class="text-red-400 text-sm mb-3"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
				@enderror
				<button type="submit" class="px-6 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold text-white transition">
					<i class="fas fa-paper-plane"></i> Publicar Comentario
				</button>
			</form>
		</section>

		<!-- Comentarios -->
		<section class="p-6 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm mb-6">
			<h3 class="font-bold text-xl text-purple-200 mb-4">
				<i class="fas fa-comments"></i> Comentarios ({{ $post->comments->count() }})
			</h3>
			
			@forelse($post->comments as $comment)
				<div class="mb-4 pb-4 border-b border-purple-500/30 last:border-0 last:mb-0 last:pb-0">
					<div class="flex items-start justify-between mb-2">
						<div class="flex items-center gap-3 flex-1">
							<a href="{{ route('users.show', $comment->user) }}" class="flex items-center gap-2 hover:opacity-80 transition">
								<div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold border border-purple-400 overflow-hidden">
									@if($comment->user->avatar)
										<img src="{{ asset('storage/' . $comment->user->avatar) }}" class="w-full h-full object-cover" alt="{{ $comment->user->name }}">
									@else
										{{ strtoupper(substr($comment->user->name, 0, 1)) }}
									@endif
								</div>
								<div>
									<div class="font-semibold text-purple-200 text-sm">{{ $comment->user->name }}</div>
									<div class="text-xs text-purple-400">{{ $comment->created_at->diffForHumans() }}</div>
								</div>
							</a>
						</div>
						@if(auth()->id() === $comment->user_id || auth()->id() === $post->user_id)
							<div class="flex gap-2">
								<button 
									type="button" 
									onclick="openDeleteCommentModal({{ $comment->id }}, {{ $post->id }}, this)" 
									data-comment-preview="{{ htmlspecialchars(substr($comment->content, 0, 50) . '...', ENT_QUOTES, 'UTF-8') }}"
									class="px-3 py-1 rounded-lg bg-red-600/30 hover:bg-red-600/50 border border-red-500/50 text-red-300 text-xs transition delete-comment-btn" 
									title="Eliminar">
									<i class="fas fa-trash-alt"></i>
								</button>
							</div>
						@endif
					</div>
					<div class="text-purple-100 ml-14">{{ $comment->content }}</div>
				</div>
			@empty
				<p class="text-purple-400 text-center py-4">
					<i class="fas fa-comment-slash"></i> Aún no hay comentarios. ¡Sé el primero en comentar!
				</p>
			@endforelse
		</section>

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

		function openDeleteCommentModal(commentId, postId, btnElement) {
			deleteCommentId = commentId;
			deleteCommentPostId = postId;
			
			// Obtener el preview del comentario desde el atributo data
			const btn = btnElement || document.querySelector('.delete-comment-btn[data-comment-preview]');
			const commentPreview = btn ? btn.getAttribute('data-comment-preview') : 'Este comentario';
			
			document.getElementById('deleteCommentPreview').textContent = commentPreview;
			document.getElementById('deleteCommentForm').action = `/comments/${commentId}`;
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
