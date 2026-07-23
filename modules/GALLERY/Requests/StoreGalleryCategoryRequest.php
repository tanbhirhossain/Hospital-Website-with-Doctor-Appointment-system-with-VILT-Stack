<?php

namespace Modules\GALLERY\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGalleryCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can($this->isMethod('post') ? 'gallery.create' : 'gallery.edit') === true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $categoryId = $this->route('gallery_category')?->id;

        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('gallery_categories', 'name')->ignore($categoryId)],
            'slug' => ['nullable', 'string', 'max:140', Rule::unique('gallery_categories', 'slug')->ignore($categoryId)],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
