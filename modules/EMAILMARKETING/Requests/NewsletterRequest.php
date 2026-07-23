<?php

namespace Modules\EMAILMARKETING\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewsletterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can($this->isMethod('post') ? 'emailmarketing.create' : 'emailmarketing.edit') === true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $newsletterId = $this->route('newsletter')?->id;

        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('newsletters', 'email')->ignore($newsletterId)],
            'phone' => ['nullable', 'string', 'max:50'],
            'source' => ['nullable', 'string', 'max:100'],
            'isActive' => ['required', 'boolean'],
            'status' => ['required', Rule::in(['subscribed', 'unsubscribed', 'bounced', 'complained'])],
            'audience_type' => ['nullable', 'string', 'max:100'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:100'],
        ];
    }
}
