<?php

namespace App\Http\Requests\Hall;

use App\Enums\SeatType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'seats' => ['required', 'array', 'min:1'],
            'seats.*.row_number' => ['required', 'integer', 'min:1', 'max:'.$this->integer('rows_count', 40)],
            'seats.*.column_number' => ['required', 'integer', 'min:1', 'max:'.$this->integer('columns_count', 40)],
            'seats.*.seat_type' => ['required', Rule::in(array_column(SeatType::cases(), 'value'))],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $seats = collect($this->input('seats', []));

            $hasDuplicatePosition = $seats
                ->map(fn ($seat) => ($seat['row_number'] ?? null).':'.($seat['column_number'] ?? null))
                ->duplicates()
                ->isNotEmpty();

            if ($hasDuplicatePosition) {
                $validator->errors()->add('seats', 'Место не может быть указано в схеме зала дважды.');
            }
        });
    }
}
