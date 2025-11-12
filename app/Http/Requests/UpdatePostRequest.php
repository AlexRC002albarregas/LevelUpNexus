<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->id() === $this->post->user_id || auth()->user()->role === 'admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:5000'],
            'group_id' => ['nullable', 'exists:groups,id'],
            'game_id' => ['nullable', 'exists:games,id'],
            'rawg_game_id' => ['nullable', 'integer'],
            'game_title' => ['nullable', 'string', 'max:255'],
            'game_image' => ['nullable', 'string', 'max:500'],
            'game_platform' => ['nullable', 'string', 'max:255'],
            'visibility' => ['nullable', 'in:public,private,group'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'], // 5MB máximo
        ];
    }
}
