<?php

namespace Modules\WEBSITE\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class COERequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('coe') ? $this->route('coe')->id : 'NULL';
        
        return [
            'name'             => ['required', 'string', 'max:255'],
            'slug'             => ['nullable', 'string', 'max:255', "unique:center_of_excellences,slug,{$id}"],
            'description'      => ['nullable', 'string'],
            'icon'             => ['nullable', 'string', 'max:255'],
            'style'            => ['nullable', 'string'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'canonical_url'    => ['nullable', 'url', 'max:255'],
            'og_title'         => ['nullable', 'string', 'max:255'],
            'og_description'   => ['nullable', 'string'],
            'indexable'        => ['required', 'boolean'],
        ];
    }
}