<x-layouts.app :title="($profile->nick).' - Perfil'">
	<div class="flex items-start gap-6">
		<img src="{{ $profile->avatar ?? 'https://via.placeholder.com/96' }}" class="w-24 h-24 rounded-full object-cover" />
		<div>
			<h1 class="text-2xl font-bold">{{ $profile->nick }}</h1>
			<div class="text-slate-600">{{ $profile->platform }}</div>
			<div class="mt-2">Horas jugadas: <span class="font-semibold">{{ $profile->hours_played }}</span></div>
		</div>
	</div>
	<div class="mt-6 grid md:grid-cols-2 gap-6">
		<div class="p-4 rounded-xl border bg-white">
			<h3 class="font-semibold mb-2">Juegos favoritos</h3>
			<ul class="list-disc list-inside text-slate-700">
				@foreach(($profile->favorite_games ?? []) as $g)
					<li>{{ $g }}</li>
				@endforeach
			</ul>
		</div>
		<div class="p-4 rounded-xl border bg-white">
			<h3 class="font-semibold mb-2">Logros</h3>
			<ul class="list-disc list-inside text-slate-700">
				@foreach(($profile->achievements ?? []) as $a)
					<li>{{ $a }}</li>
				@endforeach
			</ul>
		</div>
	</div>
	@if(auth()->check() && auth()->id() === $profile->user_id)
		<div class="mt-6">
			<a href="{{ route('profiles.edit') }}" class="px-4 py-2 rounded bg-indigo-600 text-white">Editar perfil</a>
		</div>
	@endif
</x-layouts.app>



