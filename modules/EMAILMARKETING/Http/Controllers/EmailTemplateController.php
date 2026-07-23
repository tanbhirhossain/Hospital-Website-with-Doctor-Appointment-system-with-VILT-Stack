<?php

namespace Modules\EMAILMARKETING\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\EMAILMARKETING\Models\EmailTemplate;
use Modules\EMAILMARKETING\Requests\EmailTemplateRequest;
use Modules\EMAILMARKETING\Requests\SendTestEmailRequest;
use Modules\EMAILMARKETING\Services\EmailCampaignService;

class EmailTemplateController extends Controller
{
    public function __construct(private readonly EmailCampaignService $emails) {}

    public function store(EmailTemplateRequest $request): RedirectResponse
    {
        $this->emails->createTemplate($request->validated(), (int) $request->user()->id);

        return to_route('emailmarketing.index', ['tab' => 'templates'])->with('success', 'Email template created successfully.');
    }

    public function update(EmailTemplateRequest $request, EmailTemplate $template): RedirectResponse
    {
        $this->emails->updateTemplate($template, $request->validated(), (int) $request->user()->id);

        return back()->with('success', 'Email template updated successfully.');
    }

    public function destroy(Request $request, EmailTemplate $template): RedirectResponse
    {
        abort_unless($request->user()?->can('emailmarketing.delete'), 403);
        $template->delete();

        return back()->with('success', 'Email template deleted successfully.');
    }

    public function duplicate(Request $request, EmailTemplate $template): RedirectResponse
    {
        abort_unless($request->user()?->can('emailmarketing.create'), 403);
        $this->emails->duplicateTemplate($template, (int) $request->user()->id);

        return back()->with('success', 'Email template duplicated.');
    }

    public function test(SendTestEmailRequest $request, EmailTemplate $template): RedirectResponse
    {
        $this->emails->sendTemplateTest($template, $request->validated('email'), $request->validated('name'));

        return back()->with('success', 'Test email sent successfully.');
    }
}
