<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    /**
     * Display a paginated list of users with status information.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim();

        $usersQuery = User::query()
            ->when($search->isNotEmpty(), function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('name');

        $users = $usersQuery->paginate(12)->withQueryString();

        if ($request->ajax()) {
            return view('admin.users.partials.table', [
                'users' => $users,
            ])->render();
        }

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search->toString(),
        ]);
    }

    /**
     * Toggle user active status.
     */
    public function toggle(User $user): RedirectResponse
    {
        // Evitar que un administrador se desactive a sí mismo
        if ($user->id === auth()->id()) {
            return back()->with('status', 'No puedes desactivar tu propio perfil.');
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        return back()->with('status', "El perfil de {$user->name} ahora está " . ($user->is_active ? 'activado' : 'desactivado') . '.');
    }
}

