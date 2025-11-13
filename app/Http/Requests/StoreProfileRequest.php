<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
{
    /**
     * Indica si cualquier usuario puede crear un perfil.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Establece las reglas de validación para almacenar el perfil.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nick' => ['required','string','max:50','unique:profiles,nick'],
            'avatar' => ['nullable','url'],
            'platform' => ['required','in:pc,xbox,playstation,switch,mobile,other'],
            'favorite_games' => ['nullable','array'],
            'favorite_games.*' => ['string','max:100'],
            'hours_played' => ['nullable','integer','min:0'],
            'achievements' => ['nullable','array'],
            'achievements.*' => ['string','max:100'],
            'bio' => ['nullable','string','max:500'],
        ];
    }
}
