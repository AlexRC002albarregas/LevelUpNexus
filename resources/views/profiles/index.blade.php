<x-layouts.app :title="'Perfiles - LevelUp Nexus'">
	<h1 class="text-2xl font-bold mb-6">Perfiles</h1>
	<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
		@foreach($profiles as $p)
			<a href="{{ route('profiles.web.show',$p) }}" class="p-4 rounded-xl border bg-white hover:shadow">
				<div class="flex items-center gap-3">
					<img src="{{ $p->avatar ?? 'https://via.placeholder.com/64' }}" class="w-12 h-12 rounded-full object-cover" />
					<div>
						<div class="font-semibold">{{ $p->nick }}</div>
						<div class="text-slate-500 text-sm">{{ $p->platform }}</div>
					</div>
				</div>
				<div class="mt-3 text-sm text-slate-600 line-clamp-2">{{ $p->bio }}</div>
			</a>
		@endforeach
	</div>
	<div class="mt-6">{{ $profiles->links() }}</div>
</x-layouts.app>



