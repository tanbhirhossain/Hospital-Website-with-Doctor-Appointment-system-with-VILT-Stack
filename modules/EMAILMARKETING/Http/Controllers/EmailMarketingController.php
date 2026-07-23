<?php

namespace Modules\EMAILMARKETING\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;
use Modules\BLOG\Models\Blog;
use Modules\EMAILMARKETING\Interfaces\NewsletterRepositoryInterface;
use Modules\EMAILMARKETING\Models\EmailCampaign;
use Modules\EMAILMARKETING\Models\EmailTemplate;
use Modules\EMAILMARKETING\Models\Newsletter;
use Modules\EMAILMARKETING\Services\EmailCampaignService;

class EmailMarketingController extends Controller
{
    public function __construct(
        private readonly NewsletterRepositoryInterface $newsletters,
        private readonly EmailCampaignService $campaigns,
    ) {}

    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('emailmarketing.view'), 403);

        $filters = $request->only([
            'subscriber_search',
            'subscriber_status',
            'subscriber_audience_type',
            'subscriber_source',
            'subscriber_country',
            'campaign_search',
            'campaign_status',
            'campaign_type',
            'template_search',
            'template_category',
            'template_status',
        ]);

        $subscriberFilters = [
            'search' => $filters['subscriber_search'] ?? null,
            'status' => $filters['subscriber_status'] ?? null,
            'audience_type' => $filters['subscriber_audience_type'] ?? null,
            'source' => $filters['subscriber_source'] ?? null,
            'country' => $filters['subscriber_country'] ?? null,
        ];

        $subscribers = $this->newsletters
            ->paginate($subscriberFilters, 8, 'subscribers_page')
            ->through(fn (Newsletter $newsletter): array => $this->serializeNewsletter($newsletter));

        $campaigns = $this->campaigns
            ->paginateCampaigns($filters, 8, 'campaigns_page')
            ->through(fn (EmailCampaign $campaign): array => $this->serializeCampaign($campaign));

        $templates = $this->campaigns
            ->paginateTemplates($filters, 8, 'templates_page')
            ->through(fn (EmailTemplate $template): array => $this->serializeTemplate($template));

        return Inertia::render('EMAILMARKETING::Index', [
            'stats' => $this->stats(),
            'subscribers' => $subscribers,
            'campaigns' => $campaigns,
            'templates' => $templates,
            'templateOptions' => EmailTemplate::query()
                ->select(['id', 'name', 'category', 'subject', 'preheader', 'status'])
                ->orderBy('name')
                ->get(),
            'segments' => $this->segments(),
            'recentBlogs' => $this->recentBlogs(),
            'filters' => $filters,
            'campaignTypes' => $this->campaignTypes(),
            'can' => [
                'create' => $request->user()?->can('emailmarketing.create') === true,
                'edit' => $request->user()?->can('emailmarketing.edit') === true,
                'delete' => $request->user()?->can('emailmarketing.delete') === true,
                'send' => $request->user()?->can('emailmarketing.send') === true,
            ],
            'mail' => [
                'default' => config('mail.default'),
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
            ],
        ]);
    }

    /** @return array<string, mixed> */
    private function stats(): array
    {
        return [
            'subscribers_total' => Newsletter::count(),
            'subscribers_active' => Newsletter::subscribed()->count(),
            'subscribers_unsubscribed' => Newsletter::where('status', 'unsubscribed')->count(),
            'templates_total' => EmailTemplate::count(),
            'campaigns_total' => EmailCampaign::count(),
            'campaigns_scheduled' => EmailCampaign::where('status', EmailCampaign::STATUS_SCHEDULED)->count(),
            'campaigns_sent' => EmailCampaign::where('status', EmailCampaign::STATUS_SENT)->count(),
            'emails_sent' => EmailCampaign::sum('sent_count'),
            'open_rate' => $this->percentage((int) EmailCampaign::sum('opened_count'), max((int) EmailCampaign::sum('sent_count'), 1)),
            'click_rate' => $this->percentage((int) EmailCampaign::sum('clicked_count'), max((int) EmailCampaign::sum('sent_count'), 1)),
        ];
    }

    /** @return array<string, array<int, string>> */
    private function segments(): array
    {
        $tags = Newsletter::query()
            ->whereNotNull('tags')
            ->pluck('tags')
            ->flatMap(fn ($tags): array => is_array($tags) ? $tags : (json_decode((string) $tags, true) ?: []))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return [
            'audienceTypes' => Newsletter::query()->whereNotNull('audience_type')->distinct()->orderBy('audience_type')->pluck('audience_type')->values()->all(),
            'sources' => Newsletter::query()->whereNotNull('source')->distinct()->orderBy('source')->pluck('source')->values()->all(),
            'countries' => Newsletter::query()->whereNotNull('country')->distinct()->orderBy('country')->pluck('country')->values()->all(),
            'tags' => $tags,
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function recentBlogs(): array
    {
        if (! class_exists(Blog::class)) {
            return [];
        }

        return Blog::query()
            ->select(['id', 'name', 'slug', 'created_at'])
            ->latest('id')
            ->limit(8)
            ->get()
            ->map(fn (Blog $blog): array => [
                'id' => $blog->id,
                'name' => $blog->name,
                'slug' => $blog->slug,
                'url' => route('blog.show', $blog->slug),
                'created_at' => $blog->created_at?->toFormattedDateString(),
            ])
            ->values()
            ->all();
    }

    /** @return array<string, string> */
    private function campaignTypes(): array
    {
        return [
            'blog_notification' => 'Blog Post Notification',
            'health_tip' => 'Health Tips Email',
            'awareness_tip' => 'Awareness Tips Email',
            'tips_tricks' => 'Tips & Tricks Email',
            'newsletter' => 'Newsletter',
            'custom' => 'Custom Email',
        ];
    }

    /** @return array<string, mixed> */
    private function serializeNewsletter(Newsletter $newsletter): array
    {
        return [
            'id' => $newsletter->id,
            'name' => $newsletter->name,
            'email' => $newsletter->email,
            'phone' => $newsletter->phone,
            'source' => $newsletter->source,
            'isActive' => $newsletter->isActive,
            'status' => $newsletter->status,
            'audience_type' => $newsletter->audience_type,
            'tags' => $newsletter->tags ?: [],
            'country' => $newsletter->country,
            'subscribed_at' => $newsletter->subscribed_at?->toFormattedDateString(),
            'unsubscribed_at' => $newsletter->unsubscribed_at?->toFormattedDateString(),
            'created_at' => $newsletter->created_at?->toFormattedDateString(),
        ];
    }

    /** @return array<string, mixed> */
    private function serializeTemplate(EmailTemplate $template): array
    {
        return [
            'id' => $template->id,
            'name' => $template->name,
            'slug' => $template->slug,
            'category' => $template->category,
            'subject' => $template->subject,
            'preheader' => $template->preheader,
            'builder_json' => $template->builder_json ?: [],
            'html_content' => $template->html_content,
            'text_content' => $template->text_content,
            'status' => $template->status,
            'campaigns_count' => $template->campaigns_count ?? 0,
            'creator' => $template->creator?->name,
            'created_at' => $template->created_at?->toFormattedDateString(),
            'updated_at' => $template->updated_at?->diffForHumans(),
        ];
    }

    /** @return array<string, mixed> */
    private function serializeCampaign(EmailCampaign $campaign): array
    {
        $sent = max((int) $campaign->sent_count, 0);

        return [
            'id' => $campaign->id,
            'name' => $campaign->name,
            'type' => $campaign->type,
            'subject' => $campaign->subject,
            'preheader' => $campaign->preheader,
            'email_template_id' => $campaign->email_template_id,
            'template' => $campaign->template?->only(['id', 'name', 'category']),
            'recipient_filters' => $campaign->recipient_filters ?: [],
            'sender_name' => $campaign->sender_name,
            'sender_email' => $campaign->sender_email,
            'reply_to' => $campaign->reply_to,
            'status' => $campaign->status,
            'scheduled_at' => $campaign->scheduled_at?->format('Y-m-d\TH:i'),
            'scheduled_at_human' => $campaign->scheduled_at?->toDayDateTimeString(),
            'sent_at' => $campaign->sent_at?->toDayDateTimeString(),
            'total_recipients' => $campaign->total_recipients,
            'sent_count' => $campaign->sent_count,
            'failed_count' => $campaign->failed_count,
            'opened_count' => $campaign->opened_count,
            'clicked_count' => $campaign->clicked_count,
            'open_rate' => $sent > 0 ? $this->percentage((int) $campaign->opened_count, $sent) : 0,
            'click_rate' => $sent > 0 ? $this->percentage((int) $campaign->clicked_count, $sent) : 0,
            'recipients_count' => $campaign->recipients_count ?? 0,
            'creator' => $campaign->creator?->name,
            'created_at' => $campaign->created_at?->toFormattedDateString(),
            'updated_at' => $campaign->updated_at?->diffForHumans(),
        ];
    }

    private function percentage(int $value, int $total): float
    {
        if ($total <= 0) {
            return 0;
        }

        return round(($value / $total) * 100, 1);
    }
}
