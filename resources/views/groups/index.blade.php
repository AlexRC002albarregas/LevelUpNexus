<x-layouts.app :title="'Mis Grupos - LevelUp Nexus'">
	<div class="max-w-7xl mx-auto">
		<div class="flex items-center justify-between mb-8">
			<h1 class="text-4xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">
				<i class="fas fa-users"></i> Mis Grupos
			</h1>
			@auth
				<a href="{{ route('groups.create') }}" class="px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold text-white transition glow-sm">
					<i class="fas fa-plus"></i> Crear Grupo
				</a>
			@endauth
		</div>

		<!-- Invitaciones Pendientes -->
		@if($pendingInvitations->count() > 0)
			<section class="mb-8 p-6 rounded-2xl bg-yellow-500/10 border border-yellow-500 backdrop-blur-sm glow-sm">
				<h3 class="font-bold text-xl mb-4 text-yellow-300">
					<i class="fas fa-clock"></i> Invitaciones a grupos pendientes ({{ $pendingInvitations->count() }})
				</h3>
				<div class="space-y-3">
					@foreach($pendingInvitations as $invitation)
						<div class="flex items-center justify-between gap-6 p-4 rounded-xl bg-slate-800/50 border border-yellow-500/30 card-hover">
							<div class="flex items-center gap-3 flex-1 min-w-0">
								<div class="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold text-xl border-2 border-purple-400 overflow-hidden">
									@if($invitation->group->avatar)
										<img src="{{ asset('storage/' . $invitation->group->avatar) }}" class="w-full h-full object-cover" alt="{{ $invitation->group->name }}">
									@else
										{{ strtoupper(substr($invitation->group->name, 0, 2)) }}
									@endif
								</div>
								<div>
									<div class="font-bold text-purple-200">{{ $invitation->group->name }}</div>
									<div class="text-sm text-purple-400">
										<i class="fas fa-user"></i> Invitado por {{ $invitation->sender->name }}
									</div>
								</div>
							</div>
							<div class="flex gap-2 flex-shrink-0">
								<form method="POST" action="{{ route('group-invitations.accept', $invitation) }}">
									@csrf
									<button class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white font-bold transition glow-sm">
										<i class="fas fa-check"></i> Aceptar
									</button>
								</form>
								<form method="POST" action="{{ route('group-invitations.decline', $invitation) }}">
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

		<!-- Mis Grupos -->
		<div>
			@if($groups->count() > 0 || $pendingInvitations->count() > 0)
				<h2 class="text-2xl font-bold text-purple-200 mb-4">
					<i class="fas fa-layer-group"></i> Grupos en los que estás
				</h2>
			@endif
			
			<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
				@forelse($groups as $group)
					<a href="{{ route('groups.show', $group) }}" class="p-6 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm card-hover relative">
						<div class="w-24 h-24 mx-auto rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-black text-3xl border-2 border-purple-400 overflow-hidden mb-4">
							@if($group->avatar)
								<img src="{{ asset('storage/' . $group->avatar) }}" class="w-full h-full object-cover" alt="{{ $group->name }}">
							@else
								{{ strtoupper(substr($group->name, 0, 2)) }}
							@endif
						</div>
						<div class="font-bold text-xl text-purple-200 mb-3 text-center">{{ $group->name }}</div>
						<div class="text-purple-400 text-sm text-center mb-3">
							<i class="fas fa-users"></i> {{ $group->members_count }} miembro{{ $group->members_count !== 1 ? 's' : '' }}
						</div>
						@if($group->description)
							<div class="text-purple-300 text-sm line-clamp-2 text-center">{{ $group->description }}</div>
						@endif
						@if($group->owner)
							<div class="text-xs text-purple-400 mt-3 text-center">
								<i class="fas fa-crown"></i> {{ $group->owner->name }}
							</div>
						@endif
					</a>
				@empty
					@if($pendingInvitations->count() == 0)
						<div class="col-span-full p-12 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm text-center">
							<i class="fas fa-users-slash text-6xl text-purple-400 mb-4"></i>
							<p class="text-xl text-purple-300 mb-2">No perteneces a ningún grupo</p>
							<p class="text-purple-400 mb-6">¡Crea tu primer grupo o espera una invitación!</p>
							@auth
								<a href="{{ route('groups.create') }}" class="inline-block px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold text-white transition glow-sm">
									<i class="fas fa-plus"></i> Crear Grupo
								</a>
							@endauth
						</div>
					@endif
				@endforelse
			</div>

			@if($groups->count() > 0)
				<div class="mt-8">
					{{ $groups->links() }}
				</div>
			@endif
		</div>
	</div>
</x-layouts.app>
