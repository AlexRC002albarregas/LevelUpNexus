<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
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
}
