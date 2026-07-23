<?php

namespace Modules\WEBSITE_EXTRA\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\WEBSITE_EXTRA\Models\ContactMessage;

class ContactMessageCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage) {}

    public function build(): self
    {
        return $this
            ->subject('We received your message - AMZ Hospital')
            ->html($this->htmlBody());
    }

    private function htmlBody(): string
    {
        $message = $this->contactMessage;

        return '<div style="margin:0;padding:28px;background:#f1f5f9;font-family:Arial,sans-serif;color:#0f172a;">'
            .'<div style="max-width:680px;margin:auto;background:#ffffff;border-radius:24px;overflow:hidden;box-shadow:0 18px 50px rgba(15,23,42,.12);">'
            .'<div style="background:linear-gradient(135deg,#1e40af,#0ea5e9,#10b981);padding:32px;color:#ffffff;text-align:center;">'
            .'<p style="margin:0 0 8px;font-size:12px;letter-spacing:.16em;text-transform:uppercase;font-weight:800;">AMZ Hospital</p>'
            .'<h1 style="margin:0;font-size:28px;line-height:1.2;">Thank you, '.e($message->name).'</h1>'
            .'<p style="margin:12px 0 0;color:#e0f2fe;font-size:15px;line-height:1.7;">We have received your contact message and our team will get back to you as soon as possible.</p>'
            .'</div>'
            .'<div style="padding:28px;">'
            .'<p style="font-size:15px;line-height:1.75;color:#334155;margin:0 0 18px;">Here is a copy of your message for your records:</p>'
            .'<div style="padding:18px;border-radius:18px;background:#f8fafc;border:1px solid #e2e8f0;">'
            .'<p style="margin:0 0 10px;font-weight:800;color:#0f172a;">'.e($message->subject).'</p>'
            .'<div style="font-size:15px;line-height:1.75;color:#475569;white-space:pre-line;">'.e($message->message).'</div>'
            .'</div>'
            .'<p style="margin:22px 0 0;font-size:14px;line-height:1.7;color:#64748b;">For urgent support, please call our emergency hotline <strong style="color:#1e40af;">10699</strong>.</p>'
            .'</div></div></div>';
    }
}
