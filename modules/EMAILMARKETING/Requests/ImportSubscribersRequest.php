<?php

namespace Modules\EMAILMARKETING\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportSubscribersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('emailmarketing.create') === true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
            'default_source' => ['nullable', 'string', 'max:100'],
            'default_audience_type' => ['nullable', 'string', 'max:100'],
            'default_country' => ['nullable', 'string', 'max:100'],
        ];
    }
}
