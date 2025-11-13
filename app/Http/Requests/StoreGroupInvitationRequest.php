<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupInvitationRequest extends FormRequest
{
    /**
     * Indica si el usuario puede enviar la invitación.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Define las reglas de validación que se aplican a la invitación.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Establece los mensajes de error personalizados.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Debes proporcionar un nombre de usuario o correo electrónico',
            'username.max' => 'El nombre de usuario no puede exceder los 255 caracteres',
        ];
    }
}
