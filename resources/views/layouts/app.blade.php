<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'LevelUp Nexus' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
	<header class="border-b bg-white sticky top-0 z-40">
		<div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
			<a href="{{ route('landing') }}" class="font-bold text-xl">LevelUp <span class="text-indigo-600">Nexus</span></a>
			<nav class="flex items-center gap-4">
				<a href="{{ route('landing') }}" class="hover:text-indigo-600">Inicio</a>
				<a href="{{ route('profiles.web.index') }}" class="hover:text-indigo-600">Perfiles</a>
				<a href="{{ route('games.web.index') }}" class="hover:text-indigo-600">Juegos</a>
				<a href="{{ route('groups.index') }}" class="hover:text-indigo-600">Grupos</a>
				@auth
					<form method="POST" action="{{ route('auth.logout') }}">
						@csrf
						<button class="px-3 py-1 rounded bg-slate-100 hover:bg-slate-200">Salir</button>
					</form>
				@else
					<a href="{{ route('auth.login') }}" class="px-3 py-1 rounded bg-indigo-600 text-white hover:bg-indigo-700">Entrar</a>
					<a href="{{ route('auth.register') }}" class="px-3 py-1 rounded border border-indigo-600 text-indigo-600 hover:bg-indigo-50">Crear cuenta</a>
				@endauth
			</nav>
		</div>
	</header>

	<main class="max-w-7xl mx-auto px-4 py-8">
		@if (session('status'))
			<div class="mb-4 p-3 rounded bg-green-50 text-green-700">{{ session('status') }}</div>
		@endif
		@if ($errors->any())
			<div class="mb-4 p-3 rounded bg-red-50 text-red-700">
				<ul class="list-disc list-inside">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
		{{ $slot ?? '' }}
	</main>

	<footer class="border-t bg-white">
		<div class="max-w-7xl mx-auto px-4 py-6 text-sm text-slate-500">
			© {{ date('Y') }} LevelUp Nexus
		</div>
	</footer>
</body>
</html>


