<x-layouts.app :title="'Panel Admin'">
	<h1 class="text-2xl font-bold mb-6">Panel de administrador</h1>
	<div class="grid md:grid-cols-2 gap-6">
		<div class="p-4 rounded-xl border bg-white">
			<h3 class="font-semibold mb-2">Usuarios</h3>
			<ul class="text-sm list-disc list-inside">
				@foreach($users as $u)
					<li>{{ $u->name }} ({{ $u->email }}) - {{ $u->role }}</li>
				@endforeach
			</ul>
			<div class="mt-3">{{ $users->links() }}</div>
		</div>
		<div class="p-4 rounded-xl border bg-white">
			<h3 class="font-semibold mb-2">Grupos</h3>
			<ul class="text-sm list-disc list-inside">
				@foreach($groups as $g)
					<li>{{ $g->name }}</li>
				@endforeach
			</ul>
			<div class="mt-3">{{ $groups->links() }}</div>
		</div>
		<div class="p-4 rounded-xl border bg-white">
			<h3 class="font-semibold mb-2">Publicaciones</h3>
			<ul class="text-sm list-disc list-inside">
				@foreach($posts as $p)
					<li>#{{ $p->id }} por {{ $p->user_id }}</li>
				@endforeach
			</ul>
			<div class="mt-3">{{ $posts->links() }}</div>
		</div>
		<div class="p-4 rounded-xl border bg-white">
			<h3 class="font-semibold mb-2">Juegos</h3>
			<ul class="text-sm list-disc list-inside">
				@foreach($games as $g)
					<li>{{ $g->title }} (user {{ $g->user_id }})</li>
				@endforeach
			</ul>
			<div class="mt-3">{{ $games->links() }}</div>
		</div>
	</div>
</x-layouts.app>



