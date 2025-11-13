<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    /**
     * Determina si la persona autenticada puede modificar el comentario.
     */
    public function authorize(): bool
    {
        // Solo el autor del comentario puede editarlo
        return auth()->check() && auth()->id() === $this->comment->user_id;
    }

    /**
     * Devuelve las reglas de validación para actualizar el comentario.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:1000'],
        ];
    }
}
