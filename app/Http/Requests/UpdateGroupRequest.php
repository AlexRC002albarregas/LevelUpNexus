<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
{
    /**
     * Verifica si el usuario puede actualizar los datos del grupo.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->id() === $this->group->owner_id || auth()->user()->role === 'admin');
    }

    /**
     * Establece las reglas de validación para la actualización del grupo.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:groups,name,' . $this->group->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
        ];
    }
}
