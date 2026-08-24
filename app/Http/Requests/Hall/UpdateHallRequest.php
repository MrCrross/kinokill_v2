<?php

namespace App\Http\Requests\Hall;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHallRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique('halls', 'name')->ignore($this->route('hall'))],
            'price_standard' => ['required', 'numeric', 'min:0'],
            'price_vip' => ['required', 'numeric', 'min:0'],
        ];
    }
}
