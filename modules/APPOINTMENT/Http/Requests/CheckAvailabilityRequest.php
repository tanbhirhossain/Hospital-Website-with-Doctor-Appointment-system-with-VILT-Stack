<?php

namespace Modules\APPOINTMENT\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
            'date'      => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'doctor_id.required'           => 'Please select a doctor.',
            'doctor_id.exists'             => 'The selected doctor does not exist.',
            'date.required'                => 'Please select a date.',
            'date.after_or_equal'          => 'The date cannot be in the past.',
        ];
    }
}
