<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupInvitation;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{
    /**
     * Lista los grupos a los que pertenece el usuario y sus invitaciones.
     */
    public function index()
    {
        // Obtener solo los grupos en los que el usuario es miembro
        $groups = Group::withCount('members')
            ->with('owner')
            ->whereHas('members', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->orderBy('name')
            ->paginate(12);
        
        // Obtener invitaciones pendientes del usuario actual
        $pendingInvitations = GroupInvitation::where('recipient_id', auth()->id())
            ->where('status', 'pending')
            ->with(['group.owner', 'sender'])
            ->get();
        
        return view('groups.index', compact('groups', 'pendingInvitations'));
    }

    /**
     * Muestra el formulario de creación de grupos.
     */
    public function create()
    {
        return view('groups.create');
    }

    /**
     * Crea un nuevo grupo y registra al usuario como propietario.
     */
    public function store(StoreGroupRequest $request)
    {
        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => auth()->id(),
        ]);

        // Manejar subida de avatar
        if($request->hasFile('avatar')){
            $path = $request->file('avatar')->store('groups', 'public');
            $group->avatar = $path;
            $group->save();
        }

        // El creador es automáticamente miembro con rol owner
        $group->members()->attach(auth()->id(), ['member_role' => 'owner']);

        return redirect()
            ->route('groups.show', $group)
            ->with('status', 'Grupo creado correctamente');
    }

    /**
     * Muestra el detalle de un grupo controlando accesos y roles.
     */
    public function show(Group $group)
    {
        $group->load(['owner', 'members', 'posts.user']);
        $isMember = $group->members->contains(auth()->id());
        $isOwner = $group->owner_id === auth()->id();
        
        // Obtener el rol del usuario actual en el grupo
        $currentUserRole = null;
        if($isMember) {
            $currentUserRole = $group->members()
                ->where('user_id', auth()->id())
                ->first()
                ->pivot
                ->member_role ?? null;
        }
        
        $isModerator = $currentUserRole === 'moderator';
        
        // Si no es miembro, solo puede ver información básica (para aceptar invitación)
        if(!$isMember && !$isOwner) {
            // Invitación recibida por el usuario actual
            $receivedInvitation = GroupInvitation::where('group_id', $group->id)
                ->where('recipient_id', auth()->id())
                ->where('status', 'pending')
                ->with('sender')
                ->first();
            
            // Si no tiene invitación pendiente, no puede ver el grupo
            if(!$receivedInvitation) {
                abort(403, 'Debes ser miembro de este grupo para ver su contenido');
            }
            
            $pendingInvitations = null; // No hay invitaciones pendientes para mostrar si no es owner
            
            return view('groups.show', compact('group', 'isMember', 'isOwner', 'isModerator', 'pendingInvitations', 'receivedInvitation'));
        }
        
        // Invitaciones pendientes (solo para owner)
        $pendingInvitations = null;
        if($isOwner){
            $pendingInvitations = GroupInvitation::where('group_id', $group->id)
                ->where('status', 'pending')
                ->with('recipient')
                ->get();
        }
        
        // Invitación recibida por el usuario actual
        $receivedInvitation = GroupInvitation::where('group_id', $group->id)
            ->where('recipient_id', auth()->id())
            ->where('status', 'pending')
            ->with('sender')
            ->first();

        return view('groups.show', compact('group', 'isMember', 'isOwner', 'isModerator', 'pendingInvitations', 'receivedInvitation'));
    }

    /**
     * Muestra el formulario de edición de un grupo.
     */
    public function edit(Group $group)
    {
        abort_unless(
            auth()->id() === $group->owner_id || auth()->user()->role === 'admin',
            403
        );
        
        return view('groups.edit', compact('group'));
    }

    /**
     * Actualiza la información y el avatar de un grupo.
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Manejar subida de avatar
        if($request->hasFile('avatar')){
            // Eliminar avatar anterior si existe
            if($group->avatar && Storage::disk('public')->exists($group->avatar)){
                Storage::disk('public')->delete($group->avatar);
            }
            // Guardar nuevo avatar
            $path = $request->file('avatar')->store('groups', 'public');
            $group->avatar = $path;
            $group->save();
        }

        return redirect()
            ->route('groups.show', $group)
            ->with('status', 'Grupo actualizado correctamente');
    }

    /**
     * Elimina un grupo tras validar permisos y limpiar recursos.
     */
    public function destroy(Group $group)
    {
        abort_unless(
            auth()->id() === $group->owner_id || auth()->user()->role === 'admin',
            403
        );
        
        // Eliminar avatar si existe
        if($group->avatar && Storage::disk('public')->exists($group->avatar)){
            Storage::disk('public')->delete($group->avatar);
        }
        
        $group->delete();

        return redirect()
            ->route('groups.index')
            ->with('status', 'Grupo eliminado correctamente');
    }

    /**
     * Permite unirse a un grupo aceptando invitaciones o solicitudes.
     */
    public function join(Request $request, Group $group)
    {
        // Verificar si ya es miembro
        if($group->members->contains(auth()->id())){
            return back()->withErrors(['error' => 'Ya eres miembro de este grupo']);
        }

        // Verificar si hay una invitación pendiente
        $invitation = GroupInvitation::where('group_id', $group->id)
            ->where('recipient_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if($invitation){
            $invitation->update(['status' => 'accepted']);
        }

        // Añadir como miembro
        $group->members()->attach(auth()->id(), ['member_role' => 'member']);

        return back()->with('status', 'Te has unido al grupo correctamente');
    }

    /**
     * Permite al usuario abandonar un grupo del que es miembro.
     */
    public function leave(Group $group)
    {
        if($group->owner_id === auth()->id()){
            return back()->withErrors(['error' => 'El dueño no puede abandonar el grupo']);
        }

        if(!$group->members->contains(auth()->id())){
            return back()->withErrors(['error' => 'No eres miembro de este grupo']);
        }

        $group->members()->detach(auth()->id());

        return back()->with('status', 'Has abandonado el grupo');
    }

    /**
     * Cambia el rol de un miembro entre moderador y miembro.
     */
    public function changeMemberRole(Request $request, Group $group, $userId)
    {
        // Solo el owner puede cambiar roles
        abort_unless($group->owner_id === auth()->id(), 403, 'Solo el propietario puede cambiar roles');

        // Verificar que el usuario es miembro del grupo
        $member = $group->members()->where('user_id', $userId)->first();
        abort_unless($member, 404, 'El usuario no es miembro de este grupo');

        // No se puede cambiar el rol del owner
        if($userId == $group->owner_id){
            return back()->withErrors(['error' => 'No puedes cambiar el rol del propietario']);
        }

        $request->validate([
            'role' => 'required|in:member,moderator'
        ]);

        // Actualizar el rol
        $group->members()->updateExistingPivot($userId, [
            'member_role' => $request->role
        ]);

        $roleName = $request->role === 'moderator' ? 'moderador' : 'miembro';
        return back()->with('status', "Rol actualizado a {$roleName} correctamente");
    }

    /**
     * Expulsa a un miembro del grupo respetando las jerarquías.
     */
    public function kickMember(Group $group, $userId)
    {
        // Verificar permisos: owner o moderator
        $currentUserRole = $group->members()
            ->where('user_id', auth()->id())
            ->first()
            ->pivot
            ->member_role ?? null;

        $isOwner = $group->owner_id === auth()->id();
        $isModerator = $currentUserRole === 'moderator';

        abort_unless($isOwner || $isModerator, 403, 'No tienes permisos para expulsar miembros');

        // No se puede expulsar al owner
        if($userId == $group->owner_id){
            return back()->withErrors(['error' => 'No puedes expulsar al propietario del grupo']);
        }

        // Un moderador no puede expulsar a otro moderador, solo el owner puede
        $targetUserRole = $group->members()
            ->where('user_id', $userId)
            ->first()
            ->pivot
            ->member_role ?? null;

        if($isModerator && !$isOwner && $targetUserRole === 'moderator'){
            return back()->withErrors(['error' => 'No puedes expulsar a otro moderador']);
        }

        // Verificar que el usuario es miembro
        abort_unless($group->members()->where('user_id', $userId)->exists(), 404, 'El usuario no es miembro de este grupo');

        // Expulsar al miembro
        $group->members()->detach($userId);

        return back()->with('status', 'Miembro expulsado del grupo');
    }
}
