<?php

namespace Modules\DOCTOR\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
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
            // ==========================================
            // ১. ডক্টরের নিজস্ব ফিল্ডস (Core Relationships & Details)
            // ==========================================
            'department_id'    => ['required', 'integer', 'exists:departments,id'],
            'name'             => ['required', 'string', 'min:3', 'max:255'],
            'slug'             => ['nullable', 'string', 'max:255', 'unique:doctors,slug'], // সার্ভিস লেভেলে অটো-স্লাগ ব্যাকআপ আছে, তাই nullable করা যায়
            'specialty'        => ['required', 'string', 'min:3', 'max:255'],
            'qualifications'   => ['required', 'string', 'min:3'],
            'designation'      => ['required', 'string', 'min:3', 'max:255'],
            'institute'        => ['required', 'string', 'min:3', 'max:255'],
            'biography'        => ['nullable', 'string'],
            'chamber_location' => ['nullable', 'string', 'max:255'],
            
            // Financials
            'visit_fee'        => ['nullable', 'numeric', 'min:0'],
            'report_fee'       => ['nullable', 'numeric', 'min:0'],
            
            // Communications & Administration
            'phone'            => ['nullable', 'string', 'max:20'],
            'email'            => ['nullable', 'string', 'email', 'max:255'],
            'mis_code'         => ['nullable', 'string', 'max:50'],
            'skills'           => ['nullable', 'string'],
            'awards'           => ['nullable', 'string'],
            'serial'           => ['nullable', 'integer', 'min:0'],
            
            // Status Toggles
            'is_active'        => ['boolean'],
            'is_home_page'     => ['boolean'],
            'is_featured'      => ['boolean'],

            // SEO Fields
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'canonical_url'    => ['nullable', 'url', 'max:255'],
            'og_title'         => ['nullable', 'string', 'max:255'],
            'og_description'   => ['nullable', 'string'],
            'indexable'        => ['boolean'],

            // ==========================================
            // ২. টাইমটেবিল অ্যারে ফিল্ডস (Nested Array Validation)
            // ==========================================
            'timetables'               => ['nullable', 'array'], // ডাক্তার চাইলে কোনো শিডিউল ছাড়াও আপাতত ক্রিয়েট হতে পারে
            
            // অ্যারের প্রতিটি উপাদানের ভেতরের ফিল্ডগুলোর ভ্যালিডেশন
            'timetables.*.day_of_week' => ['required_with:timetables', 'integer', 'between:0,6'],
            'timetables.*.start_time'  => ['required_with:timetables', 'date_format:H:i'],
            'timetables.*.end_time'    => ['required_with:timetables', 'date_format:H:i', 'after:timetables.*.start_time'],
            'timetables.*.location'    => ['nullable', 'string', 'max:255'],
            'timetables.*.max_patient' => ['nullable', 'integer', 'min:1'],
            'timetables.*.is_active'   => ['nullable', 'boolean'],
        ];
    }

    /**
     * কাস্টম এরর মেসেজ (অ্যারে ফিল্ডগুলোর এরর সহজে বোঝার জন্য)
     */
    public function messages(): array
    {
        return [
            'timetables.*.day_of_week.required_with' => 'Each timetable must have a day specified.',
            'timetables.*.day_of_week.between'       => 'The day must be between 0 (Sunday) and 6 (Saturday).',
            
            'timetables.*.start_time.required_with' => 'Start time is required for each timetable row.',
            'timetables.*.start_time.date_format'   => 'Start time must be in HH:MM format (24-hour style).',
            
            'timetables.*.end_time.required_with'   => 'End time is required for each timetable row.',
            'timetables.*.end_time.date_format'     => 'End time must be in HH:MM format (24-hour style).',
            'timetables.*.end_time.after'           => 'The end time must be a time slot after the start time.',
            
            'timetables.*.max_patient.min'          => 'Maximum patients count must be at least 1.',
        ];
    }
}