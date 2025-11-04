<x-layouts.app :title="$group->name . ' - LevelUp Nexus'">
	<div class="max-w-5xl mx-auto">
		<div class="mb-6">
			<a href="{{ route('groups.index') }}" class="inline-flex items-center gap-2 text-purple-400 hover:text-purple-300 transition mb-4">
				<i class="fas fa-arrow-left"></i> Volver a grupos
			</a>
		</div>

		<!-- Header del Grupo -->
		<div class="p-8 rounded-2xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm mb-6">
			<div class="flex items-start gap-6">
				<div class="w-32 h-32 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-black text-4xl border-4 border-purple-400 overflow-hidden">
					@if($group->avatar)
						<img src="{{ asset('storage/' . $group->avatar) }}" class="w-full h-full object-cover" alt="{{ $group->name }}">
					@else
						{{ strtoupper(substr($group->name, 0, 2)) }}
					@endif
				</div>
				<div class="flex-1">
					<div class="flex items-center justify-between mb-4">
						<div>
							<h1 class="text-4xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent mb-2">
								{{ $group->name }}
							</h1>
							<div class="text-purple-400 mb-2">
								<i class="fas fa-user"></i> Propietario: 
								<a href="{{ route('users.show', $group->owner) }}" class="text-purple-300 hover:text-purple-200">{{ $group->owner->name }}</a>
							</div>
							<div class="text-purple-400">
								<i class="fas fa-users"></i> {{ $group->members->count() }} miembro{{ $group->members->count() !== 1 ? 's' : '' }}
							</div>
						</div>
						@if($isOwner)
							<div class="flex gap-2">
								<a href="{{ route('groups.edit', $group) }}" class="px-4 py-2 rounded-lg bg-blue-600/30 hover:bg-blue-600/50 border border-blue-500/50 text-blue-300 transition">
									<i class="fas fa-edit"></i> Editar
								</a>
								<form method="POST" action="{{ route('groups.destroy', $group) }}" onsubmit="return confirm('¿Estás seguro de eliminar este grupo?');" class="inline">
									@csrf
									@method('DELETE')
									<button type="submit" class="px-4 py-2 rounded-lg bg-red-600/30 hover:bg-red-600/50 border border-red-500/50 text-red-300 transition">
										<i class="fas fa-trash-alt"></i> Eliminar
									</button>
								</form>
							</div>
						@endif
					</div>
					@if($group->description)
						<p class="text-purple-200 mb-4">{{ $group->description }}</p>
					@endif
					
					<div class="flex gap-3">
						@if($isMember)
							<button type="button" onclick="openLeaveGroupModal({{ $group->id }}, '{{ $group->name }}')" class="px-4 py-2 rounded-lg bg-red-600/30 hover:bg-red-600/50 border border-red-500/50 text-red-300 transition">
								<i class="fas fa-sign-out-alt"></i> Abandonar Grupo
							</button>
						@else
							@if($receivedInvitation)
								<div class="flex gap-2">
									<form method="POST" action="{{ route('group-invitations.accept', $receivedInvitation) }}" class="inline">
										@csrf
										<button type="submit" class="px-4 py-2 rounded-lg bg-green-600/30 hover:bg-green-600/50 border border-green-500/50 text-green-300 transition">
											<i class="fas fa-check"></i> Aceptar Invitación
										</button>
									</form>
									<form method="POST" action="{{ route('group-invitations.decline', $receivedInvitation) }}" class="inline">
										@csrf
										<button type="submit" class="px-4 py-2 rounded-lg bg-red-600/30 hover:bg-red-600/50 border border-red-500/50 text-red-300 transition">
											<i class="fas fa-times"></i> Rechazar
										</button>
									</form>
								</div>
							@else
								<form method="POST" action="{{ route('groups.join', $group) }}" class="inline">
									@csrf
									<button type="submit" class="px-4 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold text-white transition">
										<i class="fas fa-user-plus"></i> Unirse al Grupo
									</button>
								</form>
							@endif
						@endif
					</div>
				</div>
			</div>
		</div>

		<!-- Invitar Usuario (Solo para owner/admins) -->
		@if($isOwner || auth()->user()->groups()->where('groups.id', $group->id)->where('member_role', 'admin')->exists())
			<div class="p-6 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm mb-6 relative z-50">
				<h3 class="font-bold text-xl text-purple-200 mb-4">
					<i class="fas fa-user-plus"></i> Invitar Usuario
				</h3>
				<form method="POST" action="{{ route('group-invitations.store', $group) }}">
					@csrf
					<div class="flex gap-3">
						<div class="flex-1 relative">
							<input 
								id="inviteUserInput"
								type="text" 
								name="username" 
								autocomplete="off"
								required 
								class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
								placeholder="Escribe para buscar..."
							>
							<!-- Dropdown de resultados -->
							<div id="inviteUserResults" class="hidden absolute z-[200] w-full mt-2 bg-slate-800 border border-purple-500/50 rounded-lg max-h-60 overflow-y-auto shadow-2xl">
								<!-- Los resultados se insertarán aquí con JavaScript -->
							</div>
						</div>
						<button type="submit" class="px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold text-white transition">
							<i class="fas fa-paper-plane"></i> Enviar Invitación
						</button>
					</div>
					@error('username')
						<p class="text-red-400 text-sm mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
					@enderror
				</form>
			</div>
		@endif

		<!-- Invitaciones Pendientes (Solo para owner) -->
		@if($isOwner && $pendingInvitations && $pendingInvitations->count() > 0)
			<div class="p-6 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm mb-6">
				<h3 class="font-bold text-xl text-purple-200 mb-4">
					<i class="fas fa-envelope"></i> Invitaciones Pendientes
				</h3>
				<div class="space-y-3">
					@foreach($pendingInvitations as $invitation)
						<div class="flex items-center justify-between p-4 rounded-lg bg-slate-900/50 border border-purple-500/30">
							<div class="flex items-center gap-3">
								<div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold overflow-hidden">
									@if($invitation->recipient->avatar)
										<img src="{{ asset('storage/' . $invitation->recipient->avatar) }}" class="w-full h-full object-cover" alt="{{ $invitation->recipient->name }}">
									@else
										{{ strtoupper(substr($invitation->recipient->name, 0, 1)) }}
									@endif
								</div>
								<div>
									<div class="font-semibold text-purple-200">{{ $invitation->recipient->name }}</div>
									<div class="text-sm text-purple-400">{{ $invitation->created_at->diffForHumans() }}</div>
								</div>
							</div>
							<form method="POST" action="{{ route('group-invitations.cancel', $invitation) }}" class="inline">
								@csrf
								<button type="submit" class="px-3 py-2 rounded-lg bg-red-600/30 hover:bg-red-600/50 border border-red-500/50 text-red-300 text-sm transition">
									<i class="fas fa-times"></i> Cancelar
								</button>
							</form>
						</div>
					@endforeach
				</div>
			</div>
		@endif

		<!-- Miembros (Solo para miembros) -->
		@if($isMember || $isOwner)
			<div class="p-6 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm mb-6">
				<h3 class="font-bold text-xl text-purple-200 mb-4">
					<i class="fas fa-users"></i> Miembros ({{ $group->members->count() }})
				</h3>
				<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
					@foreach($group->members as $member)
						<a href="{{ route('users.show', $member) }}" class="p-4 rounded-lg bg-slate-900/50 border border-purple-500/30 hover:bg-purple-500/20 transition text-center">
							<div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold text-xl border-2 border-purple-400 overflow-hidden mb-2">
								@if($member->avatar)
									<img src="{{ asset('storage/' . $member->avatar) }}" class="w-full h-full object-cover" alt="{{ $member->name }}">
								@else
									{{ strtoupper(substr($member->name, 0, 1)) }}
								@endif
							</div>
							<div class="font-semibold text-purple-200 text-sm">{{ $member->name }}</div>
							<div class="text-xs text-purple-400">
								@if($member->pivot->member_role === 'admin')
									<i class="fas fa-crown"></i> Admin
								@else
									<i class="fas fa-user"></i> Miembro
								@endif
							</div>
						</a>
					@endforeach
				</div>
			</div>
		@endif

		<!-- Publicaciones del Grupo (Solo para miembros) -->
		@if($isMember || $isOwner)
			<div class="p-6 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm">
				<h3 class="font-bold text-xl text-purple-200 mb-4">
					<i class="fas fa-newspaper"></i> Publicaciones del Grupo ({{ $group->posts->count() }})
				</h3>
					@forelse($group->posts as $post)
					<div class="mb-4 pb-4 border-b border-purple-500/30 last:border-0 last:mb-0 last:pb-0">
						<div class="flex items-center gap-2 mb-2">
							<a href="{{ route('users.show', $post->user) }}" class="font-semibold text-purple-200 hover:text-purple-100">
								{{ $post->user->name }}
							</a>
							<span class="text-purple-400 text-sm">{{ $post->created_at->diffForHumans() }}</span>
						</div>
						<p class="text-purple-100 mb-2">{{ Str::limit($post->content, 200) }}</p>
						<a href="{{ route('posts.show', $post) }}" class="text-purple-400 hover:text-purple-300 text-sm">
							<i class="fas fa-arrow-right"></i> Ver publicación completa
						</a>
					</div>
				@empty
					<p class="text-purple-400 text-center py-4">
						<i class="fas fa-inbox"></i> Aún no hay publicaciones en este grupo
					</p>
				@endforelse
			</div>
		</div>
		@else
			<div class="p-6 rounded-xl bg-yellow-500/10 border border-yellow-500/30 backdrop-blur-sm mb-6">
				<div class="text-center">
					<i class="fas fa-lock text-4xl text-yellow-400 mb-4"></i>
					<p class="text-yellow-300 font-semibold mb-2">Debes ser miembro para ver el contenido del grupo</p>
					<p class="text-yellow-400 text-sm">Acepta la invitación para acceder a las publicaciones y contenido del grupo.</p>
				</div>
			</div>
		@endif
	</div>

	<!-- Modal de Confirmación de Abandonar Grupo -->
	<div id="leaveGroupModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
		<div class="bg-slate-900 rounded-2xl w-full max-w-md border-2 border-red-500 glow">
			<div class="p-6 border-b border-red-500/30">
				<div class="flex items-center gap-3">
					<div class="w-12 h-12 rounded-full bg-red-600/20 border border-red-500 flex items-center justify-center">
						<i class="fas fa-sign-out-alt text-red-500 text-2xl"></i>
					</div>
					<h3 class="text-xl font-bold text-red-400">
						Abandonar Grupo
					</h3>
				</div>
			</div>
			
			<div class="p-6">
				<p class="text-purple-200 mb-4">
					¿Estás seguro de que quieres abandonar el grupo <strong class="text-pink-400" id="leaveGroupName"></strong>?
				</p>
				<p class="text-sm text-purple-400 mb-6">
					Esta acción no se puede deshacer. Ya no podrás ver las publicaciones ni el contenido del grupo.
				</p>
				
				<form id="leaveGroupForm" method="POST" action="">
					@csrf
				</form>
				
				<div class="flex gap-3">
					<button type="button" onclick="closeLeaveGroupModal()" class="flex-1 px-6 py-3 rounded-lg border border-purple-500 hover:bg-purple-500/20 transition font-semibold text-purple-300">
						<i class="fas fa-times"></i> Cancelar
					</button>
					<button type="button" onclick="confirmLeaveGroup()" class="flex-1 px-6 py-3 rounded-lg bg-red-600 hover:bg-red-700 font-bold text-white transition">
						<i class="fas fa-sign-out-alt"></i> Abandonar
					</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		// Modal de abandonar grupo
		let leaveGroupId = null;
		let leaveGroupName = '';

		function openLeaveGroupModal(groupId, groupName) {
			leaveGroupId = groupId;
			leaveGroupName = groupName;
			document.getElementById('leaveGroupName').textContent = groupName;
			document.getElementById('leaveGroupForm').action = `/groups/${groupId}/leave`;
			document.getElementById('leaveGroupModal').classList.remove('hidden');
		}

		function closeLeaveGroupModal() {
			document.getElementById('leaveGroupModal').classList.add('hidden');
			leaveGroupId = null;
			leaveGroupName = '';
		}

		function confirmLeaveGroup() {
			if(leaveGroupId) {
				document.getElementById('leaveGroupForm').submit();
			}
		}

		// Autocompletado para invitar usuario a grupo
		let inviteUserTimeout;
		const inviteUserInput = document.getElementById('inviteUserInput');
		const inviteUserResults = document.getElementById('inviteUserResults');

		if(inviteUserInput) {
			inviteUserInput.addEventListener('input', function(e) {
				const query = e.target.value.trim();
				
				clearTimeout(inviteUserTimeout);
				
				if(query.length < 2) {
					inviteUserResults.classList.add('hidden');
					return;
				}

				inviteUserTimeout = setTimeout(async () => {
					try {
						const res = await fetch(`/api/users/search?q=${encodeURIComponent(query)}`, {
							credentials: 'same-origin',
							headers: {
								'Accept': 'application/json',
							}
						});
						const data = await res.json();
						
						if(data.results && data.results.length > 0) {
							inviteUserResults.innerHTML = '';
							data.results.forEach(user => {
								const div = document.createElement('div');
								div.className = 'p-3 hover:bg-purple-500/20 cursor-pointer border-b border-purple-500/30 last:border-0 transition';
								div.onclick = () => {
									inviteUserInput.value = user.name;
									inviteUserResults.classList.add('hidden');
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
								inviteUserResults.appendChild(div);
							});
							inviteUserResults.classList.remove('hidden');
						} else {
							inviteUserResults.innerHTML = '<div class="p-3 text-purple-400 text-center">No se encontraron usuarios</div>';
							inviteUserResults.classList.remove('hidden');
						}
					} catch (error) {
						console.error('Error buscando usuarios:', error);
						inviteUserResults.classList.add('hidden');
					}
				}, 300);
			});

			// Ocultar resultados al hacer clic fuera
			document.addEventListener('click', function(e) {
				if(!inviteUserInput.contains(e.target) && !inviteUserResults.contains(e.target)) {
					inviteUserResults.classList.add('hidden');
				}
			});
		}
	</script>
</x-layouts.app>

