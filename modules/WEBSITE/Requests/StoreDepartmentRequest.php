<?php

namespace Modules\WEBSITE\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreDepartmentRequest extends FormRequest
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
        $departmentId = $this->route('department')?->id;

        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('departments', 'slug')->ignore($departmentId)],
            'shortDesc' => ['nullable', 'string'],
            'descriptions' => ['nullable', 'string'],
            'text_icon' => ['nullable', 'string', 'max:255'],
            'banner_image' => ['nullable', File::image()->max(3 * 1024)],
            'image' => ['nullable', File::image()->max(3 * 1024)],
            'icon_image' => ['nullable', File::image()->max(1024)],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('departments', 'id')->whereNull('deleted_at'),
                Rule::notIn(array_filter([$departmentId])),
            ],
        ];
    }
}
