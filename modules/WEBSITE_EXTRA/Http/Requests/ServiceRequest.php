<?php

namespace Modules\WEBSITE_EXTRA\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // For resource routes the parameter name is "service" (singular of services)
        $serviceId = $this->route('service') ?? $this->route('id');

        return [
            'title'              => 'required|string|max:255',
            'slug'               => ['nullable', 'string', 'max:255', Rule::unique('services', 'slug')->ignore($serviceId)],
            'icon'               => 'nullable|string|max:100',
            'short_description'  => 'nullable|string|max:500',
            'description'        => 'nullable|string',
            'price'              => 'nullable|numeric|min:0',
            'duration_minutes'   => 'nullable|integer|min:1',
            'sort_order'         => 'nullable|integer',
            'is_active'          => 'boolean',
            'is_featured'        => 'boolean',
            'meta_title'         => 'nullable|string|max:255',
            'meta_description'   => 'nullable|string|max:500',
            'indexable'          => 'boolean',
            'thumbnail'          => 'nullable|image|max:4096',
            'banner'             => 'nullable|image|max:8192',

            // Multiple gallery images
            'gallery'            => 'nullable|array',
            'gallery.*'          => 'image|max:4096',

            // IDs of gallery images to remove on update
            'remove_gallery'     => 'nullable|array',
            'remove_gallery.*'   => 'integer|exists:media,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'   => 'The service title is required.',
            'gallery.*.image'  => 'Each gallery file must be a valid image.',
            'gallery.*.max'    => 'Each gallery image must not exceed 4 MB.',
            'thumbnail.max'    => 'The thumbnail must not exceed 4 MB.',
        ];
    }
}
