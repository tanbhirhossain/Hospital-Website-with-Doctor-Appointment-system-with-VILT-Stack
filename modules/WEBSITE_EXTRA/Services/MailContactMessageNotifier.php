<?php

namespace Modules\WEBSITE_EXTRA\Services;

use Illuminate\Support\Facades\Mail;
use Modules\WEBSITE_EXTRA\Interfaces\ContactMessageNotifierInterface;
use Modules\WEBSITE_EXTRA\Mail\ContactMessageAdminMail;
use Modules\WEBSITE_EXTRA\Mail\ContactMessageCustomerMail;
use Modules\WEBSITE_EXTRA\Models\ContactMessage;

class MailContactMessageNotifier implements ContactMessageNotifierInterface
{
    public function notifyAdmin(ContactMessage $message): void
    {
        Mail::to(config('mail.contact_to', config('mail.from.address')))
            ->send(new ContactMessageAdminMail($message));
    }

    public function notifyCustomer(ContactMessage $message): void
    {
        Mail::to($message->email, $message->name)
            ->send(new ContactMessageCustomerMail($message));
    }
}
