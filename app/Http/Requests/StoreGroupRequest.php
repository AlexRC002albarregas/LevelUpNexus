<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:groups,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
        ];
    }

    /**
     * Get custom error messages for validator.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del grupo es obligatorio',
            'name.max' => 'El nombre del grupo no puede exceder los 255 caracteres',
            'name.unique' => 'Ya existe un grupo con este nombre',
            'description.max' => 'La descripción no puede exceder los 1000 caracteres',
            'avatar.image' => 'El archivo debe ser una imagen',
            'avatar.mimes' => 'El avatar debe ser de tipo: jpeg, jpg, png, gif o webp',
            'avatar.max' => 'El avatar no puede ser mayor de 2MB',
        ];
    }
}
