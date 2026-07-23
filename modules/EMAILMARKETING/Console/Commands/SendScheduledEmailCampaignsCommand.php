<?php

namespace Modules\EMAILMARKETING\Console\Commands;

use Illuminate\Console\Command;
use Modules\EMAILMARKETING\Models\EmailCampaign;
use Modules\EMAILMARKETING\Services\EmailCampaignService;

class SendScheduledEmailCampaignsCommand extends Command
{
    protected $signature = 'emailmarketing:send-scheduled';

    protected $description = 'Queue all due scheduled email marketing campaigns';

    public function handle(EmailCampaignService $campaigns): int
    {
        $count = 0;

        EmailCampaign::query()
            ->where('status', EmailCampaign::STATUS_SCHEDULED)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->orderBy('scheduled_at')
            ->get()
            ->each(function (EmailCampaign $campaign) use ($campaigns, &$count): void {
                $campaigns->sendNow($campaign);
                $count++;
            });

        $this->info("Queued {$count} scheduled campaign(s).");

        return self::SUCCESS;
    }
}
