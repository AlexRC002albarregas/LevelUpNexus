<x-layouts.app :title="'Editar perfil'">
	<h1 class="text-2xl font-bold mb-6">Editar perfil</h1>
	<form method="POST" action="{{ url('/api/profiles/'.($profile->id ?? '')) }}" class="space-y-4 max-w-lg" onsubmit="event.preventDefault(); submitProfile(this);">
		@csrf
		@if($profile)
			@method('PUT')
		@endif
		<div>
			<label class="block text-sm mb-1">Nick</label>
			<input name="nick" value="{{ old('nick',$profile->nick ?? '') }}" class="w-full border rounded px-3 py-2">
		</div>
		<div>
			<label class="block text-sm mb-1">Avatar (URL)</label>
			<input name="avatar" value="{{ old('avatar',$profile->avatar ?? '') }}" class="w-full border rounded px-3 py-2">
		</div>
		<div>
			<label class="block text-sm mb-1">Plataforma</label>
			<select name="platform" class="w-full border rounded px-3 py-2">
				@foreach(['pc','xbox','playstation','switch','mobile','other'] as $opt)
					<option value="{{ $opt }}" @selected(($profile->platform ?? '')===$opt)>{{ ucfirst($opt) }}</option>
				@endforeach
			</select>
		</div>
		<div>
			<label class="block text-sm mb-1">Horas jugadas</label>
			<input name="hours_played" type="number" min="0" value="{{ old('hours_played',$profile->hours_played ?? 0) }}" class="w-full border rounded px-3 py-2">
		</div>
		<div>
			<label class="block text-sm mb-1">Bio</label>
			<textarea name="bio" class="w-full border rounded px-3 py-2" rows="4">{{ old('bio',$profile->bio ?? '') }}</textarea>
		</div>
		<button class="px-4 py-2 rounded bg-indigo-600 text-white">Guardar</button>
	</form>
	<script>
	async function submitProfile(form){
		const fd = new FormData(form);
		const res = await fetch(form.action,{method: form.querySelector('input[name="_method"]')?.value==='PUT'?'POST':'POST', body: fd});
		if(res.ok){
			location.href = '{{ route('profiles.web.index') }}';
		}else{
			alert('Error guardando el perfil');
		}
	}
	</script>
</x-layouts.app>



