<?php

namespace Modules\GALLERY\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGalleryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can($this->isMethod('post') ? 'gallery.create' : 'gallery.edit') === true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $itemId = $this->route('gallery_item')?->id;

        return [
            'category_id' => ['required', 'integer', Rule::exists('gallery_categories', 'id')->where('is_active', true)],
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('gallery_items', 'slug')->ignore($itemId)],
            'description' => ['nullable', 'string', 'max:3000'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'image' => [$this->isMethod('post') ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
            'is_published' => ['required', 'boolean'],
            'is_featured' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
