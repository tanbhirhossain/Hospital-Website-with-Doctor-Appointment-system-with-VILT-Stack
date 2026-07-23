<?php

namespace Modules\WEBSITE\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:doctors,slug',
            'specialty' => 'required|string|max:255',
            'qualifications' => 'required|string',
            'designation' => 'required|string|max:255',
            'institute' => 'required|string|max:255',
            'biography' => 'nullable|string',
            'chamber_location' => 'nullable|string|max:255',
            'visit_fee' => 'nullable|numeric|min:0',
            'report_fee' => 'nullable|numeric|min:0',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'mis_code' => 'nullable|string|max:50',
            'skills' => 'nullable|string',
            'awards' => 'nullable|string',
            'serial' => 'nullable|integer',
            'is_active' => 'boolean',
            'is_home_page' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'canonical_url' => 'nullable|url|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'indexable' => 'boolean',
            'timetables' => 'nullable|array',
            'timetables.*.day_of_week' => 'required|integer|between:0,6',
            'timetables.*.start_time' => 'required',
            'timetables.*.end_time' => 'required',
            'timetables.*.location' => 'nullable|string|max:255',
            'timetables.*.max_patient' => 'nullable|integer',
            'timetables.*.is_active' => 'boolean',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'certificates' => 'nullable|array',
            'certificates.*' => 'image|mimes:jpg,jpeg,png,gif,webp,pdf|max:5120',
        ];
    }
}
