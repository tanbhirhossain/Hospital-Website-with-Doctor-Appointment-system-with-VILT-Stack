<?php

namespace Modules\EMAILMARKETING\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\EMAILMARKETING\Mail\MarketingCampaignMail;
use Modules\EMAILMARKETING\Models\EmailCampaign;
use Modules\EMAILMARKETING\Models\EmailCampaignRecipient;
use Modules\EMAILMARKETING\Models\EmailMarketingEvent;
use Modules\EMAILMARKETING\Services\EmailTemplateRenderer;
use Throwable;

class SendEmailCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1200;

    public int $tries = 1;

    public function __construct(public int $campaignId) {}

    public function handle(EmailTemplateRenderer $renderer): void
    {
        $campaign = EmailCampaign::with('template')->findOrFail($this->campaignId);

        if ($campaign->status === EmailCampaign::STATUS_CANCELLED) {
            return;
        }

        $campaign->forceFill([
            'status' => EmailCampaign::STATUS_SENDING,
            'started_at' => $campaign->started_at ?: now(),
        ])->save();

        $sent = 0;
        $failed = 0;

        $campaign->recipients()
            ->with('newsletter')
            ->where('status', 'pending')
            ->orderBy('id')
            ->chunkById(100, function ($recipients) use ($campaign, $renderer, &$sent, &$failed): void {
                /** @var EmailCampaignRecipient $recipient */
                foreach ($recipients as $recipient) {
                    if ($campaign->fresh()?->status === EmailCampaign::STATUS_CANCELLED) {
                        return;
                    }

                    $newsletter = $recipient->newsletter;
                    if ($newsletter && (! $newsletter->isActive || $newsletter->status !== 'subscribed')) {
                        $recipient->markSkipped('Subscriber is inactive or unsubscribed.');
                        continue;
                    }

                    try {
                        $rendered = $renderer->renderCampaign($campaign, $recipient);
                        Mail::to($recipient->email)->send(new MarketingCampaignMail(
                            $rendered['subject'],
                            $rendered['html'],
                            $campaign->sender_email,
                            $campaign->sender_name,
                            $campaign->reply_to,
                        ));

                        $recipient->markSent();
                        EmailMarketingEvent::create([
                            'email_campaign_id' => $campaign->id,
                            'email_campaign_recipient_id' => $recipient->id,
                            'newsletter_id' => $recipient->newsletter_id,
                            'email' => $recipient->email,
                            'type' => 'sent',
                        ]);
                        $sent++;
                    } catch (Throwable $exception) {
                        $recipient->markFailed($exception->getMessage());
                        EmailMarketingEvent::create([
                            'email_campaign_id' => $campaign->id,
                            'email_campaign_recipient_id' => $recipient->id,
                            'newsletter_id' => $recipient->newsletter_id,
                            'email' => $recipient->email,
                            'type' => 'failed',
                            'metadata' => ['message' => $exception->getMessage()],
                        ]);
                        $failed++;
                    }
                }
            });

        $remaining = $campaign->recipients()->where('status', 'pending')->count();
        $campaign->forceFill([
            'status' => $remaining === 0 ? EmailCampaign::STATUS_SENT : EmailCampaign::STATUS_FAILED,
            'sent_at' => now(),
            'sent_count' => $campaign->recipients()->where('status', 'sent')->count(),
            'failed_count' => $campaign->recipients()->where('status', 'failed')->count(),
            'total_recipients' => $campaign->recipients()->count(),
        ])->save();
    }
}
