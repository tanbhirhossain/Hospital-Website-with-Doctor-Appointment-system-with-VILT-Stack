<?php

namespace Modules\FRONTEND\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\WEBSITE_EXTRA\Http\Requests\StoreContactMessageRequest;
use Modules\WEBSITE_EXTRA\Models\ContactMessage;
use Modules\WEBSITE_EXTRA\Services\ContactMessageService;
use Modules\SITE_SETTINGS\Services\FrontendSiteSettingsService;

class FrontOtherController extends Controller
{
    public function __construct(
        private readonly FrontendSiteSettingsService $siteSettingsService,
    ) {}

    public function about(): Response
    {
        $shared = $this->siteSettingsService->sharedFrontendData();
        $aboutData = $this->siteSettingsService->aboutPageData();

        return Inertia::render('FRONTEND::About', array_merge($shared, $aboutData));
    }

    public function contact(): Response
    {
        $shared = $this->siteSettingsService->sharedFrontendData();
        $contactData = $this->siteSettingsService->contactPageData();

        return Inertia::render('FRONTEND::Contact', array_merge($shared, $contactData, [
            'contact' => [
                'email' => $contactData['contact_info']['email_primary'] ?? 'info@amzhospitalbd.com',
                'phone' => $contactData['contact_info']['phone_primary'] ?? '+880 184 733 1047',
                'hotline' => $contactData['contact_info']['hotline'] ?? '10699',
                'address' => $contactData['contact_info']['address'] ?? 'Cha-80/3, Shadhinota Sarani, Progati Sarani Road, Uttar Badda, Dhaka-1212',
            ],
        ]));
    }

    public function storeContact(StoreContactMessageRequest $request, ContactMessageService $messages): RedirectResponse
    {
        $message = $messages->submitPublicMessage($request->validated(), [
            'source' => 'contact-page',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        if ($message->mail_status === ContactMessage::MAIL_FAILED) {
            return back()->with('warning', 'Your message was saved, but email delivery failed. Our team can still review it from the database.');
        }

        return back()->with('success', 'Thank you. Your message has been sent and a confirmation email was delivered to you.');
    }

    
}
