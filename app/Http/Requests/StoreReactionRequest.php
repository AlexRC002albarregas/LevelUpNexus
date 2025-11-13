<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReactionRequest extends FormRequest
{
    /**
     * Indica si el usuario está autorizado para registrar reacciones.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Define las reglas de validación para almacenar la reacción.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }
}
