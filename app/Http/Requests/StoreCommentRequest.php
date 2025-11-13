<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Indica si el usuario está autorizado para realizar la petición.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Devuelve las reglas de validación aplicables a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:1000'],
            'post_id' => ['required', 'exists:posts,id'],
        ];
    }

    /**
     * Define los mensajes personalizados para los errores de validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'content.required' => 'El contenido del comentario es obligatorio',
            'content.max' => 'El comentario no puede exceder los 1000 caracteres',
            'post_id.required' => 'Debes seleccionar una publicación',
            'post_id.exists' => 'La publicación seleccionada no existe',
        ];
    }
}
