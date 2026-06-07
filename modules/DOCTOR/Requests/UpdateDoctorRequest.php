<?php

namespace Modules\DOCTOR\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // রাউট থেকে কারেন্ট ডক্টর অবজেক্ট বা আইডি নেওয়া হচ্ছে স্লাগ ইগনোর করার জন্য
        $doctor = $this->route('doctor'); 
        $doctorId = is_object($doctor) ? $doctor->id : $doctor;

        return [
            // 'sometimes' দেওয়ার কারণে কোনো ফিল্ড না পাঠালে এরর দেবে না, কিন্তু পাঠালে অবশ্যই রুলস মানতে হবে
            'department_id'    => ['sometimes', 'integer', 'exists:departments,id'],
            'name'             => ['sometimes', 'string', 'min:3', 'max:255'],
            
            // বর্তমান ডক্টরের আইডি ইগনোর করা হয়েছে যাতে ইউনিক এরর না আসে
            'slug'             => ['sometimes', 'string', 'max:255', 'unique:doctors,slug,' . $doctorId], 
            
            'specialty'        => ['sometimes', 'string', 'min:3', 'max:255'],
            'qualifications'   => ['sometimes', 'string', 'min:3'],
            'designation'      => ['sometimes', 'string', 'min:3', 'max:255'],
            'institute'        => ['sometimes', 'string', 'min:3', 'max:255'],
            'biography'        => ['nullable', 'string'],
            'chamber_location' => ['nullable', 'string', 'max:255'],
            
            'visit_fee'        => ['nullable', 'numeric', 'min:0'],
            'report_fee'       => ['nullable', 'numeric', 'min:0'],
            
            'phone'            => ['nullable', 'string', 'max:20'],
            'email'            => ['nullable', 'string', 'email', 'max:255'],
            'mis_code'         => ['nullable', 'string', 'max:50'],
            'skills'           => ['nullable', 'string'],
            'awards'           => ['nullable', 'string'],
            'serial'           => ['nullable', 'integer', 'min:0'],
            
            'is_active'        => ['boolean'],
            'is_home_page'     => ['boolean'],
            'is_featured'      => ['boolean'],

            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'canonical_url'    => ['nullable', 'url', 'max:255'],
            'og_title'         => ['nullable', 'string', 'max:255'],
            'og_description'   => ['nullable', 'string'],
            'indexable'        => ['boolean'],

            // টাইমটেবিল আপডেট ভ্যালিডেশন
            'timetables'               => ['nullable', 'array'],
            'timetables.*.day_of_week' => ['required_with:timetables', 'integer', 'between:0,6'],
            'timetables.*.start_time'  => ['required_with:timetables', 'date_format:H:i'],
            'timetables.*.end_time'    => ['required_with:timetables', 'date_format:H:i', 'after:timetables.*.start_time'],
            'timetables.*.location'    => ['nullable', 'string', 'max:255'],
            'timetables.*.max_patient' => ['nullable', 'integer', 'min:1'],
            'timetables.*.is_active'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'timetables.*.day_of_week.required_with' => 'Each timetable must have a day specified.',
            'timetables.*.day_of_week.between'       => 'The day must be between 0 and 6.',
            'timetables.*.start_time.required_with' => 'Start time is required for each timetable row.',
            'timetables.*.start_time.date_format'   => 'Start time must be in HH:MM format.',
            'timetables.*.end_time.required_with'   => 'End time is required for each timetable row.',
            'timetables.*.end_time.date_format'     => 'End time must be in HH:MM format.',
            'timetables.*.end_time.after'           => 'The end time must be after the start time.',
        ];
    }
}