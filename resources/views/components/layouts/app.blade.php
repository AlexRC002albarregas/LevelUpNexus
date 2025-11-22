<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{ $title ?? 'LevelUp Nexus' }}</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<style>
		body { background: linear-gradient(135deg, #0f0c29, #302b63, #24243e); }
		.glow { box-shadow: 0 0 20px rgba(139, 92, 246, 0.5); }
		.glow-sm { box-shadow: 0 0 10px rgba(139, 92, 246, 0.3); }
		.card-hover { transition: all 0.3s ease; }
		.card-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(139, 92, 246, 0.4); }
		[x-cloak] { display: none !important; }
		@keyframes pulse-glow {
			0%, 100% { box-shadow: 0 0 10px rgba(236, 72, 153, 0.5); }
			50% { box-shadow: 0 0 20px rgba(236, 72, 153, 1); }
		}
		.notification-badge { animation: pulse-glow 2s ease-in-out infinite; }
	</style>
</head>
<body class="min-h-screen text-white flex flex-col overflow-x-hidden">
	<header class="backdrop-blur-lg bg-slate-900/80 border-b border-purple-500/30 sticky top-0 z-[100]">
		<div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
			<a href="{{ route('landing') }}" class="flex items-center gap-3 group">
				<x-logo class="h-12 w-auto transition group-hover:scale-105" />
				<span class="text-2xl font-black bg-gradient-to-r from-purple-400 to-pink-600 bg-clip-text text-transparent">
					LevelUp Nexus
				</span>
			</a>
			<nav class="flex items-center gap-6">
				<a href="{{ route('landing') }}" class="hover:text-purple-400 transition"><i class="fas fa-home"></i> Inicio</a>
				@auth
					<a href="{{ route('posts.index') }}" class="hover:text-purple-400 transition"><i class="fas fa-newspaper"></i> Publicaciones</a>
					<a href="{{ route('friends.index') }}" class="hover:text-purple-400 transition relative">
						<i class="fas fa-user-friends"></i> Amigos
						@php
							$notifCount = auth()->user()->notificationsCount();
						@endphp
						@if($notifCount > 0)
							<span class="absolute -top-2 -right-2 bg-pink-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center notification-badge">{{ $notifCount }}</span>
						@endif
					</a>
					<a href="{{ route('games.web.index') }}" class="hover:text-purple-400 transition"><i class="fas fa-gamepad"></i> Mis Juegos</a>
					<a href="{{ route('groups.index') }}" class="hover:text-purple-400 transition relative">
						<i class="fas fa-users"></i> Grupos
						@php
							$groupInvitationsCount = auth()->user()->pendingGroupInvitationsCount();
						@endphp
						@if($groupInvitationsCount > 0)
							<span class="absolute -top-2 -right-2 bg-pink-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center notification-badge">{{ $groupInvitationsCount }}</span>
						@endif
					</a>
					
					<!-- Menú de usuario -->
					<div class="flex items-center gap-3 ml-4 pl-4 border-l border-purple-500/30">
						<a href="{{ route('account.edit') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-purple-500/20 transition group">
							<div class="w-9 h-9 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold border-2 border-purple-400 glow-sm overflow-hidden">
								@if(auth()->user()->avatar)
									<img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="w-full h-full object-cover" alt="{{ auth()->user()->name }}">
								@else
									{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
								@endif
							</div>
							<span class="font-semibold group-hover:text-purple-300">{{ auth()->user()->name }}</span>
						</a>
						@if(auth()->user()->isAdmin())
							<div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
								<button 
									type="button" 
									class="px-3 py-2 rounded-lg bg-slate-900/70 border border-purple-500/40 hover:bg-purple-500/30 transition flex items-center gap-2"
									@click="open = !open"
									aria-haspopup="true"
									:aria-expanded="open.toString()"
								>
									<i class="fas fa-bars"></i>
								</button>
								<div 
									class="absolute right-0 mt-2 w-48 rounded-xl border border-purple-500/40 bg-slate-900/90 backdrop-blur-lg shadow-xl shadow-purple-900/30 overflow-hidden z-50"
									x-cloak
									x-show="open"
									x-transition.origin.top.right
									@click.away="open = false"
								>
									<a 
										href="{{ route('admin.users.index') }}" 
										class="flex items-center gap-2 px-4 py-3 text-sm text-purple-100 hover:bg-purple-500/20 transition"
										@click="open = false"
									>
										<i class="fas fa-sliders-h"></i> Panel de Control
									</a>
									<form method="POST" action="{{ route('auth.logout') }}">
										@csrf
										<button type="submit" class="w-full flex items-center gap-2 px-4 py-3 text-sm text-red-200 hover:bg-red-500/20 transition">
											<i class="fas fa-sign-out-alt"></i> Cerrar sesión
										</button>
									</form>
								</div>
							</div>
						@else
							<form method="POST" action="{{ route('auth.logout') }}">
								@csrf
								<button class="px-3 py-2 rounded-lg bg-red-600/20 border border-red-500 hover:bg-red-600/40 transition" title="Cerrar sesión">
									<i class="fas fa-sign-out-alt"></i>
								</button>
							</form>
						@endif
					</div>
				@else
					<a href="{{ route('auth.login') }}" class="px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 glow-sm transition"><i class="fas fa-sign-in-alt"></i> Entrar</a>
					<a href="{{ route('auth.register') }}" class="px-4 py-2 rounded-lg border-2 border-purple-500 hover:bg-purple-500/20 transition"><i class="fas fa-user-plus"></i> Registro</a>
				@endauth
			</nav>
		</div>
	</header>

    <main class="max-w-7xl mx-auto px-4 py-8 flex-1">
		@if (session('status'))
			<div class="mb-4 p-4 rounded-lg bg-green-500/20 border border-green-500 text-green-200 glow-sm" data-auto-dismiss><i class="fas fa-check-circle"></i> {{ session('status') }}</div>
		@endif
		@if ($errors->any())
			<div class="mb-4 p-4 rounded-lg bg-red-500/20 border border-red-500 text-red-200">
				<ul class="list-disc list-inside">
					@foreach ($errors->all() as $error)
						<li><i class="fas fa-exclamation-circle"></i> {{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
		{{ $slot }}
	</main>

    <footer class="border-t border-purple-500/40 bg-slate-900/70 backdrop-blur-lg mt-auto">
		<div class="max-w-7xl mx-auto px-6 py-10">
			<div class="grid gap-8 md:grid-cols-4 text-sm">
				<div>
					<a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-xl font-black bg-gradient-to-r from-purple-400 to-pink-500 bg-clip-text text-transparent">
						<i class="fas fa-gamepad"></i> LevelUp Nexus
					</a>
					<p class="mt-3 text-purple-300/80 leading-relaxed">
						Tu hub social para descubrir juegos, compartir logros y conectar con jugadores de todo el mundo.
					</p>
					<div class="flex gap-3 mt-5 text-purple-200/80">
						<a href="https://twitter.com" target="_blank" rel="noopener" class="w-9 h-9 flex items-center justify-center rounded-full bg-purple-600/20 border border-purple-500/40 hover:bg-purple-600/40 transition">
							<i class="fab fa-twitter"></i>
						</a>
						<a href="https://discord.com" target="_blank" rel="noopener" class="w-9 h-9 flex items-center justify-center rounded-full bg-purple-600/20 border border-purple-500/40 hover:bg-purple-600/40 transition">
							<i class="fab fa-discord"></i>
						</a>
						<a href="https://twitch.tv" target="_blank" rel="noopener" class="w-9 h-9 flex items-center justify-center rounded-full bg-purple-600/20 border border-purple-500/40 hover:bg-purple-600/40 transition">
							<i class="fab fa-twitch"></i>
						</a>
					</div>
				</div>
				<div>
					<h4 class="text-purple-200 font-semibold uppercase tracking-wide mb-4 text-xs">Explorar</h4>
					<ul class="space-y-2 text-purple-300/80">
						<li><a href="{{ route('posts.index') }}" class="hover:text-purple-200 transition"><i class="fas fa-newspaper mr-2 text-purple-400/80"></i>Publicaciones</a></li>
						<li><a href="{{ route('games.web.index') }}" class="hover:text-purple-200 transition"><i class="fas fa-gamepad mr-2 text-purple-400/80"></i>Mi Biblioteca</a></li>
						<li><a href="{{ route('groups.index') }}" class="hover:text-purple-200 transition"><i class="fas fa-users mr-2 text-purple-400/80"></i>Grupos</a></li>
						<li><a href="{{ route('friends.index') }}" class="hover:text-purple-200 transition"><i class="fas fa-user-friends mr-2 text-purple-400/80"></i>Amigos</a></li>
					</ul>
				</div>
				<div>
					<h4 class="text-purple-200 font-semibold uppercase tracking-wide mb-4 text-xs">Comunidad</h4>
					<ul class="space-y-2 text-purple-300/80">
						<li><span class="flex items-center gap-2"><i class="fas fa-trophy text-purple-400/80"></i>Torneos semanales</span></li>
						<li><span class="flex items-center gap-2"><i class="fas fa-microphone text-purple-400/80"></i>Podcast mensual</span></li>
						<li><span class="flex items-center gap-2"><i class="fas fa-heart text-purple-400/80"></i>Eventos benéficos</span></li>
						<li><span class="flex items-center gap-2"><i class="fas fa-star text-purple-400/80"></i>Programas VIP</span></li>
					</ul>
				</div>
				<div>
					<h4 class="text-purple-200 font-semibold uppercase tracking-wide mb-4 text-xs">Boletín</h4>
					<p class="text-purple-300/80 mb-3 leading-relaxed">Recibe actualizaciones sobre nuevos juegos, retos y funcionalidades antes que nadie.</p>
					<form class="space-y-3">
						<input type="email" placeholder="tu@email.com" class="w-full px-4 py-2.5 rounded-lg bg-slate-900/80 border border-purple-500/40 text-purple-100 placeholder-purple-400/50 focus:outline-none focus:ring-2 focus:ring-purple-500/60">
						<button type="button" class="w-full px-4 py-2.5 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 font-semibold text-white transition glow-sm">
							<i class="fas fa-paper-plane mr-2"></i>Quiero unirme
						</button>
					</form>
				</div>
			</div>
			<div class="mt-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4 text-xs text-purple-400/80 border-t border-purple-500/20 pt-6">
				<span><i class="fas fa-code"></i> {{ date('Y') }} LevelUp Nexus · Hecho para gamers por gamers</span>
				<div class="flex flex-wrap gap-4">
					<a href="#" class="hover:text-purple-200 transition">Términos</a>
					<a href="#" class="hover:text-purple-200 transition">Privacidad</a>
					<a href="#" class="hover:text-purple-200 transition">Soporte</a>
					<a href="mailto:hello@levelupnexus.com" class="hover:text-purple-200 transition"><i class="fas fa-envelope mr-1"></i>Contacto</a>
				</div>
			</div>
		</div>
	</footer>

	@guest
		<div
			id="cookies-banner"
			class="fixed inset-x-0 bottom-4 px-4 sm:px-0 z-[120] opacity-0 pointer-events-none translate-y-8 transition-all duration-500 ease-out"
		>
			<div class="mx-auto max-w-3xl rounded-2xl border border-purple-500/40 bg-slate-900/95 backdrop-blur-lg shadow-2xl shadow-purple-900/40 px-6 py-5 sm:py-6">
				<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
					<div class="sm:max-w-md">
						<div class="flex items-center gap-3 text-purple-200">
							<i class="fas fa-cookie-bite text-2xl text-pink-400"></i>
							<p class="text-sm sm:text-base leading-relaxed">
								Usamos cookies para mejorar tu experiencia. ¿Prefieres aceptarlas todas, rechazarlas o quedarte solo con las esenciales?
							</p>
						</div>
					</div>
					<div class="flex flex-wrap gap-2 justify-end sm:justify-start">
						<button
							type="button"
							class="js-cookies-close min-w-[150px] text-center px-4 py-2.5 rounded-xl border border-purple-500/40 text-purple-200 hover:bg-purple-500/20 transition text-sm font-medium"
						>
							Solo esenciales
						</button>
						<button
							type="button"
							class="js-cookies-close min-w-[150px] text-center px-4 py-2.5 rounded-xl border border-purple-500/40 text-purple-200 hover:bg-purple-500/20 transition text-sm font-medium"
						>
							Rechazar todas
						</button>
						<button
							type="button"
							class="js-cookies-close min-w-[150px] text-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white transition text-sm font-semibold shadow-lg shadow-purple-900/40"
						>
							Aceptar todas
						</button>
					</div>
				</div>
			</div>
		</div>
	@endguest

	<script>
		// Toast global reutilizable
		window.showToast = window.showToast || function(message, type = 'success') {
			if(!message) return;
			let container = document.getElementById('toastContainer');
			if(!container) {
				container = document.createElement('div');
				container.id = 'toastContainer';
				container.style.position = 'fixed';
				container.style.top = '1rem';
				container.style.right = '1rem';
				container.style.zIndex = '9999';
				container.style.display = 'flex';
				container.style.flexDirection = 'column';
				container.style.gap = '0.5rem';
				container.style.pointerEvents = 'none';
				document.body.appendChild(container);
			}
			const toast = document.createElement('div');
			toast.className = type === 'error'
				? 'px-4 py-3 rounded-lg bg-red-600/90 border border-red-400 text-white text-sm shadow-lg transition'
				: 'px-4 py-3 rounded-lg bg-purple-600/90 border border-purple-300 text-white text-sm shadow-lg transition';
			toast.textContent = message;
			toast.style.opacity = '1';
			container.appendChild(toast);
			setTimeout(() => {
				toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
				toast.style.opacity = '0';
				toast.style.transform = 'translateY(-10px)';
				setTimeout(() => toast.remove(), 400);
			}, 2500);
		};

		// Auto-ocultar notificaciones después de 4 segundos
		document.addEventListener('DOMContentLoaded', function() {
			const notifications = document.querySelectorAll('[data-auto-dismiss]');
			
			notifications.forEach(notification => {
				// Añadir animación de fade out
				setTimeout(() => {
					notification.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
					notification.style.opacity = '0';
					notification.style.transform = 'translateY(-20px)';
					
					// Eliminar del DOM después de la animación
					setTimeout(() => {
						notification.remove();
					}, 500);
				}, 4000); // 4 segundos
			});

			const banner = document.getElementById('cookies-banner');

			if (banner) {
				const cookieName = 'levelupnexus_cookies_banner';
				const cookieValue = document.cookie.split('; ').find(row => row.startsWith(`${cookieName}=`));

				if (cookieValue) {
					banner.remove();
					return;
				}

				const buttons = banner.querySelectorAll('.js-cookies-close');

				setTimeout(() => {
					banner.classList.remove('opacity-0', 'translate-y-8', 'pointer-events-none');
					banner.classList.add('opacity-100');
				}, 200);

				buttons.forEach(button => {
					button.addEventListener('click', () => {
						const expiryDate = new Date(Date.now() + 60 * 60 * 1000);
						document.cookie = `${cookieName}=acknowledged; expires=${expiryDate.toUTCString()}; path=/; SameSite=Lax`;

						banner.classList.remove('opacity-100');
						banner.classList.add('opacity-0', 'translate-y-8', 'pointer-events-none');
						setTimeout(() => banner.remove(), 400);
					});
				});
			}
		});
	</script>

	@stack('scripts')
</body>
</html>


