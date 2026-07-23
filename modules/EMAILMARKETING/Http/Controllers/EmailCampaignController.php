<?php

namespace Modules\EMAILMARKETING\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\EMAILMARKETING\Models\EmailCampaign;
use Modules\EMAILMARKETING\Requests\EmailCampaignRequest;
use Modules\EMAILMARKETING\Requests\SendTestEmailRequest;
use Modules\EMAILMARKETING\Services\EmailCampaignService;

class EmailCampaignController extends Controller
{
    public function __construct(private readonly EmailCampaignService $emails) {}

    public function store(EmailCampaignRequest $request): RedirectResponse
    {
        $campaign = $this->emails->createCampaign($request->validated(), (int) $request->user()->id);

        return to_route('emailmarketing.index', ['tab' => 'campaigns'])->with('success', "Campaign '{$campaign->name}' created.");
    }

    public function update(EmailCampaignRequest $request, EmailCampaign $campaign): RedirectResponse
    {
        $this->emails->updateCampaign($campaign, $request->validated(), (int) $request->user()->id);

        return back()->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Request $request, EmailCampaign $campaign): RedirectResponse
    {
        abort_unless($request->user()?->can('emailmarketing.delete'), 403);
        $campaign->delete();

        return back()->with('success', 'Campaign deleted successfully.');
    }

    public function send(Request $request, EmailCampaign $campaign): RedirectResponse
    {
        abort_unless($request->user()?->can('emailmarketing.send'), 403);
        $this->emails->sendNow($campaign);

        return back()->with('success', 'Campaign queued for sending. Keep your queue worker running.');
    }

    public function schedule(Request $request, EmailCampaign $campaign): RedirectResponse
    {
        abort_unless($request->user()?->can('emailmarketing.send'), 403);
        $request->validate(['scheduled_at' => ['required', 'date', 'after:now']]);
        $this->emails->schedule($campaign, $request->string('scheduled_at')->toString());

        return back()->with('success', 'Campaign scheduled successfully.');
    }

    public function duplicate(Request $request, EmailCampaign $campaign): RedirectResponse
    {
        abort_unless($request->user()?->can('emailmarketing.create'), 403);
        $this->emails->duplicateCampaign($campaign, (int) $request->user()->id);

        return back()->with('success', 'Campaign duplicated.');
    }

    public function cancel(Request $request, EmailCampaign $campaign): RedirectResponse
    {
        abort_unless($request->user()?->can('emailmarketing.send'), 403);
        $this->emails->cancel($campaign);

        return back()->with('success', 'Campaign cancelled.');
    }

    public function test(SendTestEmailRequest $request, EmailCampaign $campaign): RedirectResponse
    {
        $this->emails->sendCampaignTest($campaign, $request->validated('email'), $request->validated('name'));

        return back()->with('success', 'Test email sent successfully.');
    }
}
