<?php

namespace App\Http\Requests\Session;

use Illuminate\Foundation\Http\FormRequest;

class StoreSessionRequest extends FormRequest
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
            'film_id' => ['required', 'integer', 'exists:films,id'],
            'hall_id' => ['required', 'integer', 'exists:halls,id'],
            'starts_at' => ['required', 'date', 'after:now'],
            'price_standard' => ['nullable', 'numeric', 'min:0'],
            'price_vip' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
