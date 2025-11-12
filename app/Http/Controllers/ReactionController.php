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

        if (!in_array($type, $allowedTypes)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tipo de reacción no válido',
                ], 422);
            }

            return back()->withErrors(['type' => 'Tipo de reacción no válido']);
        }

        // Verificar que el usuario puede ver el post
        if (!auth()->user()->canViewProfile($post->user)) {
            abort(403, 'No tienes permiso para reaccionar a esta publicación');
        }

        $existingReaction = Reaction::where('user_id', auth()->id())
            ->where('reactable_type', Post::class)
            ->where('reactable_id', $post->id)
            ->first();

        $message = '';

        if ($existingReaction) {
            // Si la reacción es del mismo tipo, eliminar (toggle off)
            if ($existingReaction->type === $type) {
                $existingReaction->delete();
                $message = 'Reacción eliminada';
            } else {
                // Si es diferente tipo, actualizar
                $existingReaction->update(['type' => $type]);
                $message = 'Reacción actualizada';
            }
        } else {
            // Crear nueva reacción
            Reaction::create([
                'user_id' => auth()->id(),
                'reactable_type' => Post::class,
                'reactable_id' => $post->id,
                'type' => $type,
            ]);
            $message = 'Reacción añadida';
        }

        $post->load('reactions');
        $userReaction = $post->reactions->where('user_id', auth()->id())->first();
        $reactionCounts = $post->reactions
            ->groupBy('type')
            ->map(function ($group) {
                return $group->count();
            });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'userReaction' => $userReaction?->type,
                'counts' => [
                    'total' => $post->reactions->count(),
                    'byType' => $reactionCounts,
                ],
            ]);
        }

        return back()->with('status', $message);
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
