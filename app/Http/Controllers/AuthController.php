<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Show registration form
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle login request (web)
     */
    public function login(Request $request)
    {
        // Si es una petición AJAX/API, devolver JSON
        if($request->expectsJson() || $request->ajax()) {
            $credentials = $request->validate([
                'email' => ['required','email'],
                'password' => ['required','string'],
            ]);

            $user = User::where('email', $credentials['email'])->first();
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json(['message' => 'Credenciales inválidas'], 422);
            }

            $token = $user->createToken('api')->plainTextToken;
            return response()->json(['user' => $user, 'token' => $token]);
        }

        // Para peticiones web normales, usar sesión
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();
            return redirect()->route('landing')->with('status', 'Has iniciado sesión');
        }

        return back()->withErrors(['email' => 'Credenciales inválidas'])->onlyInput('email');
    }

    /**
     * Handle registration request (web)
     */
    public function register(Request $request)
    {
        // Si es una petición AJAX/API, devolver JSON
        if($request->expectsJson() || $request->ajax()) {
            $validated = $request->validate([
                'name' => ['required','string','max:255'],
                'email' => ['required','email','unique:users,email'],
                'password' => ['required', Password::defaults()],
                'role' => ['nullable','in:player,group_member,admin'],
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'] ?? 'player',
            ]);

            $token = $user->createToken('api')->plainTextToken;
            return response()->json(['user' => $user, 'token' => $token], 201);
        }

        // Para peticiones web normales, usar sesión
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','unique:users,email'],
            'password' => ['required','string','min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'player',
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('landing')->with('status', 'Cuenta creada');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        // Si es una petición AJAX/API, eliminar token
        if($request->expectsJson() || $request->ajax()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Sesión cerrada']);
        }

        // Para peticiones web normales, cerrar sesión
        if (auth()->check()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('landing')->with('status', 'Sesión cerrada');
    }
}
