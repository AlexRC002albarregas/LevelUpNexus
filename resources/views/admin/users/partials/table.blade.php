<div class="overflow-x-auto rounded-2xl border border-purple-500/30 bg-slate-900/60 backdrop-blur-sm shadow-lg shadow-purple-900/20">
	<table class="min-w-full divide-y divide-purple-500/20 text-sm">
		<thead class="bg-slate-900/70">
			<tr class="text-left text-xs font-semibold uppercase tracking-wider text-purple-300">
				<th class="px-6 py-3">Usuario</th>
				<th class="px-6 py-3">Correo electrónico</th>
				<th class="px-6 py-3 text-center">Privado</th>
				<th class="px-6 py-3 text-center">Estado</th>
				<th class="px-6 py-3 text-center">Acciones</th>
			</tr>
		</thead>
		<tbody class="divide-y divide-purple-500/10 text-purple-100">
			@forelse ($users as $user)
				<tr class="hover:bg-slate-900/40 transition">
					<td class="px-6 py-4">
						<div class="flex items-center gap-3">
							<div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 border border-purple-400/60 overflow-hidden flex items-center justify-center font-semibold">
								@if($user->avatar)
									<img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
								@else
									{{ strtoupper(substr($user->name, 0, 1)) }}
								@endif
							</div>
							<div>
								<div class="font-semibold">{{ $user->name }}</div>
								<div class="text-xs text-purple-300/80 flex items-center gap-2">
									<span>ID #{{ $user->id }}</span>
									@if($user->isAdmin())
										<span class="px-2 py-0.5 rounded-full bg-purple-600/40 border border-purple-400/60 text-[10px] uppercase tracking-wider">Admin</span>
									@endif
								</div>
							</div>
						</div>
					</td>
					<td class="px-6 py-4">
						<div class="text-sm break-all">{{ $user->email }}</div>
						<div class="text-xs text-purple-400">Registrado {{ $user->created_at->diffForHumans() }}</div>
					</td>
					<td class="px-6 py-4 text-center">
						<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full border {{ $user->is_private ? 'border-yellow-500/50 bg-yellow-500/10 text-yellow-200' : 'border-emerald-500/50 bg-emerald-500/10 text-emerald-200' }}">
							<i class="fas {{ $user->is_private ? 'fa-lock' : 'fa-unlock' }}"></i>
							{{ $user->is_private ? 'Privado' : 'Público' }}
						</span>
					</td>
					<td class="px-6 py-4 text-center">
						<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full border {{ $user->is_active ? 'border-emerald-500/50 bg-emerald-500/10 text-emerald-200' : 'border-red-500/50 bg-red-500/10 text-red-200' }}">
							<i class="fas {{ $user->is_active ? 'fa-check-circle' : 'fa-ban' }}"></i>
							{{ $user->is_active ? 'Activado' : 'Desactivado' }}
						</span>
					</td>
					<td class="px-6 py-4 text-center">
						<form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="inline-flex items-center justify-center">
							@csrf
							@method('PATCH')
							<label class="relative inline-flex cursor-pointer items-center">
								<input 
									type="checkbox" 
									class="sr-only peer" 
									onchange="this.form.submit()" 
									{{ $user->is_active ? 'checked' : '' }}
									{{ $user->id === auth()->id() ? 'disabled' : '' }}
								>
								<div class="w-14 h-7 rounded-full border border-purple-500/40 transition-all duration-300 
									{{ $user->id === auth()->id() ? 'opacity-40 cursor-not-allowed' : 'peer-checked:bg-emerald-500/60 bg-red-500/40' }}">
									<span class="absolute top-1 left-1 h-5 w-5 rounded-full bg-white shadow transition-all duration-300 
										peer-checked:translate-x-7"></span>
								</div>
							</label>
						</form>
					</td>
				</tr>
			@empty
				<tr>
					<td colspan="5" class="px-6 py-10 text-center text-purple-300">
						<div class="max-w-md mx-auto">
							<i class="fas fa-users-slash text-4xl mb-4"></i>
							<p>No se encontraron usuarios.</p>
						</div>
					</td>
				</tr>
			@endforelse
		</tbody>
	</table>
</div>

<div class="mt-4">
	{{ $users->links() }}
</div>

