<?php

namespace App\Http\Requests\Hall;

use Illuminate\Foundation\Http\FormRequest;

class StoreHallRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:halls,name'],
            'rows_count' => ['required', 'integer', 'min:1', 'max:40'],
            'columns_count' => ['required', 'integer', 'min:1', 'max:40'],
            'price_standard' => ['required', 'numeric', 'min:0'],
            'price_vip' => ['required', 'numeric', 'min:0'],
        ];
    }
}
