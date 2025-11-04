<x-layouts.app :title="'Mis amigos - LevelUp Nexus'">
	<div class="flex items-center justify-between mb-8">
		<h1 class="text-4xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">
			<i class="fas fa-user-friends"></i> Mis Amigos
		</h1>
		<button onclick="document.getElementById('addFriendModal').classList.remove('hidden')" class="px-5 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold glow transition">
			<i class="fas fa-user-plus"></i> Añadir amigo
		</button>
	</div>

	@if($pending->count() > 0)
		<section class="mb-8 p-6 rounded-2xl bg-yellow-500/10 border border-yellow-500 backdrop-blur-sm glow-sm">
			<h3 class="font-bold text-xl mb-4 text-yellow-300"><i class="fas fa-clock"></i> Solicitudes pendientes ({{ $pending->count() }})</h3>
			<div class="space-y-3">
				@foreach($pending as $req)
					<div class="flex items-center justify-between p-4 rounded-xl bg-slate-800/50 border border-yellow-500/30 card-hover">
						<div class="flex items-center gap-3">
							<div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold text-lg overflow-hidden">
								@if($req->sender->avatar)
									<img src="{{ asset('storage/' . $req->sender->avatar) }}" class="w-full h-full object-cover" alt="{{ $req->sender->name }}">
								@else
									{{ strtoupper(substr($req->sender->name, 0, 1)) }}
								@endif
							</div>
							<div>
								<div class="font-bold text-purple-200">{{ $req->sender->name }}</div>
								<div class="text-sm text-purple-400">{{ $req->sender->email }}</div>
							</div>
						</div>
						<div class="flex gap-2">
							<form method="POST" action="{{ route('friends.accept', $req) }}">
								@csrf
								<button class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white font-bold transition glow-sm">
									<i class="fas fa-check"></i> Aceptar
								</button>
							</form>
							<form method="POST" action="{{ route('friends.decline', $req) }}">
								@csrf
								<button class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold transition">
									<i class="fas fa-times"></i> Rechazar
								</button>
							</form>
						</div>
					</div>
				@endforeach
			</div>
		</section>
	@endif

	<section>
		<h3 class="font-bold text-2xl mb-4 text-purple-300"><i class="fas fa-users"></i> Amigos conectados ({{ $friends->count() }})</h3>
		@if($friends->count() > 0)
			<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
				@foreach($friends as $friend)
					<div onclick="openChat({{ $friend->id }}, '{{ $friend->name }}')" class="p-5 rounded-xl bg-slate-800/50 border border-purple-500/30 card-hover backdrop-blur-sm cursor-pointer relative">
						@if(isset($unreadCounts[$friend->id]) && $unreadCounts[$friend->id] > 0)
							<div class="absolute -top-2 -right-2 bg-pink-600 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center notification-badge border-2 border-slate-900">
								{{ $unreadCounts[$friend->id] }}
							</div>
						@endif
						<div class="flex items-center gap-3 mb-2">
							<div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center font-black text-xl overflow-hidden border-2 border-purple-400 relative">
								@if($friend->avatar)
									<img src="{{ asset('storage/' . $friend->avatar) }}" class="w-full h-full object-cover" alt="{{ $friend->name }}">
								@else
									{{ strtoupper(substr($friend->name, 0, 1)) }}
								@endif
								@if(isset($unreadCounts[$friend->id]) && $unreadCounts[$friend->id] > 0)
									<div class="absolute -bottom-1 -right-1 w-4 h-4 bg-pink-600 rounded-full border-2 border-slate-900 flex items-center justify-center">
										<i class="fas fa-envelope text-[8px] text-white"></i>
									</div>
								@endif
							</div>
							<div class="flex-1">
								<div class="font-bold text-lg text-purple-200">{{ $friend->name }}</div>
								<div class="text-sm text-purple-400"><i class="fas fa-envelope"></i> {{ $friend->email }}</div>
							</div>
						</div>
						<div class="mt-3 pt-3 border-t border-purple-500/30 text-sm text-purple-300 flex items-center justify-between">
							<span><i class="fas fa-comment"></i> Abrir chat</span>
							@if(isset($unreadCounts[$friend->id]) && $unreadCounts[$friend->id] > 0)
								<span class="text-pink-400 font-bold text-xs">
									<i class="fas fa-circle text-pink-500 pulse-glow"></i> {{ $unreadCounts[$friend->id] }} nuevo{{ $unreadCounts[$friend->id] > 1 ? 's' : '' }}
								</span>
							@endif
						</div>
					<div class="flex gap-2 mt-2">
						<a href="{{ route('users.show', $friend) }}" onclick="event.stopPropagation()" class="flex-1 text-center px-3 py-2 rounded-lg bg-purple-600/30 hover:bg-purple-600/50 border border-purple-500/50 text-xs font-semibold text-purple-300 transition">
							<i class="fas fa-user"></i> Ver perfil
						</a>
						<button type="button" onclick="event.stopPropagation(); openDeleteFriendModal({{ $friend->id }}, '{{ $friend->name }}', {{ $friend->friendship_id }})" class="px-3 py-2 rounded-lg bg-red-600/30 hover:bg-red-600/50 border border-red-500/50 text-xs font-semibold text-red-300 transition">
							<i class="fas fa-user-times"></i>
						</button>
					</div>
				</div>
				@endforeach
			</div>
		@else
			<div class="text-center py-12 p-8 rounded-xl bg-slate-800/30 border border-purple-500/20">
				<i class="fas fa-user-plus text-6xl text-purple-500/50 mb-4"></i>
				<p class="text-purple-300 text-lg">Aún no tienes amigos. Envía solicitudes para conectar con otros gamers.</p>
			</div>
		@endif
	</section>

	<!-- Modal Añadir Amigo -->
	<div id="addFriendModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50" onclick="if(event.target===this) this.classList.add('hidden')">
		<div class="bg-slate-900 rounded-2xl p-8 max-w-md w-full mx-4 border border-purple-500 glow">
			<h3 class="text-2xl font-bold mb-6 bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">
				<i class="fas fa-user-plus"></i> Añadir amigo
			</h3>
			<form method="POST" action="{{ route('friends.send') }}">
				@csrf
				<div class="mb-6 relative">
					<label class="block text-sm mb-2 text-purple-300 font-semibold">Nombre de usuario o email</label>
					<input 
						id="addFriendInput" 
						name="username" 
						type="text"
						autocomplete="off"
						required 
						class="w-full bg-slate-800 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50" 
						placeholder="Escribe para buscar..."
					>
					<!-- Dropdown de resultados -->
					<div id="addFriendResults" class="hidden absolute z-50 w-full mt-2 bg-slate-800 border border-purple-500/50 rounded-lg max-h-60 overflow-y-auto shadow-xl">
						<!-- Los resultados se insertarán aquí con JavaScript -->
					</div>
				</div>
				<div class="flex gap-3 justify-end">
					<button type="button" onclick="document.getElementById('addFriendModal').classList.add('hidden')" class="px-5 py-2 rounded-lg border border-purple-500 hover:bg-purple-500/20 transition">
						Cancelar
					</button>
					<button class="px-5 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold glow-sm transition">
						<i class="fas fa-paper-plane"></i> Enviar solicitud
					</button>
				</div>
			</form>
		</div>
	</div>

	<!-- Modal Chat -->
	<div id="chatModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50">
		<div class="bg-slate-900 rounded-2xl w-full max-w-2xl mx-4 border border-purple-500 glow flex flex-col" style="height: 600px;">
			<div class="p-6 border-b border-purple-500/30 flex items-center justify-between">
				<div class="flex items-center gap-3">
					<div id="chatAvatar" class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center font-bold text-lg"></div>
					<div>
						<h3 id="chatName" class="text-xl font-bold text-purple-200"></h3>
						<div class="text-sm text-green-400"><i class="fas fa-circle text-xs"></i> Online</div>
					</div>
				</div>
				<button onclick="closeChat()" class="text-purple-400 hover:text-white transition">
					<i class="fas fa-times text-2xl"></i>
				</button>
			</div>
			<div id="chatMessages" class="flex-1 overflow-y-auto p-6 space-y-3"></div>
			<div class="p-4 border-t border-purple-500/30">
				<form id="chatForm" class="flex gap-2" onsubmit="sendMessage(event)">
					<input id="chatInput" type="text" placeholder="Escribe un mensaje..." class="flex-1 bg-slate-800 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50">
					<button type="submit" class="px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold glow-sm transition">
						<i class="fas fa-paper-plane"></i>
					</button>
				</form>
			</div>
		</div>
	</div>

	<script>
		let currentFriendId = null;
		let currentFriendName = '';

		function openChat(friendId, friendName) {
			currentFriendId = friendId;
			currentFriendName = friendName;
			document.getElementById('chatName').textContent = friendName;
			document.getElementById('chatAvatar').textContent = friendName.charAt(0).toUpperCase();
			document.getElementById('chatModal').classList.remove('hidden');
			loadMessages();
			
			// Ocultar el badge de notificación de este amigo
			updateFriendBadge(friendId, 0);
		}

		function closeChat() {
			document.getElementById('chatModal').classList.add('hidden');
			currentFriendId = null;
		}

		async function loadMessages() {
			const res = await fetch(`/messages/${currentFriendId}`);
			const data = await res.json();
			const container = document.getElementById('chatMessages');
			container.innerHTML = '';
			
			data.messages.forEach(msg => {
				const isMe = msg.sender_id === {{ auth()->id() }};
				const div = document.createElement('div');
				div.className = `flex ${isMe ? 'justify-end' : 'justify-start'}`;
				div.innerHTML = `
					<div class="max-w-xs ${isMe ? 'bg-purple-600' : 'bg-slate-800'} rounded-2xl px-4 py-2 border ${isMe ? 'border-purple-500' : 'border-purple-500/30'}">
						<p class="text-white">${msg.content}</p>
						<div class="text-xs text-purple-300 mt-1">${new Date(msg.created_at).toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'})}</div>
					</div>
				`;
				container.appendChild(div);
			});
			container.scrollTop = container.scrollHeight;
			
			// Actualizar el contador global de notificaciones
			updateGlobalNotificationCount();
		}

		async function sendMessage(e) {
			e.preventDefault();
			const input = document.getElementById('chatInput');
			const content = input.value.trim();
			if(!content) return;

			const formData = new FormData();
			formData.append('content', content);
			formData.append('_token', '{{ csrf_token() }}');

			const res = await fetch(`/messages/${currentFriendId}`, {
				method: 'POST',
				body: formData
			});

			if(res.ok) {
				input.value = '';
				loadMessages();
			}
		}

		function updateFriendBadge(friendId, count) {
			// Buscar la tarjeta del amigo
			const friendCards = document.querySelectorAll('[onclick*="openChat(' + friendId + ',"]');
			if (friendCards.length > 0) {
				const card = friendCards[0];
				
				// Ocultar o eliminar badges
				const topBadge = card.querySelector('.absolute.-top-2.-right-2');
				if (topBadge) topBadge.style.display = 'none';
				
				const avatarBadge = card.querySelector('.absolute.-bottom-1.-right-1');
				if (avatarBadge) avatarBadge.style.display = 'none';
				
				const footerText = card.querySelector('.text-pink-400');
				if (footerText) footerText.style.display = 'none';
			}
		}

		async function updateGlobalNotificationCount() {
			try {
				const res = await fetch('/api/notifications/count');
				const data = await res.json();
				const badge = document.querySelector('.notification-badge');
				if (badge) {
					if (data.count > 0) {
						badge.textContent = data.count;
						badge.style.display = 'flex';
					} else {
						badge.style.display = 'none';
					}
				}
			} catch (error) {
				console.log('Error al actualizar notificaciones');
			}
		}

		// Actualizar mensajes cada 10 segundos si el chat está abierto
		setInterval(() => {
			if(currentFriendId) {
				loadMessages();
			}
		}, 10000);
		
		// Actualizar contador global cada 10 segundos
		setInterval(() => {
			updateGlobalNotificationCount();
		}, 10000);

		// Modal de eliminar amigo
		let deleteFriendshipId = null;
		let deleteFriendName = '';

		function openDeleteFriendModal(friendId, friendName, friendshipId) {
			deleteFriendshipId = friendshipId;
			deleteFriendName = friendName;
			document.getElementById('deleteFriendName').textContent = friendName;
			document.getElementById('deleteFriendModal').classList.remove('hidden');
		}

		function closeDeleteFriendModal() {
			document.getElementById('deleteFriendModal').classList.add('hidden');
			deleteFriendshipId = null;
			deleteFriendName = '';
		}

		function confirmDeleteFriend() {
			if(deleteFriendshipId) {
				document.getElementById('deleteFriendForm').action = `/friends/${deleteFriendshipId}`;
				document.getElementById('deleteFriendForm').submit();
			}
		}

		// Autocompletado para añadir amigo
		let searchTimeout;
		const addFriendInput = document.getElementById('addFriendInput');
		const addFriendResults = document.getElementById('addFriendResults');

		if(addFriendInput) {
			addFriendInput.addEventListener('input', function(e) {
				const query = e.target.value.trim();
				
				clearTimeout(searchTimeout);
				
				if(query.length < 2) {
					addFriendResults.classList.add('hidden');
					return;
				}

				searchTimeout = setTimeout(async () => {
					try {
						const res = await fetch(`/api/users/search?q=${encodeURIComponent(query)}`, {
							credentials: 'same-origin',
							headers: {
								'Accept': 'application/json',
							}
						});
						const data = await res.json();
						
						if(data.results && data.results.length > 0) {
							addFriendResults.innerHTML = '';
							data.results.forEach(user => {
								const div = document.createElement('div');
								div.className = 'p-3 hover:bg-purple-500/20 cursor-pointer border-b border-purple-500/30 last:border-0 transition';
								div.onclick = () => {
									addFriendInput.value = user.name;
									addFriendResults.classList.add('hidden');
								};
								div.innerHTML = `
									<div class="flex items-center gap-3">
										<div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold overflow-hidden border border-purple-400">
											${user.avatar ? `<img src="/storage/${user.avatar}" class="w-full h-full object-cover" alt="${user.name}">` : user.name.charAt(0).toUpperCase()}
										</div>
										<div class="flex-1">
											<div class="font-semibold text-purple-200">${user.name}</div>
											<div class="text-sm text-purple-400">${user.email}</div>
										</div>
									</div>
								`;
								addFriendResults.appendChild(div);
							});
							addFriendResults.classList.remove('hidden');
						} else {
							addFriendResults.innerHTML = '<div class="p-3 text-purple-400 text-center">No se encontraron usuarios</div>';
							addFriendResults.classList.remove('hidden');
						}
					} catch (error) {
						console.error('Error buscando usuarios:', error);
						addFriendResults.classList.add('hidden');
					}
				}, 300);
			});

			// Ocultar resultados al hacer clic fuera
			document.addEventListener('click', function(e) {
				if(!addFriendInput.contains(e.target) && !addFriendResults.contains(e.target)) {
					addFriendResults.classList.add('hidden');
				}
			});
		}
	</script>

	<!-- Modal de Confirmación de Eliminación de Amigo -->
	<div id="deleteFriendModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
		<div class="bg-slate-900 rounded-2xl w-full max-w-md border-2 border-red-500 glow">
			<div class="p-6 border-b border-red-500/30">
				<div class="flex items-center gap-3">
					<div class="w-12 h-12 rounded-full bg-red-600/20 border border-red-500 flex items-center justify-center">
						<i class="fas fa-user-times text-red-500 text-2xl"></i>
					</div>
					<h3 class="text-xl font-bold text-red-400">
						Eliminar Amigo
					</h3>
				</div>
			</div>
			
			<div class="p-6">
				<p class="text-purple-200 mb-4">
					¿Estás seguro de que quieres eliminar a <strong class="text-pink-400" id="deleteFriendName"></strong> de tu lista de amigos?
				</p>
				<p class="text-sm text-purple-400 mb-6">
					Se eliminarán todos los mensajes entre vosotros y ya no podrás ver su perfil si es privado.
				</p>
				
				<form id="deleteFriendForm" method="POST" action="">
					@csrf
					@method('DELETE')
				</form>
				
				<div class="flex gap-3">
					<button type="button" onclick="closeDeleteFriendModal()" class="flex-1 px-6 py-3 rounded-lg border border-purple-500 hover:bg-purple-500/20 transition font-semibold text-purple-300">
						<i class="fas fa-times"></i> Cancelar
					</button>
					<button type="button" onclick="confirmDeleteFriend()" class="flex-1 px-6 py-3 rounded-lg bg-red-600 hover:bg-red-700 font-bold text-white transition">
						<i class="fas fa-trash-alt"></i> Eliminar
					</button>
				</div>
			</div>
		</div>
	</div>
</x-layouts.app>

