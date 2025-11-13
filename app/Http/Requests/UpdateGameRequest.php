<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGameRequest extends FormRequest
{
    /**
     * Indica si se permite actualizar los datos del juego.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Establece las reglas de validación para modificar el juego.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes','string','max:150'],
            'platform' => ['sometimes','in:pc,xbox,playstation,switch,mobile,other'],
            'genre' => ['nullable','string','max:100'],
            'hours_played' => ['nullable','integer','min:0'],
            'is_favorite' => ['boolean'],
            'rawg_id' => ['nullable','integer'],
            'rawg_image' => ['nullable','string','max:500'],
            'rawg_rating' => ['nullable','numeric','min:0','max:5'],
            'released_date' => ['nullable','date'],
            'rawg_slug' => ['nullable','string','max:200'],
        ];
    }
}
