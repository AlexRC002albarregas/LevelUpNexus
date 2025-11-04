<x-layouts.app :title="'LevelUp Nexus - Tu Hub Gamer'">
	<section class="grid lg:grid-cols-2 gap-12 items-center py-12">
		<div class="space-y-8">
			<h1 class="text-5xl md:text-6xl font-black leading-tight">
				Conecta, juega y <span class="bg-gradient-to-r from-purple-400 via-pink-500 to-red-500 bg-clip-text text-transparent">domina</span>
			</h1>
			<p class="text-xl text-purple-200">Crea tu perfil gamer, gestiona tus juegos, comparte publicaciones, añade amigos y únete a grupos épicos. Todo en una plataforma.</p>
			<div class="flex gap-4">
				<a href="{{ route('auth.register') }}" class="px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold glow transition">
					<i class="fas fa-rocket"></i> Empieza gratis
				</a>
				<a href="{{ route('auth.login') }}" class="px-6 py-3 rounded-lg border-2 border-purple-500 hover:bg-purple-500/20 font-bold transition">
					<i class="fas fa-sign-in-alt"></i> Entrar
				</a>
			</div>
		</div>
		<div class="relative">
			<div class="aspect-video rounded-2xl bg-gradient-to-br from-purple-900 via-pink-900 to-red-900 flex items-center justify-center text-4xl font-black glow border border-purple-500">
				<i class="fas fa-gamepad text-purple-300"></i>
			</div>
		</div>
	</section>

	<section class="mt-20 grid md:grid-cols-3 gap-6">
		<div class="p-6 rounded-xl bg-slate-800/50 border border-purple-500/30 card-hover backdrop-blur-sm">
			<div class="text-3xl mb-3 text-purple-400"><i class="fas fa-user-circle"></i></div>
			<h3 class="font-bold text-xl mb-2 text-purple-300">Perfiles</h3>
			<p class="text-purple-200/70">Nick, avatar, plataforma, juegos favoritos, horas y logros.</p>
		</div>
		<div class="p-6 rounded-xl bg-slate-800/50 border border-pink-500/30 card-hover backdrop-blur-sm">
			<div class="text-3xl mb-3 text-pink-400"><i class="fas fa-trophy"></i></div>
			<h3 class="font-bold text-xl mb-2 text-pink-300">Juegos</h3>
			<p class="text-pink-200/70">CRUD de tu biblioteca con favoritos y horas jugadas.</p>
		</div>
		<div class="p-6 rounded-xl bg-slate-800/50 border border-blue-500/30 card-hover backdrop-blur-sm">
			<div class="text-3xl mb-3 text-blue-400"><i class="fas fa-comments"></i></div>
			<h3 class="font-bold text-xl mb-2 text-blue-300">Social</h3>
			<p class="text-blue-200/70">Publicaciones, comentarios y reacciones con amigos.</p>
		</div>
	</section>

	<section class="mt-12 p-8 rounded-2xl bg-gradient-to-r from-purple-900/50 to-pink-900/50 border border-purple-500 glow backdrop-blur-sm">
		<div class="flex items-center gap-4 mb-3">
			<i class="fas fa-users text-4xl text-purple-300"></i>
			<h3 class="font-bold text-2xl text-purple-200">Grupos y clanes</h3>
		</div>
		<p class="text-purple-100">Crea grupos, envía invitaciones y gestiona miembros con roles. Domina junto a tu escuadrón.</p>
	</section>
</x-layouts.app>



