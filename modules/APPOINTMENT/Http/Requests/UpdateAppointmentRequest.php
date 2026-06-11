<?php

namespace Modules\APPOINTMENT\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
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
            'patient_name'         => ['sometimes', 'required', 'string', 'max:255'],
            'patient_email'        => ['nullable', 'email', 'max:255'],
            'patient_phone'        => ['sometimes', 'required', 'string', 'max:20'],
            'appointment_date'     => ['sometimes', 'required', 'date'],
            'start_time'           => ['sometimes', 'required', 'date_format:H:i'],
            'end_time'             => ['sometimes', 'required', 'date_format:H:i', 'after:start_time'],
            'slot_duration'        => ['sometimes', 'required', 'integer', 'min:5', 'max:480'],
            'status'               => ['sometimes', 'required', 'in:pending,confirmed,cancelled,completed,no_show'],
            'notes'                => ['nullable', 'string', 'max:2000'],
            'cancellation_reason'  => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'patient_name.required'   => 'Patient name is required.',
            'patient_phone.required'  => 'Patient phone number is required.',
            'status.in'               => 'Invalid status value.',
            'end_time.after'          => 'End time must be after start time.',
        ];
    }
}
