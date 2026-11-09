<?php

namespace App\Http\Requests\Film;

use App\Models\Country;
use App\Models\Film;
use Illuminate\Validation\Rule;

class UpdateFilmRequest extends StoreFilmRequest
{
    /**
     * The country list stays open for the film's current value, so films
     * seeded before the list existed (e.g. "США/Великобритания") stay editable.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $film = Film::find($this->route('film'));

        $rules['country'] = [
            'required',
            'string',
            'max:255',
            Rule::in([
                ...Country::query()->pluck('name')->all(),
                $film?->country,
            ]),
        ];

        return $rules;
    }
}
