<?php

namespace Modules\FRONTEND\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\WEBSITE_EXTRA\Http\Requests\StoreContactMessageRequest;
use Modules\WEBSITE_EXTRA\Models\ContactMessage;
use Modules\WEBSITE_EXTRA\Services\ContactMessageService;

class FrontOtherController extends Controller
{
    public function about(): Response
    {
        return Inertia::render('FRONTEND::About');
    }

    public function contact(): Response
    {
        return Inertia::render('FRONTEND::Contact', [
            'contact' => [
                'email' => 'info@amzhospitalbd.com',
                'phone' => '+880 184 733 1047',
                'hotline' => '10699',
                'address' => 'Cha-80/3, Shadhinota Sarani, Progati Sarani Road, Uttar Badda, Dhaka-1212',
            ],
        ]);
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
