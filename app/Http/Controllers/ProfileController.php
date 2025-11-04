<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Gate;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProfileRequest $request)
    {
		$this->authorize('create', Profile::class);
		$profile = Profile::create(array_merge($request->validated(), ['user_id' => $request->user()->id]));
		return response()->json($profile, 201);
    }

    /**
     * Display the specified resource.
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
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $profile = auth()->user()->profile;
        return view('profiles.edit', compact('profile'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfileRequest $request, Profile $profile)
    {
		$this->authorize('update', $profile);
		$profile->update($request->validated());
		return response()->json($profile);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profile $profile)
    {
		$this->authorize('delete', $profile);
		$profile->delete();
		return response()->json(['message' => 'Perfil eliminado']);
    }
}
