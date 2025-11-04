<?php

namespace App\Http\Controllers;

use App\Models\Reaction;
use App\Models\Post;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    /**
     * Toggle reaction on a post (like/unlike)
     */
    public function toggle(Request $request, Post $post)
    {
        $type = $request->input('type', 'like');
        $allowedTypes = ['like', 'love', 'haha', 'wow', 'sad', 'angry'];
        
        if(!in_array($type, $allowedTypes)){
            return back()->withErrors(['type' => 'Tipo de reacción no válido']);
        }

        // Verificar que el usuario puede ver el post
        if(!auth()->user()->canViewProfile($post->user)){
            abort(403, 'No tienes permiso para reaccionar a esta publicación');
        }

        $existingReaction = Reaction::where('user_id', auth()->id())
            ->where('reactable_type', Post::class)
            ->where('reactable_id', $post->id)
            ->first();

        if($existingReaction){
            // Si la reacción es del mismo tipo, eliminar (toggle off)
            if($existingReaction->type === $type){
                $existingReaction->delete();
                return back()->with('status', 'Reacción eliminada');
            } else {
                // Si es diferente tipo, actualizar
                $existingReaction->update(['type' => $type]);
                return back()->with('status', 'Reacción actualizada');
            }
        } else {
            // Crear nueva reacción
            Reaction::create([
                'user_id' => auth()->id(),
                'reactable_type' => Post::class,
                'reactable_id' => $post->id,
                'type' => $type,
            ]);
            return back()->with('status', 'Reacción añadida');
        }
        
        // Nota: back() automáticamente redirige al origen (index o show)
    }

    /**
     * Remove reaction
     */
    public function destroy(Reaction $reaction)
    {
        abort_unless(
            auth()->id() === $reaction->user_id || auth()->user()->role === 'admin',
            403
        );
        
        $reaction->delete();
        
        return back()->with('status', 'Reacción eliminada');
    }
}
