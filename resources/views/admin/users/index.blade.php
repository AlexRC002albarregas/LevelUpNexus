<x-layouts.app :title="'Administrar usuarios'">
	<div class="max-w-6xl mx-auto space-y-6">
		<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
			<div>
				<h1 class="text-3xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">
					Gestión de usuarios
				</h1>
				<p class="text-sm text-purple-200/80 mt-1">
					Habilita o deshabilita perfiles y revisa el estado de privacidad de cada jugador.
				</p>
			</div>
			<form method="GET" class="flex items-center gap-2 bg-slate-900/60 border border-purple-500/40 rounded-xl px-4 py-2">
				<i class="fas fa-search text-purple-300"></i>
				<input
					type="text"
					name="search"
					value="{{ $search }}"
					class="bg-transparent focus:outline-none text-sm text-purple-100 placeholder-purple-400/60"
					placeholder="Buscar por nombre o correo…"
				>
				@if($search)
					<a href="{{ route('admin.users.index') }}" class="text-xs text-purple-300 hover:text-purple-100 transition">
						Limpiar
					</a>
				@endif
			</form>
		</div>

		@include('admin.users.partials.table', ['users' => $users])
	</div>
</x-layouts.app>

