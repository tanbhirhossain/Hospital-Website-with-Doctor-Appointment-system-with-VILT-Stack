<?php

namespace Modules\WEBSITE\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class COEStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add your own authorization logic
    }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:255'],
            'slug'              => ['nullable', 'string', 'max:255', Rule::unique('center_of_excellences', 'slug')],
            'description'       => ['nullable', 'string'],
            'icon'              => ['nullable', 'string', 'max:255'],
            'style'             => ['nullable', 'json'],
            'meta_title'        => ['nullable', 'string', 'max:60'],
            'meta_description'  => ['nullable', 'string', 'max:160'],
            'canonical_url'     => ['nullable', 'string', 'max:255', 'url'],
            'og_title'          => ['nullable', 'string', 'max:255'],
            'og_description'    => ['nullable', 'string', 'max:255'],
            'indexable'         => ['nullable', 'boolean'],
            'banner_image' => ['nullable', 'image', 'max:5120'], // 5MB
            'thumb_image'  => ['nullable', 'image', 'max:2048'], // 2MB
            'gallery'      => ['nullable', 'array', 'max:10'],
            'gallery.*'    => ['image', 'max:5120'],
            'color'        => ['nullable', 'string', 'max:7'], // HEX color code
            'text_color'   => ['nullable', 'string', 'max:7'], //
            'bg_color'     => ['nullable', 'string', 'max:7'], //
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name is required.',
            'slug.unique'   => 'This slug is already taken.',
        ];
    }
}