<?php $errors = $errors ?? new \Illuminate\Support\ViewErrorBag(); ?>
<x-layouts.app :title="'Juego no disponible - LevelUp Nexus'">
	<div class="max-w-3xl mx-auto text-center py-20">
		<div class="w-24 h-24 mx-auto rounded-3xl bg-emerald-500/20 border border-emerald-400/40 flex items-center justify-center text-emerald-200 text-4xl mb-6 glow">
			<i class="fas fa-gamepad"></i>
		</div>
		<p class="uppercase tracking-[0.5em] text-sm text-emerald-200/80 mb-2">Biblioteca en modo seguro</p>
		<h1 class="text-4xl md:text-5xl font-black bg-gradient-to-r from-green-400 to-cyan-500 bg-clip-text text-transparent mb-4">
			No pudimos cargar este juego
		</h1>
		<p class="text-purple-200/80 max-w-2xl mx-auto mb-10">
			Quizá fue eliminado de tu biblioteca o ya no está disponible en la API. 
			Intenta volver a añadirlo o revisa tus filtros para seguir jugando.
		</p>
		<div class="flex flex-col sm:flex-row items-center justify-center gap-4">
			<a href="{{ route('games.web.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl border border-purple-500/40 text-purple-100 hover:bg-purple-500/10 transition">
				<i class="fas fa-book"></i> Ver mi biblioteca
			</a>
			<a href="{{ route('games.web.create') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-semibold text-white transition glow">
				<i class="fas fa-plus"></i> Añadir un juego
			</a>
		</div>
	</div>
</x-layouts.app>

