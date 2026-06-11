<?php

namespace Modules\APPOINTMENT\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'doctor_id'        => ['required', 'integer', 'exists:doctors,id'],
            'patient_name'     => ['required', 'string', 'max:255'],
            'patient_email'    => ['nullable', 'email', 'max:255'],
            'patient_phone'    => ['required', 'string', 'max:20'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time'       => ['required', 'date_format:H:i'],
            'end_time'         => ['required', 'date_format:H:i', 'after:start_time'],
            'slot_duration'    => ['required', 'integer', 'min:5', 'max:480'],
            'notes'            => ['nullable', 'string', 'max:2000'],
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
            'patient_name.required'        => 'Patient name is required.',
            'patient_phone.required'       => 'Patient phone number is required.',
            'appointment_date.required'    => 'Please select an appointment date.',
            'appointment_date.after_or_equal' => 'The appointment date cannot be in the past.',
            'start_time.required'          => 'Please select a start time.',
            'start_time.date_format'       => 'Invalid time format.',
            'end_time.required'            => 'End time is required.',
            'end_time.after'               => 'End time must be after start time.',
            'slot_duration.required'       => 'Slot duration is required.',
            'slot_duration.min'            => 'Slot duration must be at least 5 minutes.',
        ];
    }
}
