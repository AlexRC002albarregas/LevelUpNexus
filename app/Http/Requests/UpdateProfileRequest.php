<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Indica si se permite modificar el perfil.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Establece las reglas de validación para actualizar el perfil.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nick' => ['sometimes','string','max:50','unique:profiles,nick,'.($this->profile->id ?? 'NULL')],
            'avatar' => ['nullable','url'],
            'platform' => ['sometimes','in:pc,xbox,playstation,switch,mobile,other'],
            'favorite_games' => ['nullable','array'],
            'favorite_games.*' => ['string','max:100'],
            'hours_played' => ['nullable','integer','min:0'],
            'achievements' => ['nullable','array'],
            'achievements.*' => ['string','max:100'],
            'bio' => ['nullable','string','max:500'],
        ];
    }
}
