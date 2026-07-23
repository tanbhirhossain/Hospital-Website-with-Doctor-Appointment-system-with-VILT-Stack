<?php

namespace Modules\EMAILMARKETING\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\EMAILMARKETING\Jobs\SendEmailCampaignJob;
use Modules\EMAILMARKETING\Mail\MarketingCampaignMail;
use Modules\EMAILMARKETING\Models\EmailCampaign;
use Modules\EMAILMARKETING\Models\EmailCampaignRecipient;
use Modules\EMAILMARKETING\Models\EmailMarketingEvent;
use Modules\EMAILMARKETING\Models\EmailTemplate;
use Modules\EMAILMARKETING\Models\Newsletter;

class EmailCampaignService
{
    public function __construct(private readonly EmailTemplateRenderer $renderer) {}

    public function paginateCampaigns(array $filters = [], int $perPage = 8, string $pageName = 'campaigns_page'): LengthAwarePaginator
    {
        return EmailCampaign::query()
            ->with(['template:id,name,category', 'creator:id,name'])
            ->withCount('recipients')
            ->search($filters['campaign_search'] ?? null)
            ->status($filters['campaign_status'] ?? null)
            ->type($filters['campaign_type'] ?? null)
            ->latest('id')
            ->paginate($perPage, ['*'], $pageName)
            ->withQueryString();
    }

    public function paginateTemplates(array $filters = [], int $perPage = 8, string $pageName = 'templates_page'): LengthAwarePaginator
    {
        return EmailTemplate::query()
            ->with(['creator:id,name'])
            ->withCount('campaigns')
            ->search($filters['template_search'] ?? null)
            ->when(($filters['template_category'] ?? '') !== '', fn (Builder $query) => $query->where('category', $filters['template_category']))
            ->when(($filters['template_status'] ?? '') !== '', fn (Builder $query) => $query->where('status', $filters['template_status']))
            ->latest('id')
            ->paginate($perPage, ['*'], $pageName)
            ->withQueryString();
    }

    public function createTemplate(array $data, int $userId): EmailTemplate
    {
        $data['slug'] = $this->uniqueTemplateSlug($data['slug'] ?? $data['name']);
        $data['builder_json'] = $this->renderer->normalizeBlocks($data['builder_json'] ?? []);
        $data['created_by'] = $userId;
        $data['updated_by'] = null;

        return EmailTemplate::create($data);
    }

    public function updateTemplate(EmailTemplate $template, array $data, int $userId): EmailTemplate
    {
        $data['slug'] = $this->uniqueTemplateSlug($data['slug'] ?? $data['name'], $template->id);
        $data['builder_json'] = $this->renderer->normalizeBlocks($data['builder_json'] ?? []);
        $data['updated_by'] = $userId;

        $template->update($data);

        return $template->refresh();
    }

    public function duplicateTemplate(EmailTemplate $template, int $userId): EmailTemplate
    {
        $copy = $template->replicate(['slug', 'created_by', 'updated_by']);
        $copy->name = $template->name.' Copy';
        $copy->slug = $this->uniqueTemplateSlug($template->slug.'-copy');
        $copy->status = 'draft';
        $copy->created_by = $userId;
        $copy->updated_by = null;
        $copy->save();

        return $copy;
    }

    public function createCampaign(array $data, int $userId): EmailCampaign
    {
        return DB::transaction(function () use ($data, $userId): EmailCampaign {
            $template = EmailTemplate::findOrFail((int) $data['email_template_id']);
            $campaign = EmailCampaign::create($this->campaignPayload($data, $template, $userId));
            $this->prepareRecipients($campaign);

            return $campaign->refresh()->load('template');
        });
    }

    public function updateCampaign(EmailCampaign $campaign, array $data, int $userId): EmailCampaign
    {
        if (in_array($campaign->status, [EmailCampaign::STATUS_SENDING, EmailCampaign::STATUS_SENT], true)) {
            throw ValidationException::withMessages(['campaign' => 'Sent or sending campaigns cannot be edited. Duplicate the campaign instead.']);
        }

        return DB::transaction(function () use ($campaign, $data, $userId): EmailCampaign {
            $template = EmailTemplate::findOrFail((int) $data['email_template_id']);
            $campaign->update($this->campaignPayload($data, $template, $userId, true));
            $campaign->recipients()->delete();
            $this->prepareRecipients($campaign->refresh());

            return $campaign->refresh()->load('template');
        });
    }

    public function duplicateCampaign(EmailCampaign $campaign, int $userId): EmailCampaign
    {
        return DB::transaction(function () use ($campaign, $userId): EmailCampaign {
            $copy = $campaign->replicate(['status', 'scheduled_at', 'started_at', 'sent_at', 'sent_count', 'failed_count', 'opened_count', 'clicked_count', 'created_by', 'updated_by']);
            $copy->name = $campaign->name.' Copy';
            $copy->status = EmailCampaign::STATUS_DRAFT;
            $copy->scheduled_at = null;
            $copy->started_at = null;
            $copy->sent_at = null;
            $copy->sent_count = 0;
            $copy->failed_count = 0;
            $copy->opened_count = 0;
            $copy->clicked_count = 0;
            $copy->created_by = $userId;
            $copy->updated_by = null;
            $copy->save();
            $this->prepareRecipients($copy);

            return $copy->refresh()->load('template');
        });
    }

    public function prepareRecipients(EmailCampaign $campaign): int
    {
        $filters = $campaign->recipient_filters ?: [];
        $count = 0;

        Newsletter::query()
            ->subscribed()
            ->when(($filters['audience_type'] ?? '') !== '', fn (Builder $query) => $query->where('audience_type', $filters['audience_type']))
            ->when(($filters['source'] ?? '') !== '', fn (Builder $query) => $query->where('source', $filters['source']))
            ->when(($filters['country'] ?? '') !== '', fn (Builder $query) => $query->where('country', $filters['country']))
            ->when(! empty($filters['tags']), function (Builder $query) use ($filters): void {
                foreach ((array) $filters['tags'] as $tag) {
                    if ($tag !== null && $tag !== '') {
                        $query->whereJsonContains('tags', $tag);
                    }
                }
            })
            ->orderBy('id')
            ->chunkById(300, function ($subscribers) use ($campaign, &$count): void {
                foreach ($subscribers as $subscriber) {
                    EmailCampaignRecipient::updateOrCreate(
                        [
                            'email_campaign_id' => $campaign->id,
                            'email' => $subscriber->email,
                        ],
                        [
                            'newsletter_id' => $subscriber->id,
                            'name' => $subscriber->name,
                            'status' => 'pending',
                            'error_message' => null,
                            'tracking_token' => EmailCampaignRecipient::query()
                                ->where('email_campaign_id', $campaign->id)
                                ->where('email', $subscriber->email)
                                ->value('tracking_token') ?: hash('sha256', Str::uuid()->toString().Str::random(40)),
                        ],
                    );
                    $count++;
                }
            });

        $campaign->forceFill(['total_recipients' => $count])->save();

        return $count;
    }

    public function sendNow(EmailCampaign $campaign): void
    {
        if (! $campaign->isSendable()) {
            throw ValidationException::withMessages(['campaign' => 'Only draft, scheduled, cancelled or failed campaigns can be sent.']);
        }

        if ($campaign->recipients()->count() === 0) {
            $this->prepareRecipients($campaign);
        }

        if ($campaign->refresh()->total_recipients === 0) {
            throw ValidationException::withMessages(['campaign' => 'No active subscribers match the selected audience filters.']);
        }

        $campaign->forceFill([
            'status' => EmailCampaign::STATUS_SENDING,
            'scheduled_at' => null,
            'started_at' => now(),
            'sent_at' => null,
            'sent_count' => 0,
            'failed_count' => 0,
        ])->save();

        SendEmailCampaignJob::dispatch($campaign->id);
    }

    public function schedule(EmailCampaign $campaign, ?string $scheduledAt): EmailCampaign
    {
        if (! $scheduledAt) {
            throw ValidationException::withMessages(['scheduled_at' => 'Choose a schedule date and time.']);
        }

        if (in_array($campaign->status, [EmailCampaign::STATUS_SENDING, EmailCampaign::STATUS_SENT], true)) {
            throw ValidationException::withMessages(['campaign' => 'Sent or sending campaigns cannot be scheduled.']);
        }

        $campaign->forceFill([
            'status' => EmailCampaign::STATUS_SCHEDULED,
            'scheduled_at' => $scheduledAt,
        ])->save();

        return $campaign->refresh();
    }

    public function cancel(EmailCampaign $campaign): EmailCampaign
    {
        if ($campaign->status === EmailCampaign::STATUS_SENT) {
            throw ValidationException::withMessages(['campaign' => 'Sent campaigns cannot be cancelled.']);
        }

        $campaign->forceFill([
            'status' => EmailCampaign::STATUS_CANCELLED,
            'scheduled_at' => null,
        ])->save();

        return $campaign->refresh();
    }

    public function sendTemplateTest(EmailTemplate $template, string $email, ?string $name = null): void
    {
        $rendered = $this->renderer->renderTemplate($template, $email, $name);
        Mail::to($email)->send(new MarketingCampaignMail('[TEST] '.$rendered['subject'], $rendered['html']));
    }

    public function sendCampaignTest(EmailCampaign $campaign, string $email, ?string $name = null): void
    {
        $recipient = new EmailCampaignRecipient([
            'name' => $name ?: 'Test Subscriber',
            'email' => $email,
            'tracking_token' => 'preview',
        ]);

        $rendered = $this->renderer->renderCampaign($campaign->loadMissing('template'), $recipient);
        Mail::to($email)->send(new MarketingCampaignMail(
            '[TEST] '.$rendered['subject'],
            $rendered['html'],
            $campaign->sender_email,
            $campaign->sender_name,
            $campaign->reply_to,
        ));
    }

    public function recordEvent(EmailCampaignRecipient $recipient, string $type, array $metadata = [], ?string $ipAddress = null, ?string $userAgent = null): void
    {
        EmailMarketingEvent::create([
            'email_campaign_id' => $recipient->email_campaign_id,
            'email_campaign_recipient_id' => $recipient->id,
            'newsletter_id' => $recipient->newsletter_id,
            'email' => $recipient->email,
            'type' => $type,
            'metadata' => $metadata,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        if ($type === 'open' && ! $recipient->opened_at) {
            $recipient->forceFill(['opened_at' => now()])->save();
            $recipient->campaign()->increment('opened_count');
        }

        if ($type === 'click' && ! $recipient->clicked_at) {
            $recipient->forceFill(['clicked_at' => now()])->save();
            $recipient->campaign()->increment('clicked_count');
        }
    }

    /** @param array<string, mixed> $data @return array<string, mixed> */
    private function campaignPayload(array $data, EmailTemplate $template, int $userId, bool $updating = false): array
    {
        $payload = [
            'name' => $data['name'],
            'type' => $data['type'],
            'subject' => $data['subject'],
            'preheader' => $data['preheader'] ?? null,
            'email_template_id' => $template->id,
            'template_snapshot' => [
                'name' => $template->name,
                'subject' => $template->subject,
                'preheader' => $template->preheader,
                'builder_json' => $template->builder_json,
                'html_content' => $template->html_content,
                'text_content' => $template->text_content,
            ],
            'recipient_filters' => $this->cleanFilters($data['recipient_filters'] ?? []),
            'sender_name' => $data['sender_name'] ?? null,
            'sender_email' => $data['sender_email'] ?? null,
            'reply_to' => $data['reply_to'] ?? null,
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'status' => filled($data['scheduled_at'] ?? null) ? EmailCampaign::STATUS_SCHEDULED : EmailCampaign::STATUS_DRAFT,
        ];

        $payload[$updating ? 'updated_by' : 'created_by'] = $userId;

        return $payload;
    }

    /** @param array<string, mixed> $filters @return array<string, mixed> */
    private function cleanFilters(array $filters): array
    {
        return collect([
            'audience_type' => Arr::get($filters, 'audience_type'),
            'source' => Arr::get($filters, 'source'),
            'country' => Arr::get($filters, 'country'),
            'tags' => collect((array) Arr::get($filters, 'tags', []))->filter()->values()->all(),
        ])->filter(fn ($value): bool => is_array($value) ? $value !== [] : filled($value))->all();
    }

    private function uniqueTemplateSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'email-template';
        $slug = $base;
        $counter = 2;

        while (EmailTemplate::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
