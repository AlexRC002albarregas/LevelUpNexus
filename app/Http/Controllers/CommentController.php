<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request)
    {
        $post = Post::findOrFail($request->post_id);
        
        // Verificar que el usuario puede ver el post
        if(!auth()->user()->canViewProfile($post->user)){
            abort(403, 'No tienes permiso para comentar en esta publicación');
        }
        
        $comment = Comment::create([
            'user_id' => auth()->id(),
            'post_id' => $request->post_id,
            'content' => $request->content,
        ]);

        // Redirigir al index si viene del feed, o al show si viene del detalle
        $redirectTo = $request->has('from_index') ? route('posts.index') : route('posts.show', $post);
        
        return redirect($redirectTo)
            ->with('status', 'Comentario publicado correctamente');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $comment->update([
            'content' => $request->content,
        ]);

        return redirect()
            ->route('posts.show', $comment->post)
            ->with('status', 'Comentario actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        // Verificar autorización: solo el autor del comentario o el autor del post
        $post = $comment->post;
        abort_unless(
            auth()->id() === $comment->user_id || auth()->id() === $post->user_id,
            403,
            'No tienes permiso para eliminar este comentario'
        );
        
        $comment->delete();

        // Redirigir al index si viene del feed, o al show si viene del detalle
        $redirectTo = request()->has('from_index') ? route('posts.index') : route('posts.show', $post);

        return redirect($redirectTo)
            ->with('status', 'Comentario eliminado correctamente');
    }
}
