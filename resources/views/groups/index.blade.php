<x-layouts.app :title="'Grupos - LevelUp Nexus'">
	<div class="max-w-7xl mx-auto">
		<div class="flex items-center justify-between mb-8">
			<h1 class="text-4xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">
				<i class="fas fa-users"></i> Grupos
			</h1>
			@auth
				<a href="{{ route('groups.create') }}" class="px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold text-white transition glow-sm">
					<i class="fas fa-plus"></i> Crear Grupo
				</a>
			@endauth
		</div>

		<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
			@forelse($groups as $group)
				<a href="{{ route('groups.show', $group) }}" class="p-6 rounded-xl bg-slate-800/50 border {{ in_array($group->id, $pendingInvitations ?? []) ? 'border-yellow-500/50 bg-yellow-500/5' : 'border-purple-500/30' }} backdrop-blur-sm card-hover relative">
					@if(in_array($group->id, $pendingInvitations ?? []))
						<div class="absolute top-2 right-2 bg-yellow-500 text-slate-900 text-xs font-bold px-2 py-1 rounded-full">
							<i class="fas fa-envelope"></i> Invitación
						</div>
					@endif
					<div class="w-20 h-20 mx-auto rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-black text-2xl border-2 border-purple-400 overflow-hidden mb-4">
						@if($group->avatar)
							<img src="{{ asset('storage/' . $group->avatar) }}" class="w-full h-full object-cover" alt="{{ $group->name }}">
						@else
							{{ strtoupper(substr($group->name, 0, 2)) }}
						@endif
					</div>
					<div class="font-bold text-lg text-purple-200 mb-2 text-center">{{ $group->name }}</div>
					@if(in_array($group->id, $pendingInvitations ?? []))
						<div class="mb-3 p-3 rounded-lg bg-yellow-500/20 border border-yellow-500/30">
							<p class="text-yellow-300 text-sm font-semibold text-center mb-2">
								<i class="fas fa-envelope"></i> Has sido invitado a este grupo
							</p>
							<p class="text-yellow-400 text-xs text-center">¿Deseas unirte?</p>
						</div>
					@endif
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
				<div class="col-span-full p-12 rounded-xl bg-slate-800/50 border border-purple-500/30 backdrop-blur-sm text-center">
					<i class="fas fa-users text-6xl text-purple-400 mb-4"></i>
					<p class="text-xl text-purple-300 mb-2">No hay grupos aún</p>
					<p class="text-purple-400 mb-6">¡Crea el primer grupo de la comunidad!</p>
					@auth
						<a href="{{ route('groups.create') }}" class="inline-block px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold text-white transition glow-sm">
							<i class="fas fa-plus"></i> Crear primer grupo
						</a>
					@endauth
				</div>
			@endforelse
		</div>

		<div class="mt-8">
			{{ $groups->links() }}
		</div>
	</div>
</x-layouts.app>
