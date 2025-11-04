<x-layouts.app :title="'Crear Publicación - LevelUp Nexus'">
	<div class="max-w-3xl mx-auto">
		<h1 class="text-4xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent mb-8">
			<i class="fas fa-plus-circle"></i> Crear Nueva Publicación
		</h1>

		<form method="POST" action="{{ route('posts.store') }}" class="p-8 rounded-2xl bg-slate-800/50 border border-purple-500 backdrop-blur-sm glow">
			@csrf
			
			<div class="mb-6">
				<label class="block text-sm mb-2 text-purple-300 font-semibold">
					<i class="fas fa-edit"></i> Contenido de la publicación
				</label>
				<textarea 
					name="content" 
					required 
					rows="8"
					class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 resize-none"
					placeholder="¿Qué quieres compartir con la comunidad? (máx. 5000 caracteres)"
				>{{ old('content') }}</textarea>
				@error('content')
					<p class="text-red-400 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
				@enderror
			</div>

			<div class="mb-6">
				<label class="block text-sm mb-2 text-purple-300 font-semibold">
					<i class="fas fa-users"></i> Publicar en grupo (opcional)
				</label>
				<select 
					name="group_id" 
					class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
				>
					<option value="">Publicación personal</option>
					@foreach($groups as $group)
						<option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
							{{ $group->name }}
						</option>
					@endforeach
				</select>
				@error('group_id')
					<p class="text-red-400 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
				@enderror
			</div>

			<div class="mb-6">
				<label class="block text-sm mb-2 text-purple-300 font-semibold">
					<i class="fas fa-eye"></i> Visibilidad
				</label>
				<select 
					name="visibility" 
					class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
				>
					<option value="public" {{ old('visibility', 'public') == 'public' ? 'selected' : '' }}>Pública</option>
					<option value="private" {{ old('visibility') == 'private' ? 'selected' : '' }}>Privada (solo amigos)</option>
				</select>
			</div>

			<div class="flex gap-4">
				<button type="submit" class="flex-1 px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold text-white transition glow-sm">
					<i class="fas fa-paper-plane"></i> Publicar
				</button>
				<a href="{{ route('posts.index') }}" class="px-6 py-3 rounded-lg border border-purple-500 hover:bg-purple-500/20 transition font-semibold text-purple-300">
					<i class="fas fa-times"></i> Cancelar
				</a>
			</div>
		</form>
	</div>
</x-layouts.app>

