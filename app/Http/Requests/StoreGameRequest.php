<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
    /**
     * Indica si el usuario está autorizado para enviar la solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Devuelve las reglas de validación aplicables a la creación.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required','string','max:150'],
            'platform' => ['required','in:pc,xbox,playstation,switch,mobile,other'],
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

    /**
     * Define los mensajes personalizados para las validaciones.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título del juego es obligatorio',
            'title.max' => 'El título no puede exceder los 150 caracteres',
            'platform.required' => 'La plataforma es obligatoria',
            'platform.in' => 'La plataforma debe ser: PC, Xbox, PlayStation, Switch, Mobile u Otra',
            'genre.max' => 'El género no puede exceder los 100 caracteres',
            'hours_played.integer' => 'Las horas jugadas deben ser un número entero',
            'hours_played.min' => 'Las horas jugadas no pueden ser negativas',
            'rawg_rating.numeric' => 'La valoración debe ser un número',
            'rawg_rating.min' => 'La valoración mínima es 0',
            'rawg_rating.max' => 'La valoración máxima es 5',
            'released_date.date' => 'La fecha de lanzamiento no es válida',
        ];
    }
}
