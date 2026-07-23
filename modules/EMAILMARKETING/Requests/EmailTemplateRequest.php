<?php

namespace Modules\EMAILMARKETING\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can($this->isMethod('post') ? 'emailmarketing.create' : 'emailmarketing.edit') === true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $templateId = $this->route('template')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('email_templates', 'slug')->ignore($templateId)],
            'category' => ['required', Rule::in(['blog_notification', 'health_tip', 'awareness_tip', 'tips_tricks', 'newsletter', 'custom'])],
            'subject' => ['required', 'string', 'max:255'],
            'preheader' => ['nullable', 'string', 'max:255'],
            'builder_json' => ['nullable', 'array'],
            'html_content' => ['nullable', 'string'],
            'text_content' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'active', 'archived'])],
        ];
    }
}
