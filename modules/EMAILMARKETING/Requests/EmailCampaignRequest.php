<?php

namespace Modules\EMAILMARKETING\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmailCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can($this->isMethod('post') ? 'emailmarketing.create' : 'emailmarketing.edit') === true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['blog_notification', 'health_tip', 'awareness_tip', 'tips_tricks', 'newsletter', 'custom'])],
            'subject' => ['required', 'string', 'max:255'],
            'preheader' => ['nullable', 'string', 'max:255'],
            'email_template_id' => ['required', 'integer', Rule::exists('email_templates', 'id')->whereNull('deleted_at')],
            'recipient_filters' => ['nullable', 'array'],
            'recipient_filters.audience_type' => ['nullable', 'string', 'max:100'],
            'recipient_filters.source' => ['nullable', 'string', 'max:100'],
            'recipient_filters.country' => ['nullable', 'string', 'max:100'],
            'recipient_filters.tags' => ['nullable', 'array'],
            'recipient_filters.tags.*' => ['nullable', 'string', 'max:50'],
            'sender_name' => ['nullable', 'string', 'max:255'],
            'sender_email' => ['nullable', 'email', 'max:255'],
            'reply_to' => ['nullable', 'email', 'max:255'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ];
    }
}
