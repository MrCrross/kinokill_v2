<?php

namespace App\Http\Requests\Film;

use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFilmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'producer' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255', Rule::in(Country::query()->pluck('name')->all())],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'age_limit' => ['required', 'integer', Rule::in(config('films.age_ratings'))],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'description' => ['required', 'string'],
            'genre_ids' => ['array'],
            'genre_ids.*' => ['integer', 'exists:genres,id'],
            'poster' => ['nullable', 'image', 'max:4096'],
        ];
    }
}
