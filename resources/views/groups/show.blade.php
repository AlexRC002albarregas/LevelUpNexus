<x-layouts.app :title="$group->name . ' - LevelUp Nexus'">
	<div class="max-w-7xl mx-auto px-4">
		<div class="mb-6">
			<a href="{{ route('groups.index') }}" class="inline-flex items-center gap-2 text-purple-400 hover:text-purple-300 transition mb-4">
				<i class="fas fa-arrow-left"></i> Volver a grupos
			</a>
		</div>

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

		<!-- Layout de dos columnas según el wireframe -->
		<div class="flex flex-col lg:flex-row gap-6">
			<!-- Columna izquierda: Header del grupo y Publicaciones -->
			<div class="flex-1 space-y-6">
				<!-- Header del Grupo (compacto) -->
				<div class="p-4 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm">
					<div class="flex items-center gap-4">
						<div class="w-16 h-16 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-black text-xl border-2 border-purple-400 overflow-hidden flex-shrink-0">
							@if($group->avatar)
								<img src="{{ asset('storage/' . $group->avatar) }}" class="w-full h-full object-cover" alt="{{ $group->name }}">
							@else
								{{ strtoupper(substr($group->name, 0, 2)) }}
							@endif
						</div>
						<div class="flex-1 min-w-0">
							<div class="flex items-center gap-3 flex-wrap mb-2">
								<h1 class="text-2xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">
									{{ $group->name }}
								</h1>
								@if($isOwner)
									<a href="{{ route('groups.edit', $group) }}" class="px-2 py-1 rounded bg-blue-600/30 hover:bg-blue-600/50 border border-blue-500/50 text-blue-300 transition text-sm">
										<i class="fas fa-edit"></i>
									</a>
									<form method="POST" action="{{ route('groups.destroy', $group) }}" onsubmit="return confirm('¿Estás seguro de eliminar este grupo?');" class="inline">
										@csrf
										@method('DELETE')
										<button type="submit" class="px-2 py-1 rounded bg-red-600/30 hover:bg-red-600/50 border border-red-500/50 text-red-300 transition text-sm">
											<i class="fas fa-trash-alt"></i>
										</button>
									</form>
								@endif
							</div>
							<div class="flex items-center gap-3 flex-wrap text-sm text-purple-400">
								<div>
									<i class="fas fa-user"></i> 
									<a href="{{ route('users.show', $group->owner) }}" class="text-purple-300 hover:text-purple-200">{{ $group->owner->name }}</a>
								</div>
								<div>
									<i class="fas fa-users"></i> {{ $group->members->count() }} miembro{{ $group->members->count() !== 1 ? 's' : '' }}
								</div>
								@if($group->description)
									<div class="text-purple-300">· {{ Str::limit($group->description, 50) }}</div>
								@endif
								@if($isMember)
									<button type="button" onclick="openLeaveGroupModal({{ $group->id }}, '{{ $group->name }}')" class="px-3 py-1 rounded bg-red-600/30 hover:bg-red-600/50 border border-red-500/50 text-red-300 transition text-sm">
										<i class="fas fa-sign-out-alt"></i> Abandonar
									</button>
								@else
									@if($receivedInvitation)
										<form method="POST" action="{{ route('group-invitations.accept', $receivedInvitation) }}" class="inline">
											@csrf
											<button type="submit" class="px-3 py-1 rounded bg-green-600/30 hover:bg-green-600/50 border border-green-500/50 text-green-300 transition text-sm">
												<i class="fas fa-check"></i> Aceptar Invitación
											</button>
										</form>
										<form method="POST" action="{{ route('group-invitations.decline', $receivedInvitation) }}" class="inline">
											@csrf
											<button type="submit" class="px-3 py-1 rounded bg-red-600/30 hover:bg-red-600/50 border border-red-500/50 text-red-300 transition text-sm">
												<i class="fas fa-times"></i> Rechazar
											</button>
										</form>
									@else
										<form method="POST" action="{{ route('groups.join', $group) }}" class="inline">
											@csrf
											<button type="submit" class="px-3 py-1 rounded bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold text-white transition text-sm">
												<i class="fas fa-user-plus"></i> Unirse al Grupo
											</button>
										</form>
									@endif
								@endif
							</div>
						</div>
					</div>
				</div>

				<!-- Chat del Grupo -->
				<div class="rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm flex flex-col" style="height: calc(100vh - 300px); min-height: 500px;">
					@if($isMember || $isOwner)
						<!-- Área de Mensajes -->
						<div class="flex-1 overflow-y-auto p-4 space-y-4" id="chatMessages">
							@forelse($group->posts->sortBy('created_at') as $post)
								@php
									$isOwnMessage = $post->user_id === auth()->id();
								@endphp
								<div class="flex {{ $isOwnMessage ? 'justify-end' : 'justify-start' }} items-end gap-2">
									@if(!$isOwnMessage)
										<a href="{{ route('users.show', $post->user) }}" class="flex-shrink-0">
											<div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold text-xs overflow-hidden border-2 border-purple-400">
												@if($post->user->avatar)
													<img src="{{ asset('storage/' . $post->user->avatar) }}" class="w-full h-full object-cover" alt="{{ $post->user->name }}">
												@else
													{{ strtoupper(substr($post->user->name, 0, 1)) }}
												@endif
											</div>
										</a>
									@endif
									
									<div class="max-w-[70%] flex flex-col {{ $isOwnMessage ? 'items-end' : 'items-start' }}">
										@if(!$isOwnMessage)
											<span class="text-xs text-purple-400 mb-1 px-2">{{ $post->user->name }}</span>
										@endif
										
										<div class="rounded-2xl px-4 py-2 {{ $isOwnMessage ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-br-sm' : 'bg-slate-700/70 text-purple-100 rounded-bl-sm' }} relative group">
											<p class="whitespace-pre-wrap break-words text-sm">{{ $post->content }}</p>
											
											@if($post->image)
												<div class="mt-2">
													<img 
														src="{{ asset('storage/' . $post->image) }}" 
														alt="Imagen del mensaje" 
														class="w-32 h-32 object-cover rounded-lg cursor-pointer hover:opacity-80 hover:scale-105 transition"
														onclick="openImageModal('{{ asset('storage/' . $post->image) }}')"
														title="Click para ver en tamaño completo"
													>
												</div>
											@endif

											@php
												$canDeleteMessage = false;
												if(auth()->id() === $post->user_id) {
													$canDeleteMessage = true;
												} elseif($isOwner || $isModerator) {
													$canDeleteMessage = true;
												}
											@endphp

											@if($canDeleteMessage)
												<button 
													type="button"
													onclick="openDeleteMessageModal({{ $post->id }})"
													class="absolute -top-2 {{ $isOwnMessage ? '-left-2' : '-right-2' }} w-6 h-6 rounded-full bg-red-600 hover:bg-red-700 text-white flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity"
													title="Eliminar mensaje"
												>
													<i class="fas fa-times"></i>
												</button>
											@endif
										</div>
										
										<span class="text-xs text-purple-400/70 mt-1 px-2">
											@if($post->created_at->isToday())
												{{ $post->created_at->format('H:i') }}
											@elseif($post->created_at->isYesterday())
												Ayer {{ $post->created_at->format('H:i') }}
											@else
												{{ $post->created_at->format('d/m/Y H:i') }}
											@endif
										</span>
									</div>

									@if($isOwnMessage)
										<a href="{{ route('users.show', $post->user) }}" class="flex-shrink-0">
											<div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold text-xs overflow-hidden border-2 border-purple-400">
												@if($post->user->avatar)
													<img src="{{ asset('storage/' . $post->user->avatar) }}" class="w-full h-full object-cover" alt="{{ $post->user->name }}">
												@else
													{{ strtoupper(substr($post->user->name, 0, 1)) }}
												@endif
											</div>
										</a>
									@endif
								</div>
							@empty
								<div class="flex items-center justify-center h-full">
									<div class="text-center">
										<i class="fas fa-comments text-4xl text-purple-400/50 mb-2"></i>
										<p class="text-purple-400">Aún no hay mensajes en este grupo</p>
										<p class="text-purple-400/70 text-sm">¡Sé el primero en enviar un mensaje!</p>
									</div>
								</div>
							@endforelse
						</div>

						<!-- Formulario de Envío (Fijo abajo) -->
						<div class="border-t border-purple-500/30 p-4 bg-slate-900/50">
							<form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" id="chatForm">
								@csrf
								<input type="hidden" name="group_id" value="{{ $group->id }}">
								<input type="hidden" name="visibility" value="group">

								<!-- Preview de imagen -->
								<div id="imagePreview" class="hidden mb-3 relative inline-block">
									<img id="previewImg" src="" alt="Preview" class="max-h-32 rounded-lg border border-purple-500/30">
									<button 
										type="button"
										onclick="removeImage()"
										class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-red-600 hover:bg-red-700 text-white flex items-center justify-center text-xs"
									>
										<i class="fas fa-times"></i>
									</button>
								</div>

								<div class="flex items-end gap-2">
									<input type="file" name="image" id="postImageInput" accept="image/*" class="hidden">
									<button 
										type="button"
										onclick="document.getElementById('postImageInput').click()"
										class="flex-shrink-0 w-10 h-10 rounded-full bg-purple-600/30 hover:bg-purple-600/50 border border-purple-500/50 text-purple-300 transition flex items-center justify-center"
										title="Adjuntar imagen"
									>
										<i class="fas fa-image"></i>
									</button>
									
									<textarea 
										name="content" 
										required 
										rows="1"
										id="messageInput"
										class="flex-1 bg-slate-800 border border-purple-500/50 rounded-2xl px-4 py-2 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 resize-none max-h-32"
										placeholder="Escribe un mensaje..."
										onkeydown="handleKeyPress(event)"
									></textarea>
									
									<button 
										type="submit" 
										class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white transition flex items-center justify-center"
										title="Enviar mensaje"
									>
										<i class="fas fa-paper-plane"></i>
									</button>
								</div>

								@error('content')
									<p class="text-red-400 text-xs mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
								@enderror
								@error('image')
									<p class="text-red-400 text-xs mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
								@enderror
							</form>
						</div>
					@else
						<div class="flex items-center justify-center h-full">
							<div class="text-center py-8">
								<i class="fas fa-lock text-4xl text-yellow-400 mb-4"></i>
								<p class="text-yellow-300 font-semibold mb-2">Debes ser miembro para ver el contenido del grupo</p>
								<p class="text-yellow-400 text-sm">Acepta la invitación para acceder al chat del grupo.</p>
							</div>
						</div>
					@endif
				</div>
			</div>

			<!-- Columna derecha: Invitar usuario y Miembros -->
			<div class="w-full lg:w-80 flex-shrink-0 space-y-4">
				<!-- Invitar Usuario (Solo para owner/moderators) -->
				@if($isOwner || auth()->user()->groups()->where('groups.id', $group->id)->where('member_role', 'moderator')->exists())
					<div class="p-4 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm relative z-10">
						<h3 class="font-bold text-lg text-purple-200 mb-3">
							<i class="fas fa-user-plus"></i> Invitar Usuario
						</h3>
						<form method="POST" action="{{ route('group-invitations.store', $group) }}">
							@csrf
							<div class="relative z-20 mb-2">
								<input 
									id="inviteUserInput"
									type="text" 
									name="username" 
									autocomplete="off"
									required 
									class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-3 py-2 text-white text-sm placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
									placeholder="Escribe para buscar..."
								>
								<!-- Dropdown de resultados -->
								<div id="inviteUserResults" class="hidden absolute z-[9999] w-full mt-2 bg-slate-800 border border-purple-500/50 rounded-lg max-h-60 overflow-y-auto shadow-2xl">
									<!-- Los resultados se insertarán aquí con JavaScript -->
								</div>
							</div>
							<button type="submit" class="w-full px-3 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold text-white transition text-sm">
								<i class="fas fa-paper-plane"></i> Enviar Invitación
							</button>
							@error('username')
								<p class="text-red-400 text-xs mt-2"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
							@enderror
						</form>
					</div>
				@endif

				<!-- Miembros (Solo para miembros) -->
				@if($isMember || $isOwner)
					<div class="p-4 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm relative z-0">
						<h3 class="font-bold text-lg text-purple-200 mb-3">
							<i class="fas fa-users"></i> Miembros
						</h3>
						<div class="space-y-2 max-h-[600px] overflow-y-auto">
							@foreach($group->members as $member)
								<div class="flex items-center gap-2 p-2 rounded-lg bg-slate-900/50 border border-purple-500/30">
									<a href="{{ route('users.show', $member) }}" class="flex items-center gap-2 flex-1 min-w-0 hover:opacity-80 transition">
										<div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold text-sm border border-purple-400 overflow-hidden flex-shrink-0">
											@if($member->avatar)
												<img src="{{ asset('storage/' . $member->avatar) }}" class="w-full h-full object-cover" alt="{{ $member->name }}">
											@else
												{{ strtoupper(substr($member->name, 0, 1)) }}
											@endif
										</div>
										<div class="flex-1 min-w-0">
											<div class="font-semibold text-purple-200 text-sm truncate">{{ $member->name }}</div>
											<div class="text-xs text-purple-400">
												@if($member->pivot->member_role === 'owner')
													<i class="fas fa-crown"></i> Propietario
												@elseif($member->pivot->member_role === 'moderator')
													<i class="fas fa-user-shield"></i> Moderador
												@else
													<i class="fas fa-user"></i> Miembro
												@endif
											</div>
										</div>
									</a>

									@if($isOwner && $member->id !== $group->owner_id)
										<div class="flex items-center gap-1">
											<!-- Cambiar rol -->
											<form method="POST" action="{{ route('groups.changeMemberRole', [$group, $member]) }}" class="inline">
												@csrf
												@if($member->pivot->member_role === 'moderator')
													<input type="hidden" name="role" value="member">
													<button type="submit" class="p-2 rounded bg-yellow-600/30 hover:bg-yellow-600/50 border border-yellow-500/50 text-yellow-300 transition text-xs" title="Quitar moderador">
														<i class="fas fa-user-slash"></i>
													</button>
												@else
													<input type="hidden" name="role" value="moderator">
													<button type="submit" class="p-2 rounded bg-blue-600/30 hover:bg-blue-600/50 border border-blue-500/50 text-blue-300 transition text-xs" title="Hacer moderador">
														<i class="fas fa-user-shield"></i>
													</button>
												@endif
											</form>

											<!-- Expulsar miembro -->
											<button 
												type="button"
												onclick="openKickMemberModal({{ $group->id }}, {{ $member->id }}, '{{ $member->name }}')"
												class="p-2 rounded bg-red-600/30 hover:bg-red-600/50 border border-red-500/50 text-red-300 transition text-xs"
												title="Expulsar del grupo"
											>
												<i class="fas fa-user-times"></i>
											</button>
										</div>
									@elseif($isModerator && $member->id !== auth()->id() && $member->pivot->member_role === 'member')
										<!-- Los moderadores solo pueden expulsar miembros normales -->
										<button 
											type="button"
											onclick="openKickMemberModal({{ $group->id }}, {{ $member->id }}, '{{ $member->name }}')"
											class="p-2 rounded bg-red-600/30 hover:bg-red-600/50 border border-red-500/50 text-red-300 transition text-xs"
											title="Expulsar del grupo"
										>
											<i class="fas fa-user-times"></i>
										</button>
									@endif
								</div>
							@endforeach
						</div>
					</div>
				@endif
			</div>
		</div>
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

		// Preview de imagen para chat
		const postImageInput = document.getElementById('postImageInput');
		if(postImageInput) {
			postImageInput.addEventListener('change', function(e) {
				if (this.files && this.files[0]) {
					const reader = new FileReader();
					reader.onload = function(e) {
						document.getElementById('previewImg').src = e.target.result;
						document.getElementById('imagePreview').classList.remove('hidden');
					};
					reader.readAsDataURL(this.files[0]);
				}
			});
		}

		function removeImage() {
			document.getElementById('postImageInput').value = '';
			document.getElementById('imagePreview').classList.add('hidden');
			document.getElementById('previewImg').src = '';
		}

		// Enviar con Enter (sin Shift)
		function handleKeyPress(event) {
			if (event.key === 'Enter' && !event.shiftKey) {
				event.preventDefault();
				document.getElementById('chatForm').submit();
			}
		}

		// Auto-scroll al final del chat
		const chatMessages = document.getElementById('chatMessages');
		if(chatMessages) {
			chatMessages.scrollTop = chatMessages.scrollHeight;
		}

		// Autocompletado para invitar usuario a grupo
		let inviteUserTimeout;
		const inviteUserInput = document.getElementById('inviteUserInput');
		const inviteUserResults = document.getElementById('inviteUserResults');

		if(inviteUserInput && inviteUserResults) {
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
				closeKickMemberModal();
				closeDeleteMessageModal();
			}
		});

		// Modal de expulsar miembro
		let kickMemberId = null;
		let kickGroupId = null;

		function openKickMemberModal(groupId, userId, userName) {
			kickGroupId = groupId;
			kickMemberId = userId;
			document.getElementById('kickMemberName').textContent = userName;
			document.getElementById('kickMemberForm').action = `/groups/${groupId}/members/${userId}/kick`;
			document.getElementById('kickMemberModal').classList.remove('hidden');
			document.body.style.overflow = 'hidden';
		}

		function closeKickMemberModal() {
			document.getElementById('kickMemberModal').classList.add('hidden');
			document.body.style.overflow = 'auto';
			kickMemberId = null;
			kickGroupId = null;
		}

		function confirmKickMember() {
			if(kickMemberId && kickGroupId) {
				document.getElementById('kickMemberForm').submit();
			}
		}

		// Modal de eliminar mensaje
		let deleteMessageId = null;

		function openDeleteMessageModal(postId) {
			deleteMessageId = postId;
			document.getElementById('deleteMessageForm').action = `/posts/${postId}`;
			document.getElementById('deleteMessageModal').classList.remove('hidden');
			document.body.style.overflow = 'hidden';
		}

		function closeDeleteMessageModal() {
			document.getElementById('deleteMessageModal').classList.add('hidden');
			document.body.style.overflow = 'auto';
			deleteMessageId = null;
		}

		function confirmDeleteMessage() {
			if(deleteMessageId) {
				document.getElementById('deleteMessageForm').submit();
			}
		}
	</script>

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

	<!-- Modal de Expulsar Miembro -->
	<div id="kickMemberModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
		<div class="bg-slate-900 rounded-2xl w-full max-w-md border-2 border-red-500 glow">
			<div class="p-6 border-b border-red-500/30">
				<div class="flex items-center gap-3">
					<div class="w-12 h-12 rounded-full bg-red-600/20 border border-red-500 flex items-center justify-center">
						<i class="fas fa-user-times text-red-500 text-2xl"></i>
					</div>
					<h3 class="text-xl font-bold text-red-400">
						Expulsar Miembro
					</h3>
				</div>
			</div>
			
			<div class="p-6">
				<p class="text-purple-200 mb-4">
					¿Estás seguro de que quieres expulsar a <span id="kickMemberName" class="font-bold"></span> del grupo?
				</p>
				<p class="text-sm text-purple-400 mb-6">
					El usuario será eliminado del grupo y no podrá ver ni participar en el chat.
				</p>
				
				<form id="kickMemberForm" method="POST" action="">
					@csrf
					@method('DELETE')
				</form>
				
				<div class="flex gap-3">
					<button type="button" onclick="closeKickMemberModal()" class="flex-1 px-6 py-3 rounded-lg border border-purple-500 hover:bg-purple-500/20 transition font-semibold text-purple-300">
						<i class="fas fa-times"></i> Cancelar
					</button>
					<button type="button" onclick="confirmKickMember()" class="flex-1 px-6 py-3 rounded-lg bg-red-600 hover:bg-red-700 font-bold text-white transition">
						<i class="fas fa-user-times"></i> Expulsar
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal de Eliminar Mensaje -->
	<div id="deleteMessageModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
		<div class="bg-slate-900 rounded-2xl w-full max-w-md border-2 border-red-500 glow">
			<div class="p-6 border-b border-red-500/30">
				<div class="flex items-center gap-3">
					<div class="w-12 h-12 rounded-full bg-red-600/20 border border-red-500 flex items-center justify-center">
						<i class="fas fa-trash-alt text-red-500 text-2xl"></i>
					</div>
					<h3 class="text-xl font-bold text-red-400">
						Eliminar Mensaje
					</h3>
				</div>
			</div>
			
			<div class="p-6">
				<p class="text-purple-200 mb-4">
					¿Estás seguro de que quieres eliminar este mensaje?
				</p>
				<p class="text-sm text-purple-400 mb-6">
					Esta acción no se puede deshacer.
				</p>
				
				<form id="deleteMessageForm" method="POST" action="">
					@csrf
					@method('DELETE')
				</form>
				
				<div class="flex gap-3">
					<button type="button" onclick="closeDeleteMessageModal()" class="flex-1 px-6 py-3 rounded-lg border border-purple-500 hover:bg-purple-500/20 transition font-semibold text-purple-300">
						<i class="fas fa-times"></i> Cancelar
					</button>
					<button type="button" onclick="confirmDeleteMessage()" class="flex-1 px-6 py-3 rounded-lg bg-red-600 hover:bg-red-700 font-bold text-white transition">
						<i class="fas fa-trash-alt"></i> Eliminar
					</button>
				</div>
			</div>
		</div>
	</div>
</x-layouts.app>
