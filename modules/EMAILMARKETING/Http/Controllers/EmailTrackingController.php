<?php

namespace Modules\EMAILMARKETING\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\EMAILMARKETING\Models\EmailCampaignRecipient;
use Modules\EMAILMARKETING\Services\EmailCampaignService;

class EmailTrackingController extends Controller
{
    public function __construct(private readonly EmailCampaignService $emails) {}

    public function open(Request $request, EmailCampaignRecipient $recipient, string $token): Response
    {
        abort_unless(hash_equals((string) $recipient->tracking_token, $token), 403);

        $this->emails->recordEvent($recipient, 'open', [], $request->ip(), (string) $request->userAgent());

        $pixel = base64_decode('R0lGODlhAQABAPAAAP///wAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==');

        return response($pixel, 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    public function click(Request $request, EmailCampaignRecipient $recipient, string $token): RedirectResponse
    {
        abort_unless(hash_equals((string) $recipient->tracking_token, $token), 403);

        $url = (string) $request->query('url', config('app.url'));
        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            $url = config('app.url');
        }

        $this->emails->recordEvent($recipient, 'click', ['url' => $url], $request->ip(), (string) $request->userAgent());

        return redirect()->away($url);
    }
}
