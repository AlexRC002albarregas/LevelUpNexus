<?php

namespace App\Http\Controllers;

use App\Models\GroupInvitation;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class GroupInvitationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Group $group)
    {
        $request->validate([
            'username' => ['required', 'string'],
        ]);

        // Verificar que el usuario puede invitar (owner o admin del grupo)
        $isOwner = $group->owner_id === auth()->id();
        $isGroupAdmin = $group->members()
            ->where('user_id', auth()->id())
            ->where('member_role', 'admin')
            ->exists();

        if(!$isOwner && !$isGroupAdmin && auth()->user()->role !== 'admin'){
            abort(403, 'No tienes permiso para invitar usuarios a este grupo');
        }

        // Buscar usuario por nombre o email
        $recipient = User::where('name', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if(!$recipient || $recipient->id === auth()->id()){
            return back()->withErrors(['username' => 'Usuario no encontrado']);
        }

        // Verificar que no es miembro
        if($group->members->contains($recipient->id)){
            return back()->withErrors(['username' => 'Este usuario ya es miembro del grupo']);
        }

        // Verificar que no hay una invitación pendiente
        $existingInvitation = GroupInvitation::where('group_id', $group->id)
            ->where('recipient_id', $recipient->id)
            ->where('status', 'pending')
            ->exists();

        if($existingInvitation){
            return back()->withErrors(['username' => 'Ya existe una invitación pendiente para este usuario']);
        }

        // Crear invitación
        GroupInvitation::create([
            'group_id' => $group->id,
            'sender_id' => auth()->id(),
            'recipient_id' => $recipient->id,
            'status' => 'pending',
        ]);

        return back()->with('status', 'Invitación enviada a ' . $recipient->name);
    }

    /**
     * Accept invitation
     */
    public function accept(GroupInvitation $groupInvitation)
    {
        abort_unless(
            $groupInvitation->recipient_id === auth()->id(),
            403
        );

        abort_unless(
            $groupInvitation->status === 'pending',
            400,
            'Esta invitación ya fue procesada'
        );

        // Verificar que no es ya miembro
        if($groupInvitation->group->members->contains(auth()->id())){
            // Si ya es miembro, simplemente eliminar la invitación pendiente
            $groupInvitation->delete();
            return redirect()
                ->route('groups.show', $groupInvitation->group)
                ->with('status', 'Ya eres miembro de este grupo');
        }

        // Eliminar cualquier invitación anterior (accepted, declined, etc) para evitar conflicto con unique
        GroupInvitation::where('group_id', $groupInvitation->group_id)
            ->where('recipient_id', auth()->id())
            ->where('id', '!=', $groupInvitation->id)
            ->delete();

        // Marcar como aceptada
        $groupInvitation->update(['status' => 'accepted']);

        // Añadir como miembro del grupo
        $groupInvitation->group->members()->attach(auth()->id(), ['member_role' => 'member']);

        return redirect()
            ->route('groups.show', $groupInvitation->group)
            ->with('status', 'Has aceptado la invitación al grupo');
    }

    /**
     * Decline invitation
     */
    public function decline(GroupInvitation $groupInvitation)
    {
        abort_unless(
            $groupInvitation->recipient_id === auth()->id(),
            403
        );

        $groupInvitation->update(['status' => 'declined']);

        return back()->with('status', 'Invitación rechazada');
    }

    /**
     * Cancel invitation (by sender)
     */
    public function cancel(GroupInvitation $groupInvitation)
    {
        abort_unless(
            $groupInvitation->sender_id === auth()->id() || 
            $groupInvitation->group->owner_id === auth()->id() ||
            auth()->user()->role === 'admin',
            403
        );

        abort_unless(
            $groupInvitation->status === 'pending',
            400,
            'Esta invitación ya fue procesada'
        );

        $groupInvitation->update(['status' => 'cancelled']);

        return back()->with('status', 'Invitación cancelada');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GroupInvitation $groupInvitation)
    {
        abort_unless(
            $groupInvitation->sender_id === auth()->id() || 
            $groupInvitation->group->owner_id === auth()->id() ||
            auth()->user()->role === 'admin',
            403
        );

        $groupInvitation->delete();

        return back()->with('status', 'Invitación eliminada');
    }
}
