<x-layouts.app :title="'Editar Grupo - LevelUp Nexus'">
	<div class="max-w-3xl mx-auto">
		<div class="mb-6">
			<a href="{{ route('groups.show', $group) }}" class="inline-flex items-center gap-2 text-purple-400 hover:text-purple-300 transition mb-4">
				<i class="fas fa-arrow-left"></i> Volver al grupo
			</a>
		</div>

		<h1 class="text-4xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent mb-8">
			<i class="fas fa-edit"></i> Editar Grupo
		</h1>

		<form method="POST" action="{{ route('groups.update', $group) }}" enctype="multipart/form-data" class="p-8 rounded-2xl bg-slate-800/50 border border-purple-500 backdrop-blur-sm glow">
			@csrf
			@method('PUT')
			
			<div class="mb-6">
				<label class="block text-sm mb-2 text-purple-300 font-semibold">
					<i class="fas fa-tag"></i> Nombre del grupo
				</label>
				<input 
					name="name" 
					value="{{ old('name', $group->name) }}" 
					required 
					class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
				>
				@error('name')
					<p class="text-red-400 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
				@enderror
			</div>

			<div class="mb-6">
				<label class="block text-sm mb-2 text-purple-300 font-semibold">
					<i class="fas fa-align-left"></i> Descripción
				</label>
				<textarea 
					name="description" 
					rows="5"
					class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 resize-none"
					placeholder="Describe tu grupo..."
				>{{ old('description', $group->description) }}</textarea>
				@error('description')
					<p class="text-red-400 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
				@enderror
			</div>

			<div class="mb-6">
				<label class="block text-sm mb-2 text-purple-300 font-semibold">
					<i class="fas fa-image"></i> Avatar del grupo
				</label>
				@if($group->avatar)
					<div class="mb-3">
						<img src="{{ asset('storage/' . $group->avatar) }}" alt="Avatar actual" class="w-24 h-24 rounded-lg object-cover border-2 border-purple-500">
					</div>
				@endif
				<input 
					name="avatar" 
					type="file" 
					accept="image/*" 
					class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
				>
				<p class="text-xs text-purple-400 mt-1">Opcional: JPG, PNG, GIF o WEBP (máx. 2MB)</p>
				@error('avatar')
					<p class="text-red-400 text-sm mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
				@enderror
			</div>

			<div class="flex gap-4">
				<button type="submit" class="flex-1 px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold text-white transition glow-sm">
					<i class="fas fa-save"></i> Guardar Cambios
				</button>
				<a href="{{ route('groups.show', $group) }}" class="px-6 py-3 rounded-lg border border-purple-500 hover:bg-purple-500/20 transition font-semibold text-purple-300">
					<i class="fas fa-times"></i> Cancelar
				</a>
			</div>
		</form>
	</div>
</x-layouts.app>

