<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReactionRequest extends FormRequest
{
    /**
     * Determina si el usuario puede actualizar la reacción.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Define las reglas de validación para actualizar la reacción.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }
}
