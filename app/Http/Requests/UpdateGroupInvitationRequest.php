<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupInvitationRequest extends FormRequest
{
    /**
     * Indica si el usuario tiene permisos para actualizar la invitación.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Define las reglas de validación para la actualización.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }
}
