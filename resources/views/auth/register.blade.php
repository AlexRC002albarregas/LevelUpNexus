<x-layouts.app :title="'Crear cuenta - LevelUp Nexus'">
	<div class="max-w-md mx-auto">
		<div class="text-center mb-8">
			<h1 class="text-4xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent mb-2">
				<i class="fas fa-user-plus"></i> Únete a la comunidad
			</h1>
			<p class="text-purple-300">Crea tu cuenta y comienza tu aventura gamer</p>
		</div>
		<form method="POST" action="{{ route('auth.register.post') }}" class="space-y-5 p-8 rounded-2xl bg-slate-800/50 border border-purple-500 backdrop-blur-sm glow">
			@csrf
			<div>
				<label class="block text-sm mb-2 text-purple-300 font-semibold"><i class="fas fa-user"></i> Nombre</label>
				<input name="name" required class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50" value="{{ old('name') }}" placeholder="Tu nombre gamer">
			</div>
			<div>
				<label class="block text-sm mb-2 text-purple-300 font-semibold"><i class="fas fa-envelope"></i> Email</label>
				<input name="email" type="email" required class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50" value="{{ old('email') }}" placeholder="tu@email.com">
			</div>
			<div>
				<label class="block text-sm mb-2 text-purple-300 font-semibold"><i class="fas fa-lock"></i> Contraseña</label>
				<input name="password" type="password" required class="w-full bg-slate-900 border border-purple-500/50 rounded-lg px-4 py-3 text-white placeholder-purple-400/50 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50" placeholder="••••••••">
			</div>
			<button class="w-full px-6 py-3 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-bold glow transition">
				<i class="fas fa-rocket"></i> Crear cuenta
			</button>
			<div class="text-center text-purple-300">
				¿Ya tienes cuenta? <a href="{{ route('auth.login') }}" class="text-pink-400 hover:text-pink-300 font-semibold">Inicia sesión</a>
			</div>
		</form>
	</div>
</x-layouts.app>


