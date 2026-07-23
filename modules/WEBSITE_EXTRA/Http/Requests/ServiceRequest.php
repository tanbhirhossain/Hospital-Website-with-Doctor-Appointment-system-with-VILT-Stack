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

    /**
     * Fix FormData quirks before validation runs.
     *
     * When submitting with forceFormData: true from Inertia/Vue:
     *   - Booleans arrive as strings "true"/"false" → convert to 1/0
     *   - Empty numeric fields arrive as "" → convert to null
     *   - Checkbox unchecked fields may be missing entirely
     */
    protected function prepareForValidation(): void
    {
        // --- Fix booleans (FormData sends "true"/"false" as strings) ---
        $booleanFields = ['is_active', 'is_featured', 'indexable'];
        foreach ($booleanFields as $field) {
            $value = $this->input($field);
            if ($value === 'true' || $value === true || $value === '1' || $value === 1) {
                $this->merge([$field => true]);
            } elseif ($value === 'false' || $value === false || $value === '0' || $value === 0) {
                $this->merge([$field => false]);
            } elseif ($value === null || $value === '') {
                // Checkbox was unchecked and not sent — default to false
                $this->merge([$field => false]);
            }
        }

        // --- Fix nullable numeric/string fields: convert "" to null ---
        $nullableFields = [
            'icon', 'short_description', 'description', 'price',
            'duration_minutes', 'slug', 'meta_title', 'meta_description',
            'sort_order',
        ];
        foreach ($nullableFields as $field) {
            if ($this->has($field) && $this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }
    }

    public function rules(): array
    {
        // For resource routes the parameter name is "service" (singular of "services")
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

            // Single image uploads
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
            'title.required'    => 'The service title is required.',
            'gallery.*.image'   => 'Each gallery file must be a valid image.',
            'gallery.*.max'     => 'Each gallery image must not exceed 4 MB.',
            'thumbnail.image'   => 'Thumbnail must be a valid image file.',
            'thumbnail.max'     => 'The thumbnail must not exceed 4 MB.',
            'banner.image'      => 'Banner must be a valid image file.',
            'banner.max'        => 'The banner must not exceed 8 MB.',
        ];
    }
}
