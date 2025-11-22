<?php $errors = $errors ?? new \Illuminate\Support\ViewErrorBag(); ?>
<x-layouts.app :title="'Publicación no disponible - LevelUp Nexus'">
	<div class="max-w-3xl mx-auto text-center py-20">
		<div class="w-24 h-24 mx-auto rounded-3xl bg-blue-500/20 border border-blue-400/40 flex items-center justify-center text-blue-200 text-4xl mb-6 glow">
			<i class="fas fa-newspaper"></i>
		</div>
		<p class="uppercase tracking-[0.5em] text-sm text-blue-200/80 mb-2">Ups, publicación no encontrada</p>
		<h1 class="text-4l md:text-5xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent mb-4">
			Esta publicación fue eliminada o es privada
		</h1>
		<p class="text-purple-200/80 max-w-2xl mx-auto mb-10">
			Puede que el autor cambiara la visibilidad, la haya borrado o nunca existiera. 
			Regresa al feed para seguir explorando contenido fresco de la comunidad.
		</p>
		<div class="flex flex-col sm:flex-row items-center justify-center gap-4">
			<a href="{{ route('posts.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl border border-purple-500/40 text-purple-100 hover:bg-purple-500/10 transition">
				<i class="fas a-newspaper"></i> Volver al feed
			</a>
			<a href="{{ route('games.web.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-semibold text-white transition glow">
				<i class="fas fa-gamepad"></i> Ir a mis juegos
			</a>
		</div>
	</div>
</x-layouts.app>

