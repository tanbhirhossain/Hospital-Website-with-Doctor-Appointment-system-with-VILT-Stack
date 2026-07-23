<?php

namespace Modules\WEBSITE\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorTimetableRequest extends FormRequest
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
            'doctor_id' => ['required', 'number', 'exists:doctors,id'],
            'day_of_week' => ['required','number', 'max_digits:1', 'digits_between:0,6'],
            'start_time' => ['required', 'date_form:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:20'],
            
        ];
    }
}