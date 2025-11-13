<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Gate;

class ProfileController extends Controller
{
    /**
     * Lista perfiles paginados y admite respuestas JSON.
     */
    public function index()
    {
        // Si es una petición AJAX/API, devolver JSON
        if(request()->expectsJson() || request()->ajax()) {
            $profiles = Profile::with('user')->paginate(10);
            return response()->json($profiles);
        }

        // Para peticiones web normales, devolver vista
        $profiles = Profile::with('user')->paginate(12);
        return view('profiles.index', compact('profiles'));
    }

    /**
     * Prepararía el formulario de creación de perfiles (no implementado).
     */
    public function create()
    {
    }

    /**
     * Crea un nuevo perfil asociado al usuario autenticado.
     */
    public function store(StoreProfileRequest $request)
    {
		$this->authorize('create', Profile::class);
		$profile = Profile::create(array_merge($request->validated(), ['user_id' => $request->user()->id]));
		return response()->json($profile, 201);
    }

    /**
     * Muestra un perfil individual en web o API.
     */
    public function show(Profile $profile)
    {
        // Si es una petición AJAX/API, devolver JSON
        if(request()->expectsJson() || request()->ajax()) {
            return response()->json($profile->load('user'));
        }

        // Para peticiones web normales, devolver vista
        $profile->load('user');
        return view('profiles.show', compact('profile'));
    }

    /**
     * Muestra el formulario para editar el perfil propio.
     */
    public function edit()
    {
        $profile = auth()->user()->profile;
        return view('profiles.edit', compact('profile'));
    }

    /**
     * Actualiza los datos del perfil tras validar permisos.
     */
    public function update(UpdateProfileRequest $request, Profile $profile)
    {
		$this->authorize('update', $profile);
		$profile->update($request->validated());
		return response()->json($profile);
    }

    /**
     * Elimina un perfil concreto si el usuario está autorizado.
     */
    public function destroy(Profile $profile)
    {
		$this->authorize('delete', $profile);
		$profile->delete();
		return response()->json(['message' => 'Perfil eliminado']);
    }
}
