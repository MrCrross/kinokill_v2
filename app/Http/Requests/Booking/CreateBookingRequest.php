<?php

namespace App\Http\Requests\Booking;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateBookingRequest extends FormRequest
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
        $requiresGuestDetails = ! $this->user();

        return [
            'seat_ids' => ['required', 'array', 'min:1', 'max:10'],
            'seat_ids.*' => ['integer', 'distinct', 'exists:seats,id'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'guest_name' => [Rule::requiredIf($requiresGuestDetails), 'nullable', 'string', 'max:255'],
            'guest_email' => [Rule::requiredIf($requiresGuestDetails), 'nullable', 'email', 'max:255'],
            'guest_phone' => ['nullable', 'string', 'max:32'],
        ];
    }
}
